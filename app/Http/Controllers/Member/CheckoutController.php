<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\ActivityService;
use App\Services\SystemErrorService;
use App\Services\UserNotificationService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
    public function create(Request $request, Plan $plan, ActivityService $activity, UserNotificationService $notifications, SystemErrorService $errors, WebhookService $webhooks)
    {
        $user = $request->user();

        if (! $plan->is_active) {
            return back()->with('error', 'Este plan no esta disponible.');
        }

        if (empty($plan->stripe_price_id)) {
            return back()->with('error', 'Este plan aun no tiene checkout disponible.');
        }

        $stripeSecret = config('services.stripe.secret');
        if (empty($stripeSecret)) {
            return back()->with('error', 'Stripe no esta configurado correctamente.');
        }

        $coupon = $this->resolveCouponFromSession($request, $plan);
        if ($coupon && empty($coupon->stripe_promotion_code_id) && empty($coupon->stripe_coupon_id)) {
            return back()->with('error', 'El cupon no esta disponible para Stripe.');
        }

        try {
            $stripe = new StripeClient($stripeSecret);

            $discounts = [];
            if ($coupon) {
                if (! empty($coupon->stripe_promotion_code_id)) {
                    $discounts[] = ['promotion_code' => $coupon->stripe_promotion_code_id];
                } elseif (! empty($coupon->stripe_coupon_id)) {
                    $discounts[] = ['coupon' => $coupon->stripe_coupon_id];
                }
            }

            $metadata = [
                'user_id' => (string) $user->id,
                'plan_id' => (string) $plan->id,
                'plan_slug' => (string) $plan->slug,
            ];
            if ($coupon) {
                $metadata['coupon_id'] = (string) $coupon->id;
            }

            $payload = [
                'mode' => 'subscription',
                'payment_method_types' => ['card'],
                'customer_email' => $user->email,
                'line_items' => [[
                    'price' => $plan->stripe_price_id,
                    'quantity' => 1,
                ]],
                'metadata' => $metadata,
                'success_url' => url('/member/checkout/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url('/member/checkout/cancel'),
            ];

            if (! empty($discounts)) {
                $payload['discounts'] = $discounts;
            }

            $session = $stripe->checkout->sessions->create($payload);

            $request->session()->put('stripe_checkout_session_id', $session->id);
            $request->session()->put('selected_plan_id', $plan->id);

            $activity->log('checkout_started', [
                'user' => $user,
                'actor' => $user,
                'subject' => $plan,
                'description' => 'Checkout iniciado',
                'metadata' => [
                    'plan_id' => $plan->id,
                    'stripe_session_id' => $session->id,
                ],
                'request' => $request,
            ]);

            $notifications->create(
                $user,
                'billing',
                'Checkout iniciado',
                'Estamos procesando tu pago en Stripe.',
                '/member/plan-selection'
            );

            return redirect()->away($session->url);
        } catch (\Throwable $e) {
            $errors->logException($e, $request, 'payment', [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
            ]);
            $activity->log('checkout_failed', [
                'user' => $user,
                'actor' => $user,
                'subject' => $plan,
                'description' => 'Error al iniciar checkout',
                'metadata' => [
                    'message' => $e->getMessage(),
                ],
                'request' => $request,
            ]);

            return back()->with('error', 'No pudimos iniciar el checkout. Intenta mas tarde.');
        }
    }

    public function success(Request $request, ActivityService $activity, UserNotificationService $notifications, SystemErrorService $errors, WebhookService $webhooks)
    {
        $user = $request->user();
        $sessionId = (string) $request->query('session_id', '');

        if ($sessionId === '') {
            return redirect('/member/plan-selection')->with('error', 'No se pudo validar el checkout.');
        }

        $stripeSecret = config('services.stripe.secret');
        if (empty($stripeSecret)) {
            return redirect('/member/plan-selection')->with('error', 'Stripe no esta configurado correctamente.');
        }

        try {
            $stripe = new StripeClient($stripeSecret);
            $session = $stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['payment_intent', 'subscription'],
            ]);

            if (! empty($session->metadata?->user_id) && (int) $session->metadata->user_id !== $user->id) {
                abort(403);
            }

            $planId = (int) ($session->metadata->plan_id ?? 0);
            $plan = Plan::query()->find($planId);

            if (! $plan) {
                return redirect('/member/plan-selection')->with('error', 'Plan no encontrado.');
            }

            $paymentStatus = $session->payment_status ?? null;
            if (! in_array($paymentStatus, ['paid', 'no_payment_required'], true)) {
                return redirect('/member/checkout/cancel')->with('error', 'El pago no se completo.');
            }

            $stripeSubscription = null;
            if (! empty($session->subscription)) {
                $stripeSubscription = $stripe->subscriptions->retrieve($session->subscription);
            }

            $subscriptionStatus = 'active';
            $endsAt = null;
            $trialEndsAt = null;

            if ($stripeSubscription) {
                if (in_array($stripeSubscription->status, ['active', 'trialing'], true)) {
                    $subscriptionStatus = 'active';
                } elseif ($stripeSubscription->status === 'incomplete') {
                    $subscriptionStatus = 'pending';
                } elseif ($stripeSubscription->status === 'canceled') {
                    $subscriptionStatus = 'canceled';
                } else {
                    $subscriptionStatus = 'failed';
                }

                $endsAt = $stripeSubscription->current_period_end
                    ? now()->setTimestamp($stripeSubscription->current_period_end)
                    : null;
                $trialEndsAt = $stripeSubscription->trial_end
                    ? now()->setTimestamp($stripeSubscription->trial_end)
                    : null;
            }

            $localSubscription = Subscription::query()
                ->where('user_id', $user->id)
                ->latest('id')
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'plan_id' => $plan->id,
                    'status' => $subscriptionStatus,
                    'starts_at' => now(),
                    'ends_at' => $endsAt,
                    'trial_ends_at' => $trialEndsAt,
                    'price' => $plan->price,
                    'billing_period' => $plan->billing_period,
                    'provider' => 'stripe',
                    'provider_reference' => $stripeSubscription?->id,
                    'provider_customer_id' => $session->customer ?? null,
                ]);
            } else {
                $localSubscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'status' => $subscriptionStatus,
                    'starts_at' => now(),
                    'ends_at' => $endsAt,
                    'trial_ends_at' => $trialEndsAt,
                    'price' => $plan->price,
                    'billing_period' => $plan->billing_period,
                    'provider' => 'stripe',
                    'provider_reference' => $stripeSubscription?->id,
                    'provider_customer_id' => $session->customer ?? null,
                ]);
            }

            $paymentReference = $session->payment_intent?->id ?: $session->id;
            $existingPayment = Payment::query()
                ->where('provider', 'stripe')
                ->where('provider_reference', $paymentReference)
                ->first();

            if (! $existingPayment) {
                $amount = $session->amount_total !== null
                    ? $session->amount_total / 100
                    : ($plan->price ?? 0);

                $couponId = $session->metadata->coupon_id ?? null;

                Payment::create([
                    'user_id' => $user->id,
                    'subscription_id' => $localSubscription->id,
                    'plan_id' => $plan->id,
                    'amount' => $amount,
                    'currency' => $session->currency ?? null,
                    'status' => 'paid',
                    'provider' => 'stripe',
                    'provider_reference' => $paymentReference,
                    'payment_method' => $session->payment_method_types[0] ?? null,
                    'description' => 'Pago Stripe Checkout',
                    'paid_at' => now(),
                    'metadata' => [
                        'checkout_session_id' => $session->id,
                        'subscription_id' => $stripeSubscription?->id,
                        'coupon_id' => $couponId,
                    ],
                ]);
            }

            $request->session()->forget(['selected_plan_id', 'stripe_checkout_session_id', 'applied_coupon_id']);

            $activity->log('checkout_paid', [
                'user' => $user,
                'actor' => $user,
                'subject' => $localSubscription,
                'description' => 'Pago completado',
                'metadata' => [
                    'plan_id' => $plan->id,
                    'stripe_session_id' => $session->id,
                ],
                'request' => $request,
            ]);

            $webhooks->dispatchUserEvent($user, 'payment.succeeded', [
                'subscription_id' => $localSubscription?->id,
                'plan_id' => $plan->id,
                'amount' => $session->amount_total ? $session->amount_total / 100 : ($plan->price ?? 0),
                'currency' => $session->currency ?? null,
            ]);

            $notifications->create(
                $user,
                'billing',
                'Pago confirmado',
                'Tu suscripcion fue activada correctamente.',
                '/member/account'
            );

            return Inertia::render('Member/Checkout/Success', [
                'plan' => [
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'billing_period' => $plan->billing_period,
                ],
                'subscription' => [
                    'status' => $localSubscription->status,
                    'starts_at' => $localSubscription->starts_at?->toDateString(),
                    'ends_at' => $localSubscription->ends_at?->toDateString(),
                ],
            ]);
        } catch (\Throwable $e) {
            $errors->logException($e, $request, 'payment', [
                'session_id' => $sessionId,
                'user_id' => $user->id,
            ]);
            $activity->log('checkout_failed', [
                'user' => $user,
                'actor' => $user,
                'description' => 'Error al confirmar checkout',
                'metadata' => [
                    'message' => $e->getMessage(),
                    'stripe_session_id' => $sessionId,
                ],
                'request' => $request,
            ]);

            return redirect('/member/plan-selection')->with('error', 'No pudimos confirmar el pago.');
        }
    }

    public function cancel(Request $request, ActivityService $activity, UserNotificationService $notifications)
    {
        $user = $request->user();

        $activity->log('checkout_canceled', [
            'user' => $user,
            'actor' => $user,
            'description' => 'Checkout cancelado',
            'request' => $request,
        ]);

        $notifications->create(
            $user,
            'billing',
            'Checkout cancelado',
            'Puedes volver a intentarlo cuando quieras.',
            '/pricing'
        );

        return Inertia::render('Member/Checkout/Cancel');
    }

    public function validateCoupon(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        $plan = Plan::query()->where('is_active', true)->find($data['plan_id']);
        if (! $plan) {
            $request->session()->forget('applied_coupon_id');

            return back()->with('error', 'Plan no disponible.');
        }
        $coupon = Coupon::query()
            ->where('code', strtoupper(trim($data['code'])))
            ->first();

        if (! $coupon || ! $this->isCouponValidForPlan($coupon, $plan)) {
            $request->session()->forget('applied_coupon_id');

            return back()->with('error', 'Cupon invalido o no aplicable.');
        }

        $request->session()->put('applied_coupon_id', $coupon->id);

        return back()->with('success', 'Cupon aplicado correctamente.');
    }

    public function clearCoupon(Request $request)
    {
        $request->session()->forget('applied_coupon_id');

        return back()->with('success', 'Cupon removido.');
    }

    private function resolveCouponFromSession(Request $request, Plan $plan): ?Coupon
    {
        $couponId = $request->session()->get('applied_coupon_id');
        if (! $couponId) {
            return null;
        }

        $coupon = Coupon::query()->find($couponId);
        if (! $coupon || ! $this->isCouponValidForPlan($coupon, $plan)) {
            $request->session()->forget('applied_coupon_id');

            return null;
        }

        return $coupon;
    }

    private function isCouponValidForPlan(Coupon $coupon, Plan $plan): bool
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
}
