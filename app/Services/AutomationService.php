<?php

namespace App\Services;

use App\Jobs\ExecuteAutomationActionJob;
use App\Models\Automation;
use App\Models\AutomationRun;
use App\Models\User;
use Illuminate\Support\Arr;

class AutomationService
{
    private array $allowedEvents = [
        'billing.payment_failed',
        'billing.trial_ending',
        'support.ticket_idle',
        'webhook.failed_many_times',
        'user.profile_incomplete',
        'subscription.expired',
        'subscription.canceled',
    ];

    private array $allowedActions = [
        'send_email',
        'create_notification',
        'notify_admin',
        'close_ticket',
        'trigger_webhook',
        'log_event',
    ];

    public function trigger(string $eventKey, array $context = []): void
    {
        if (! in_array($eventKey, $this->allowedEvents, true)) {
            return;
        }

        // Omite automatizaciones si el modulo correspondiente esta desactivado.
        $moduleKey = $this->moduleForEvent($eventKey);
        if ($moduleKey && ! app(ModuleService::class)->isEnabled($moduleKey)) {
            return;
        }

        $automations = Automation::query()
            ->where('event_key', $eventKey)
            ->where('is_active', true)
            ->get();

        foreach ($automations as $automation) {
            if (! in_array($automation->action_key, $this->allowedActions, true)) {
                $this->recordRun($automation, 'skipped', $eventKey, [
                    'reason' => 'action_not_allowed',
                ]);

                continue;
            }

            $jobContext = [
                'event_key' => $eventKey,
                'automation_id' => $automation->id,
                'context' => $context,
            ];

            ExecuteAutomationActionJob::dispatch($jobContext);
        }
    }

    public function runAction(Automation $automation, string $eventKey, array $context): void
    {
        $status = 'success';
        $metadata = [];

        try {
            $action = $automation->action_key;
            $config = $automation->config ?? [];

            // Omite la ejecucion si el modulo relacionado esta desactivado.
            $moduleKey = $this->moduleForEvent($eventKey);
            if ($moduleKey && ! app(ModuleService::class)->isEnabled($moduleKey)) {
                $this->recordRun($automation, 'skipped', $eventKey, [
                    'reason' => 'module_disabled',
                    'module' => $moduleKey,
                ]);

                return;
            }

            $handled = match ($action) {
                'send_email' => $this->sendEmail($config, $context),
                'create_notification' => $this->createNotification($config, $context),
                'notify_admin' => $this->notifyAdmin($config, $context),
                'close_ticket' => $this->closeTicket($config, $context),
                'trigger_webhook' => $this->triggerWebhook($config, $context),
                'log_event' => $this->logEvent($config, $context),
                default => false,
            };

            if ($handled === false) {
                $status = 'skipped';
                $metadata = [
                    'reason' => 'unsupported_action',
                ];
            }
        } catch (\Throwable $e) {
            $status = 'failed';
            $metadata = [
                'error' => $e->getMessage(),
            ];
            app(SystemErrorService::class)->logException($e, request(), 'automation', [
                'automation_id' => $automation->id,
                'event_key' => $eventKey,
            ]);
        }

        $this->recordRun($automation, $status, $eventKey, $metadata);
    }

    private function sendEmail(array $config, array $context): bool
    {
        $user = $this->resolveUser($context);
        if (! $user) {
            throw new \RuntimeException('User not resolved');
        }

        $subject = Arr::get($config, 'subject', 'Notificacion del sistema');
        $message = Arr::get($config, 'message', 'Tienes una nueva notificacion.');

        $templateKey = Arr::get($config, 'template_key');
        if ($templateKey) {
            $variables = $this->buildVariables($user, $context);
            $templates = app(TemplateRenderService::class);
            $subject = $templates->renderSubject($templateKey, $variables, $subject) ?? $subject;
            $message = $templates->render($templateKey, $variables, $message) ?? $message;
        }

        $user->notify(new \App\Notifications\AutomationEmailNotification($subject, $message ?? ''));

        return true;
    }

