<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\UserNotificationService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserSubscriptionController extends Controller
{
    public function index(Request $request, User $user)
    {
        $subscription = $user->currentSubscription;

        $plans = Plan::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($plan) => [
                'id' => $plan->id,
                'label' => $plan->name,
            ]);

        return Inertia::render('Admin/Users/Subscriptions', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'plans' => $plans,
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'plan_id' => $subscription->plan_id,
                'status' => $subscription->status,
                'starts_at' => $subscription->starts_at?->toDateString(),
                'ends_at' => $subscription->ends_at?->toDateString(),
                'trial_ends_at' => $subscription->trial_ends_at?->toDateString(),
                'price' => $subscription->price,
                'billing_period' => $subscription->billing_period,
                'canceled_at' => $subscription->canceled_at?->toDateString(),
            ] : null,
        ]);
    }

    public function store(Request $request, User $user, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            'status' => ['required', 'string', 'in:pending,trial,active,expired,canceled'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'trial_ends_at' => ['nullable', 'date'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'billing_period' => ['nullable', 'string', 'max:50'],
        ]);

        $existing = $user->currentSubscription;
        $payload = [
            'plan_id' => $data['plan_id'],
            'status' => $data['status'],
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'trial_ends_at' => $data['trial_ends_at'] ?? null,
            'price' => $data['price'] ?? null,
            'billing_period' => $data['billing_period'] ?? null,
        ];

        if ($existing) {
            $existing->update($payload);
            $subscription = $existing;
        } else {
            $subscription = $user->subscriptions()->create($payload);
        }

        $activity->log('subscription_updated', [
            'user' => $user,
            'actor' => $request->user(),
            'subject' => $subscription,
            'description' => 'Suscripcion actualizada por admin',
            'request' => $request,
        ]);

        $webhooks->dispatchUserEvent($user, 'subscription.updated', [
            'subscription_id' => $subscription->id,
            'status' => $subscription->status,
            'plan_id' => $subscription->plan_id,
        ]);

        $notifications->create(
            $user,
            'billing',
            'Suscripcion actualizada',
            'Tu suscripcion fue actualizada por un administrador.',
            '/member'
        );

        return redirect()->back()->with('success', 'Suscripcion actualizada correctamente.');
    }

    public function update(Request $request, User $user, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks)
    {
        return $this->store($request, $user, $activity, $notifications, $webhooks);
    }

    public function destroy(Request $request, User $user, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks)
    {
        $subscription = $user->currentSubscription;

        if (! $subscription) {
            return redirect()->back()->withErrors(['delete' => 'No existe suscripcion para este usuario.']);
        }

        $subscription->delete();

        $activity->log('subscription_deleted', [
            'user' => $user,
            'actor' => $request->user(),
            'subject' => $user,
            'description' => 'Suscripcion eliminada por admin',
            'request' => $request,
        ]);

        $webhooks->dispatchUserEvent($user, 'subscription.deleted', [
            'user_id' => $user->id,
        ]);

        $notifications->create(
            $user,
            'billing',
            'Suscripcion cancelada',
            'Tu suscripcion fue cancelada por un administrador.',
            '/member'
        );

        return redirect()->back()->with('success', 'Suscripcion eliminada correctamente.');
    }
}
