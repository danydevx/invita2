<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $subscription = $user->currentSubscription;
        $plan = $subscription?->plan;

        return Inertia::render('Member/Account/Show', [
            'account' => [
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => (bool) $user->is_active,
                'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
                'created_at' => $user->created_at?->toDateTimeString(),
            ],
            'subscription' => $subscription ? [
                'plan_name' => $plan?->name,
                'status' => $subscription->status,
                'price' => $subscription->price,
                'billing_period' => $subscription->billing_period,
                'starts_at' => $subscription->starts_at?->toDateTimeString(),
                'ends_at' => $subscription->ends_at?->toDateTimeString(),
                'trial_ends_at' => $subscription->trial_ends_at?->toDateTimeString(),
                'can_manage' => $subscription->provider === 'stripe' && ! empty($subscription->provider_customer_id),
            ] : null,
            'limits' => $plan?->limits ?? [],
        ]);
    }
}