    private function createNotification(array $config, array $context): bool
    {
        $user = $this->resolveUser($context);
        if (! $user) {
            throw new \RuntimeException('User not resolved');
        }

        $title = Arr::get($config, 'title', 'Notificacion');
        $message = Arr::get($config, 'message');
        $url = Arr::get($config, 'url');
        $category = Arr::get($config, 'category', 'product');

        $templateKey = Arr::get($config, 'template_key');
        if ($templateKey) {
            $variables = $this->buildVariables($user, $context);
            $templates = app(TemplateRenderService::class);
            $title = $templates->renderSubject($templateKey, $variables, $title) ?? $title;
            $message = $templates->render($templateKey, $variables, $message) ?? $message;
        }

        app(UserNotificationService::class)->create($user, $category, $title, $message, $url);

        return true;
    }

    private function notifyAdmin(array $config, array $context): bool
    {
        $title = Arr::get($config, 'title', 'Alerta del sistema');
        $message = Arr::get($config, 'message', 'Se detecto un evento que requiere atencion.');
        $templateKey = Arr::get($config, 'template_key');

        $admins = \App\Models\User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['admin', 'superadmin']))
            ->get();

        foreach ($admins as $admin) {
            $finalTitle = $title;
            $finalMessage = $message;

            if ($templateKey) {
                $variables = $this->buildVariables($admin, $context);
                $templates = app(TemplateRenderService::class);
                $finalTitle = $templates->renderSubject($templateKey, $variables, $title) ?? $title;
                $finalMessage = $templates->render($templateKey, $variables, $message) ?? $message;
            }

            app(UserNotificationService::class)->create($admin, 'security', $finalTitle, $finalMessage, Arr::get($config, 'url'));
        }

        return true;
    }

    private function closeTicket(array $config, array $context): bool
    {
        $ticketId = Arr::get($context, 'ticket_id');
        if (! $ticketId) {
            throw new \RuntimeException('Ticket not resolved');
        }

        $ticket = \App\Models\SupportTicket::query()->find($ticketId);
        if (! $ticket) {
            throw new \RuntimeException('Ticket not found');
        }

        if ($ticket->status !== 'closed') {
            $ticket->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);
        }

        return true;
    }

    private function triggerWebhook(array $config, array $context): bool
    {
        $user = $this->resolveUser($context);
        if (! $user) {
            throw new \RuntimeException('User not resolved');
        }

        $event = Arr::get($config, 'event', 'automation.triggered');
        $payload = Arr::get($config, 'payload', []);

        app(WebhookService::class)->dispatchUserEvent($user, $event, array_merge($payload, $context));

        return true;
    }

    private function logEvent(array $config, array $context): bool
    {
        $user = $this->resolveUser($context);
        $description = Arr::get($config, 'description', 'Automation event');

        app(ActivityService::class)->log('automation_event', [
            'user' => $user,
            'actor' => $user,
            'subject' => $user,
            'description' => $description,
            'metadata' => $context,
            'request' => request(),
        ]);

        return true;
    }

    private function resolveUser(array $context): ?User
    {
        $userId = Arr::get($context, 'user_id');
        if (! $userId) {
            return null;
        }

        return \App\Models\User::query()->find((int) $userId);
    }

    private function buildVariables(User $user, array $context): array
    {
        // Combina variables base del sistema con el contexto del evento.
        return array_merge([
            'user_name' => (string) ($user->name ?? ''),
            'user_email' => (string) ($user->email ?? ''),
            'app_name' => (string) config('app.name'),
            'support_email' => (string) (config('mail.from.address') ?? ''),
            'date' => now()->toDateString(),
        ], $this->sanitizeContext($context));
    }

    private function sanitizeContext(array $context): array
    {
        $sanitized = [];

        foreach ($context as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $sanitized[$key] = (string) ($value ?? '');
            }
        }

        return $sanitized;
    }

    private function moduleForEvent(string $eventKey): ?string
    {
        if (str_starts_with($eventKey, 'billing.')) {
            return 'billing';
        }
        if (str_starts_with($eventKey, 'subscription.')) {
            return 'billing';
        }
        if (str_starts_with($eventKey, 'support.')) {
            return 'support';
        }
        if (str_starts_with($eventKey, 'webhook.')) {
            return 'webhooks';
        }
        if (str_starts_with($eventKey, 'user.')) {
            return 'users';
        }

        return null;
    }

    private function recordRun(Automation $automation, string $status, string $eventKey, array $metadata = []): void
    {
        AutomationRun::create([
            'automation_id' => $automation->id,
            'event_key' => $eventKey,
            'status' => $status,
            'executed_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    private function markSkipped(string $reason): void {}
}
