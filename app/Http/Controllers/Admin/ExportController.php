<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\FeatureService;
use App\Services\SystemErrorService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function index(Request $request, FeatureService $features)
    {
        if (! $features->enabled($request->user(), 'can_export', true)) {
            return redirect('/dashboard')->with('error', 'No tienes acceso a exportaciones.');
        }
        $roles = Role::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($role) => [
                'id' => $role->id,
                'label' => $role->name,
            ]);

        $plans = Plan::query()
            ->orderByRaw('sort_order is null, sort_order asc')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn ($plan) => [
                'id' => $plan->id,
                'label' => $plan->name,
            ]);

        $subscriptionStatuses = Subscription::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->values();

        $paymentStatuses = Payment::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->values();

        $providers = Payment::query()
            ->whereNotNull('provider')
            ->distinct()
            ->orderBy('provider')
            ->pluck('provider')
            ->values();

        $ticketStatuses = SupportTicket::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->values();

        $ticketPriorities = SupportTicket::query()
            ->whereNotNull('priority')
            ->distinct()
            ->orderBy('priority')
            ->pluck('priority')
            ->values();

        $ticketCategories = SupportTicket::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values();

        $activityTypes = Activity::query()
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->values();

        return Inertia::render('Admin/Exports/Index', [
            'roles' => $roles,
            'plans' => $plans,
            'subscriptionStatuses' => $subscriptionStatuses,
            'paymentStatuses' => $paymentStatuses,
            'providers' => $providers,
            'ticketStatuses' => $ticketStatuses,
            'ticketPriorities' => $ticketPriorities,
            'ticketCategories' => $ticketCategories,
            'activityTypes' => $activityTypes,
        ]);
    }

    public function users(Request $request, ActivityService $activity, SystemErrorService $errors, FeatureService $features)
    {
        if (! $features->enabled($request->user(), 'can_export', true)) {
            return back()->withErrors(['user' => 'No tienes acceso a exportaciones.']);
        }
        $query = User::query()->with(['roles:id,name']);

        $q = trim((string) $request->input('q', ''));
        $roleId = $request->input('role', '');
        $isActive = $request->input('is_active', '');
        $verified = $request->input('email_verified', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        if ($q !== '') {
            $needle = mb_strtolower($q);
            $query->where(function ($qBuilder) use ($needle, $q) {
                $qBuilder->whereRaw('LOWER(name) like ?', ['%'.$needle.'%'])
                    ->orWhereRaw('LOWER(email) like ?', ['%'.$needle.'%']);

                if (is_numeric($q)) {
                    $qBuilder->orWhere('id', (int) $q);
                }
            });
        }

        if ($roleId !== '') {
            $query->whereHas('roles', function ($roleQuery) use ($roleId) {
                $roleQuery->where('id', $roleId);
            });
        }

        $activeFilter = $this->parseBooleanFilter($isActive);
        if ($activeFilter !== null) {
            $query->where('is_active', $activeFilter);
        }

        if ($verified === 'verified') {
            $query->whereNotNull('email_verified_at');
        }
        if ($verified === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        if ($this->isValidDate($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($this->isValidDate($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->orderBy('id');

        if (! (clone $query)->exists()) {
            return back()->with('error', 'No hay registros para exportar con los filtros seleccionados.');
        }

        $this->logExport($activity, $request, 'export_users', [
            'q' => $q,
            'role' => $roleId,
            'is_active' => $isActive,
            'email_verified' => $verified,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        $headers = ['id', 'name', 'email', 'role', 'is_active', 'email_verified', 'created_at'];
        $filename = $this->filename('users');

        return $this->streamCsv($filename, $headers, function ($handle) use ($query, $errors, $request) {
            try {
                $query->chunk(500, function ($users) use ($handle) {
                    foreach ($users as $user) {
                        $role = $user->roles->pluck('name')->first() ?: '';
                        fputcsv($handle, [
                            $user->id,
                            $user->name,
                            $user->email,
                            $role,
                            $user->is_active ? 'true' : 'false',
                            $user->email_verified_at ? 'true' : 'false',
                            $user->created_at?->toDateTimeString(),
                        ]);
                    }
                });
            } catch (\Throwable $e) {
                $errors->logException($e, $request, 'system', ['export' => 'users']);
                throw $e;
            }
        });
    }

    public function subscriptions(Request $request, ActivityService $activity, SystemErrorService $errors, FeatureService $features)
    {
        if (! $features->enabled($request->user(), 'can_export', true)) {
            return back()->withErrors(['user' => 'No tienes acceso a exportaciones.']);
        }
        $query = Subscription::query()->with(['user:id,name,email', 'plan:id,name']);

        $status = $request->input('status', '');
        $planId = $request->input('plan_id', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($planId !== '') {
            $query->where('plan_id', $planId);
        }
        if ($this->isValidDate($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($this->isValidDate($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->orderBy('id');

        if (! (clone $query)->exists()) {
            return back()->with('error', 'No hay registros para exportar con los filtros seleccionados.');
        }

        $this->logExport($activity, $request, 'export_subscriptions', [
            'status' => $status,
            'plan_id' => $planId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        $headers = [
            'id',
            'user_id',
            'user_name',
            'user_email',
            'plan',
            'status',
            'starts_at',
            'ends_at',
            'created_at',
        ];
        $filename = $this->filename('subscriptions');

        return $this->streamCsv($filename, $headers, function ($handle) use ($query, $errors, $request) {
            try {
                $query->chunk(500, function ($subscriptions) use ($handle) {
                    foreach ($subscriptions as $subscription) {
                        fputcsv($handle, [
                            $subscription->id,
                            $subscription->user_id,
                            $subscription->user?->name ?? '',
                            $subscription->user?->email ?? '',
                            $subscription->plan?->name ?? '',
                            $subscription->status,
                            $subscription->starts_at?->toDateTimeString(),
                            $subscription->ends_at?->toDateTimeString(),
                            $subscription->created_at?->toDateTimeString(),
                        ]);
                    }
                });
            } catch (\Throwable $e) {
                $errors->logException($e, $request, 'system', ['export' => 'subscriptions']);
                throw $e;
            }
        });
    }

    public function payments(Request $request, ActivityService $activity, SystemErrorService $errors, FeatureService $features)
    {
        if (! $features->enabled($request->user(), 'can_export', true)) {
            return back()->withErrors(['user' => 'No tienes acceso a exportaciones.']);
        }
        $query = Payment::query()->with(['user:id,name,email', 'plan:id,name']);

        $status = $request->input('status', '');
        $provider = $request->input('provider', '');
        $planId = $request->input('plan_id', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($provider !== '') {
            $query->where('provider', $provider);
        }
        if ($planId !== '') {
            $query->where('plan_id', $planId);
        }
        if ($this->isValidDate($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($this->isValidDate($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->orderBy('id');

        if (! (clone $query)->exists()) {
            return back()->with('error', 'No hay registros para exportar con los filtros seleccionados.');
        }

        $this->logExport($activity, $request, 'export_payments', [
            'status' => $status,
            'provider' => $provider,
            'plan_id' => $planId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        $headers = [
            'id',
            'user_id',
            'user_name',
            'user_email',
            'amount',
            'currency',
            'status',
            'provider',
            'provider_reference',
            'paid_at',
            'created_at',
        ];
        $filename = $this->filename('payments');

        return $this->streamCsv($filename, $headers, function ($handle) use ($query, $errors, $request) {
            try {
                $query->chunk(500, function ($payments) use ($handle) {
                    foreach ($payments as $payment) {
                        $amount = $payment->amount !== null ? number_format((float) $payment->amount, 2, '.', '') : '';
                        fputcsv($handle, [
                            $payment->id,
                            $payment->user_id,
                            $payment->user?->name ?? '',
                            $payment->user?->email ?? '',
                            $amount,
                            $payment->currency,
                            $payment->status,
                            $payment->provider,
                            $payment->provider_reference,
                            $payment->paid_at?->toDateTimeString(),
                            $payment->created_at?->toDateTimeString(),
                        ]);
                    }
                });
            } catch (\Throwable $e) {
                $errors->logException($e, $request, 'system', ['export' => 'payments']);
                throw $e;
            }
        });
    }

    public function tickets(Request $request, ActivityService $activity, SystemErrorService $errors, FeatureService $features)
    {
        if (! $features->enabled($request->user(), 'can_export', true)) {
            return back()->withErrors(['user' => 'No tienes acceso a exportaciones.']);
        }
        $query = SupportTicket::query()->with(['user:id,name,email']);

        $status = $request->input('status', '');
        $priority = $request->input('priority', '');
        $category = $request->input('category', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($priority !== '') {
            $query->where('priority', $priority);
        }
        if ($category !== '') {
            $query->where('category', $category);
        }
        if ($this->isValidDate($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($this->isValidDate($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->orderBy('id');

        if (! (clone $query)->exists()) {
            return back()->with('error', 'No hay registros para exportar con los filtros seleccionados.');
        }

        $this->logExport($activity, $request, 'export_tickets', [
            'status' => $status,
            'priority' => $priority,
            'category' => $category,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        $headers = [
            'id',
            'user_id',
            'user_name',
            'user_email',
            'subject',
            'status',
            'priority',
            'category',
            'last_reply_at',
            'created_at',
        ];
        $filename = $this->filename('tickets');

        return $this->streamCsv($filename, $headers, function ($handle) use ($query, $errors, $request) {
            try {
                $query->chunk(500, function ($tickets) use ($handle) {
                    foreach ($tickets as $ticket) {
                        fputcsv($handle, [
                            $ticket->id,
                            $ticket->user_id,
                            $ticket->user?->name ?? '',
                            $ticket->user?->email ?? '',
                            $ticket->subject,
                            $ticket->status,
                            $ticket->priority,
                            $ticket->category,
                            $ticket->last_reply_at?->toDateTimeString(),
                            $ticket->created_at?->toDateTimeString(),
                        ]);
                    }
                });
            } catch (\Throwable $e) {
                $errors->logException($e, $request, 'system', ['export' => 'tickets']);
                throw $e;
            }
        });
    }

    public function activities(Request $request, ActivityService $activity, SystemErrorService $errors, FeatureService $features)
    {
        if (! $features->enabled($request->user(), 'can_export', true)) {
            return back()->withErrors(['user' => 'No tienes acceso a exportaciones.']);
        }
        $query = Activity::query()->with(['user:id,name']);

        $userId = trim((string) $request->input('user_id', ''));
        $type = trim((string) $request->input('type', ''));
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        if ($userId !== '' && ctype_digit($userId)) {
            $query->where('user_id', (int) $userId);
        }
        if ($type !== '') {
            $query->where('type', $type);
        }
        if ($this->isValidDate($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($this->isValidDate($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->orderBy('id');

        if (! (clone $query)->exists()) {
            return back()->with('error', 'No hay registros para exportar con los filtros seleccionados.');
        }

        $this->logExport($activity, $request, 'export_activities', [
            'user_id' => $userId,
            'type' => $type,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        $headers = [
            'id',
            'user_id',
            'user_name',
            'type',
            'description',
            'subject_type',
            'subject_id',
            'ip_address',
            'created_at',
        ];
        $filename = $this->filename('activities');

        return $this->streamCsv($filename, $headers, function ($handle) use ($query, $errors, $request) {
            try {
                $query->chunk(500, function ($activities) use ($handle) {
                    foreach ($activities as $activity) {
                        fputcsv($handle, [
                            $activity->id,
                            $activity->user_id,
                            $activity->user?->name ?? '',
                            $activity->type,
                            $activity->description,
                            $activity->subject_type,
                            $activity->subject_id,
                            $activity->ip_address,
                            $activity->created_at?->toDateTimeString(),
                        ]);
                    }
                });
            } catch (\Throwable $e) {
                $errors->logException($e, $request, 'system', ['export' => 'activities']);
                throw $e;
            }
        });
    }

    private function streamCsv(string $filename, array $headers, callable $callback): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $callback) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            $callback($handle);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function filename(string $prefix): string
    {
        return sprintf('%s-%s.csv', $prefix, now()->toDateString());
    }

    private function parseBooleanFilter(?string $value): ?bool
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $trueValues = ['1', 'true', 'active', 'yes'];
        $falseValues = ['0', 'false', 'inactive', 'no'];

        if (in_array($value, $trueValues, true)) {
            return true;
        }
        if (in_array($value, $falseValues, true)) {
            return false;
        }

        return null;
    }

    private function isValidDate(?string $value): bool
    {
        if (! is_string($value) || $value === '') {
            return false;
        }

        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
    }

    private function logExport(ActivityService $activity, Request $request, string $type, array $filters): void
    {
        $activity->log($type, [
            'user' => $request->user(),
            'actor' => $request->user(),
            'description' => 'Exportacion generada',
            'metadata' => [
                'filters' => array_filter($filters, fn ($value) => $value !== '' && $value !== null),
            ],
            'request' => $request,
        ]);
    }
}
