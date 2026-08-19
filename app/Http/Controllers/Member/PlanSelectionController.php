<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanSelectionController extends Controller
{
    public function show(Request $request)
    {
        $planId = $request->session()->get('selected_plan_id');
        $plan = null;

        if ($planId) {
            $plan = Plan::query()
                ->where('is_active', true)
                ->find($planId);
        }

        $subscription = $request->user()->currentSubscription;

        $couponData = null;
        $price = $plan?->price;
        $couponId = $request->session()->get('applied_coupon_id');
        if ($plan && $couponId) {
            $coupon = \App\Models\Coupon::query()->find($couponId);
            if ($coupon && $this->isCouponValidForPlan($coupon, $plan)) {
                $discountAmount = $this->calculateDiscount($coupon, $price);
                $couponData = [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'discount_amount' => $discountAmount,
                ];
            } else {
                $request->session()->forget('applied_coupon_id');
            }
        }

        return Inertia::render('Member/PlanSelection/Show', [
            'plan' => $plan ? [
                'id' => $plan->id,
                'name' => $plan->name,
                'description' => $plan->description,
                'price' => $plan->price,
                'billing_period' => $plan->billing_period,
                'features' => $plan->features ?? [],
                'limits' => $plan->limits ?? [],
                'stripe_price_id' => $plan->stripe_price_id,
            ] : null,
            'coupon' => $couponData,
            'price' => $price,
            'subscription' => $subscription ? [
                'plan_name' => $subscription->plan?->name,
                'status' => $subscription->status,
                'starts_at' => $subscription->starts_at?->toDateString(),
                'ends_at' => $subscription->ends_at?->toDateString(),
            ] : null,
        ]);
    }

    public function clear(Request $request)
    {
        $request->session()->forget(['selected_plan_id', 'applied_coupon_id']);

        return redirect('/member')->with('success', 'Seleccion de plan guardada. Te avisaremos cuando el checkout este listo.');
    }

    private function isCouponValidForPlan(\App\Models\Coupon $coupon, Plan $plan): bool
    {
        if (! $coupon->is_active) {
            return false;
        }

        $now = now();
        if ($coupon->starts_at && $coupon->starts_at->gt($now)) {
            return false;
        }

        if ($coupon->ends_at && $coupon->ends_at->lt($now)) {
            return false;
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return false;
        }

        if (! $coupon->applies_to_all_plans) {
            return $coupon->plans()->where('plans.id', $plan->id)->exists();
        }

        return true;
    }

    private function calculateDiscount(\App\Models\Coupon $coupon, $price): ?float
    {
        if ($price === null) {
            return null;
        }

        $amount = (float) $price;
        if ($coupon->type === 'percent') {
            return round($amount * ((float) $coupon->value / 100), 2);
        }

        return min((float) $coupon->value, $amount);
    }
}
