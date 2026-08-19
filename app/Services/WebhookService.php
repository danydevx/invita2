<?php

namespace App\Services;

use App\Jobs\DeliverWebhookJob;
use App\Models\User;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class WebhookService
{
    public const EVENTS = [
        'user.updated',
        'user.password_changed',
        'subscription.created',
        'subscription.updated',
        'subscription.canceled',
        'payment.succeeded',
        'payment.failed',
        'ticket.created',
        'ticket.replied',
        'ticket.closed',
    ];

    public function dispatchUserEvent(User $user, string $event, array $data): void
    {
        // Evita ejecutar webhooks si el modulo esta desactivado.
        if (! app(ModuleService::class)->isEnabled('webhooks')) {
            return;
        }

        $endpoints = WebhookEndpoint::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        foreach ($endpoints as $endpoint) {
            if (! $this->endpointWantsEvent($endpoint, $event)) {
                continue;
            }

            $payload = $this->buildPayload($user, $event, $data);
            $this->enqueueDelivery($endpoint, $event, $payload);
        }
    }

    public function sendTest(WebhookEndpoint $endpoint): WebhookDelivery
    {
        // Evita enviar pruebas si el modulo esta desactivado.
        if (! app(ModuleService::class)->isEnabled('webhooks')) {
            return WebhookDelivery::create([
                'webhook_endpoint_id' => $endpoint->id,
                'event' => 'webhook.test',
                'payload' => [],
                'attempt_count' => 0,
                'failed_at' => now(),
                'error_message' => 'Modulo webhooks desactivado',
            ]);
        }

        $payload = $this->buildPayload($endpoint->user, 'webhook.test', [
            'message' => 'Evento de prueba',
        ]);

        return $this->enqueueDelivery($endpoint, 'webhook.test', $payload);
    }

    public function retryDelivery(WebhookDelivery $delivery): WebhookDelivery
    {
        $endpoint = $delivery->endpoint;
        $payload = $delivery->payload ?? [];

        $delivery->update([
            'attempt_count' => $delivery->attempt_count + 1,
            'response_status' => null,
            'response_body' => null,
            'error_message' => null,
            'delivered_at' => null,
            'failed_at' => null,
        ]);

        return $this->enqueueDelivery($endpoint, $delivery->event, $payload, $delivery);
    }

    private function endpointWantsEvent(WebhookEndpoint $endpoint, string $event): bool
    {
        $events = $endpoint->events ?? [];

        return in_array($event, $events, true);
    }

    private function buildPayload(?User $user, string $event, array $data): array
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => $event,
            'created_at' => now()->toIso8601String(),
            'user_id' => $user?->id,
            'data' => $data,
        ];
    }

    public function enqueueDelivery(WebhookEndpoint $endpoint, string $event, array $payload, ?WebhookDelivery $delivery = null): WebhookDelivery
    {
        $delivery = $delivery ?: WebhookDelivery::create([
            'webhook_endpoint_id' => $endpoint->id,
            'event' => $event,
            'payload' => $payload,
            'attempt_count' => 1,
        ]);

        DeliverWebhookJob::dispatch($delivery->id);

        return $delivery;
    }

    public function deliverNow(int $deliveryId): void
    {
        // Evita procesar entregas si el modulo esta desactivado.
        if (! app(ModuleService::class)->isEnabled('webhooks')) {
            return;
        }

        $delivery = WebhookDelivery::query()->with('endpoint')->find($deliveryId);
        if (! $delivery || ! $delivery->endpoint) {
            return;
        }

        $endpoint = $delivery->endpoint;
        if (! $endpoint->is_active) {
            $delivery->update([
                'failed_at' => now(),
                'error_message' => 'Webhook inactivo',
            ]);

            return;
        }

        $payload = $delivery->payload ?? [];
        $timestamp = now()->timestamp;
        $signature = $this->signature($endpoint->secret, $payload, $timestamp);

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->withHeaders([
                    'X-Webhook-Event' => $delivery->event,
                    'X-Webhook-Timestamp' => (string) $timestamp,
                    'X-Webhook-Signature' => $signature,
                ])
                ->post($endpoint->url, $payload);

            $delivery->update([
                'response_status' => $response->status(),
                'response_body' => $this->truncateBody($response->body()),
                'delivered_at' => $response->successful() ? now() : null,
                'failed_at' => $response->successful() ? null : now(),
                'error_message' => $response->successful() ? null : 'Respuesta no exitosa',
            ]);

            $endpoint->update([
                'last_used_at' => now(),
                'failure_count' => $response->successful() ? 0 : $endpoint->failure_count + 1,
            ]);
        } catch (RequestException $exception) {
            $delivery->update([
                'response_status' => $exception->response?->status(),
                'response_body' => $this->truncateBody($exception->response?->body()),
                'failed_at' => now(),
                'error_message' => $exception->getMessage(),
            ]);

            $endpoint->update([
                'failure_count' => $endpoint->failure_count + 1,
            ]);
        } catch (Throwable $exception) {
            $delivery->update([
                'failed_at' => now(),
                'error_message' => $exception->getMessage(),
            ]);

            $endpoint->update([
                'failure_count' => $endpoint->failure_count + 1,
            ]);
        }
    }

    private function signature(string $secret, array $payload, int $timestamp): string
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $content = $timestamp.'.'.$body;

        return hash_hmac('sha256', $content, $secret);
    }

    private function truncateBody(?string $body): ?string
    {
        if (! $body) {
            return null;
        }

        return mb_substr($body, 0, 2000);
    }
}
