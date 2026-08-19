<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', '');
        $provider = $request->input('provider', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        $payments = Payment::query()
            ->with(['user:id,name,email', 'plan:id,name', 'subscription:id,plan_id'])
            ->when($search !== '', function ($query) use ($search) {
                $needle = mb_strtolower($search);
                $query->where(function ($q) use ($needle, $search) {
                    $q->whereHas('user', function ($userQuery) use ($needle) {
                        $userQuery->whereRaw('LOWER(name) like ?', ['%'.$needle.'%'])
                            ->orWhereRaw('LOWER(email) like ?', ['%'.$needle.'%']);
                    })
                        ->orWhere('provider_reference', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($provider !== '', fn ($query) => $query->where('provider', $provider))
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($payment) => [
                'id' => $payment->id,
                'user' => $payment->user ? [
                    'id' => $payment->user->id,
                    'name' => $payment->user->name,
                    'email' => $payment->user->email,
                ] : null,
                'plan' => $payment->plan ? [
                    'id' => $payment->plan->id,
                    'name' => $payment->plan->name,
                ] : null,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'provider' => $payment->provider,
                'provider_reference' => $payment->provider_reference,
                'paid_at' => $payment->paid_at?->toDateString(),
                'created_at' => $payment->created_at?->toDateString(),
            ]);

        $providers = Payment::query()
            ->whereNotNull('provider')
            ->distinct()
            ->orderBy('provider')
            ->pluck('provider')
            ->values();

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments,
            'providers' => $providers,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'provider' => $provider,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Payments/Create', [
            'users' => $this->userOptions(),
            'plans' => $this->planOptions(),
            'subscriptions' => $this->subscriptionOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function store(Request $request, ActivityService $activity, WebhookService $webhooks)
    {
        $data = $this->validated($request);

        $payment = Payment::create($data);

        $type = match ($payment->status) {
            'paid' => 'payment_succeeded',
            'failed' => 'payment_failed',
            default => 'payment_created',
        };

        $activity->log($type, [
            'user' => $payment->user,
            'actor' => $request->user(),
            'subject' => $payment,
            'description' => 'Pago creado',
            'request' => $request,
        ]);

        if ($payment->user && in_array($type, ['payment_succeeded', 'payment_failed'], true)) {
            $webhooks->dispatchUserEvent($payment->user, $type === 'payment_succeeded' ? 'payment.succeeded' : 'payment.failed', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
            ]);
        }

        return redirect()->route('admin.payments.edit', $payment);
    }

    public function edit(Payment $payment)
    {
        return Inertia::render('Admin/Payments/Edit', [
            'payment' => [
                'id' => $payment->id,
                'user_id' => $payment->user_id,
                'subscription_id' => $payment->subscription_id,
                'plan_id' => $payment->plan_id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'provider' => $payment->provider,
                'provider_reference' => $payment->provider_reference,
                'payment_method' => $payment->payment_method,
                'description' => $payment->description,
                'paid_at' => $payment->paid_at?->toDateString(),
            ],
            'users' => $this->userOptions(),
            'plans' => $this->planOptions(),
            'subscriptions' => $this->subscriptionOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function update(Request $request, Payment $payment, ActivityService $activity, WebhookService $webhooks)
    {
        $data = $this->validated($request, $payment->id);

        $previousStatus = $payment->status;

        $payment->update($data);

        $type = 'payment_updated';
        if ($previousStatus !== $payment->status) {
            $type = match ($payment->status) {
                'paid' => 'payment_succeeded',
                'failed' => 'payment_failed',
                default => 'payment_updated',
            };
        }

        $activity->log($type, [
            'user' => $payment->user,
            'actor' => $request->user(),
            'subject' => $payment,
            'description' => 'Pago actualizado',
            'request' => $request,
        ]);

        if ($payment->user && in_array($type, ['payment_succeeded', 'payment_failed'], true)) {
            $webhooks->dispatchUserEvent($payment->user, $type === 'payment_succeeded' ? 'payment.succeeded' : 'payment.failed', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
            ]);
        }

        return redirect()->route('admin.payments.edit', $payment)->with('success', 'Pago actualizado correctamente.');
    }

    private function validated(Request $request, ?int $paymentId = null): array
    {
        return $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'subscription_id' => ['nullable', 'integer', Rule::exists('subscriptions', 'id')],
            'plan_id' => ['nullable', 'integer', Rule::exists('plans', 'id')],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'string', Rule::in($this->statusOptions())],
            'provider' => ['nullable', 'string', 'max:50'],
            'provider_reference' => ['nullable', 'string', 'max:150'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'paid_at' => ['nullable', 'date'],
        ]);
    }

    private function statusOptions(): array
    {
        return ['pending', 'paid', 'failed', 'canceled', 'refunded'];
    }

    private function userOptions()
    {
        return User::query()
            ->orderByDesc('id')
            ->limit(200)
            ->get(['id', 'name', 'email'])
            ->map(fn ($user) => [
                'value' => $user->id,
                'label' => $user->name.' ('.$user->email.')',
            ]);
    }

    private function planOptions()
    {
        return Plan::query()
            ->orderByRaw('sort_order is null, sort_order asc')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn ($plan) => [
                'value' => $plan->id,
                'label' => $plan->name,
            ]);
    }

    private function subscriptionOptions()
    {
        return Subscription::query()
            ->with(['user:id,name', 'plan:id,name'])
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(fn ($subscription) => [
                'value' => $subscription->id,
                'label' => sprintf(
                    '#%d - %s (%s)',
                    $subscription->id,
                    $subscription->user?->name ?? 'Usuario',
                    $subscription->plan?->name ?? 'Sin plan'
                ),
            ]);
    }
}
