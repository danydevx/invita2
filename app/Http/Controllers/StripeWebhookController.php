<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\StripeWebhookEvent;
use App\Models\Subscription;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\ModuleService;
use App\Services\SystemErrorService;
use App\Services\UserNotificationService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, ActivityService $activity, UserNotificationService $notifications, SystemErrorService $errors, WebhookService $webhooks)
    {
        // Evita procesar eventos de Stripe si el modulo billing esta desactivado.
        if (! app(ModuleService::class)->isEnabled('billing')) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if (empty($secret)) {
            return response('Webhook secret missing', Response::HTTP_BAD_REQUEST);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (\Throwable $e) {
            return response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        $record = StripeWebhookEvent::firstOrCreate(
            ['stripe_event_id' => $event->id],
            [
                'type' => $event->type,
                'payload' => json_decode($payload, true),
                'status' => 'received',
            ]
        );

        if ($record->processed_at) {
            return response('Already processed', Response::HTTP_OK);
        }

        try {
            $this->processEvent($event, $activity, $notifications, $webhooks);

            $record->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            return response('ok', Response::HTTP_OK);
        } catch (\Throwable $e) {
            $errors->logException($e, $request, 'webhook', [
                'provider' => 'stripe',
                'event_id' => $event->id ?? null,
                'event_type' => $event->type ?? null,
            ]);
            $record->update([
                'status' => 'failed',
            ]);

            return response('Webhook error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function processEvent($event, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks): void
    {
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object, $activity, $notifications, $webhooks);
                break;
            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object, $activity, $notifications, $webhooks);
                break;
            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object, $activity, $notifications, $webhooks);
                break;
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $this->handleSubscriptionEvent($event->data->object, $activity, $notifications, $webhooks);
                break;
            default:
                break;
        }
    }

    private function handleCheckoutCompleted($session, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks): void
    {
        $user = $this->resolveUserFromMetadata($session->metadata ?? null);
        $plan = $this->resolvePlanFromMetadata($session->metadata ?? null);

        if (! $user || ! $plan) {
            return;
        }

        $subscriptionId = $session->subscription ?? null;
        $customerId = $session->customer ?? null;

        $localSubscription = $this->upsertSubscriptionFromStripe(
            $user,
            $plan,
            $subscriptionId,
            $customerId,
            'active'
        );

        if (($session->payment_status ?? null) === 'paid') {
            $paymentReference = $session->payment_intent ?? $session->id;
            $this->upsertPayment([
                'user_id' => $user->id,
                'subscription_id' => $localSubscription?->id,
                'plan_id' => $plan->id,
                'amount' => $session->amount_total ? $session->amount_total / 100 : ($plan->price ?? 0),
                'currency' => $session->currency ?? null,
                'status' => 'paid',
                'provider_reference' => $paymentReference,
                'payment_method' => $session->payment_method_types[0] ?? null,
                'metadata' => [
                    'checkout_session_id' => $session->id,
                ],
            ]);

            $this->notify($user, $notifications, 'Pago confirmado', 'Tu pago fue confirmado por Stripe.', '/member/account');
            $this->logActivity($activity, 'checkout_paid', $user, $localSubscription, [
                'stripe_session_id' => $session->id,
            ]);

            $webhooks->dispatchUserEvent($user, 'payment.succeeded', [
                'subscription_id' => $localSubscription?->id,
                'plan_id' => $plan->id,
                'amount' => $session->amount_total ? $session->amount_total / 100 : ($plan->price ?? 0),
                'currency' => $session->currency ?? null,
            ]);
        }
    }

    private function handleInvoicePaymentSucceeded($invoice, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks): void
    {
        $user = $this->resolveUserFromInvoice($invoice);
        $plan = $this->resolvePlanFromInvoice($invoice);
        $subscriptionId = $invoice->subscription ?? null;
        $customerId = $invoice->customer ?? null;

        if (! $user) {
            return;
        }

        $localSubscription = null;
        if ($plan && $subscriptionId) {
            $localSubscription = $this->upsertSubscriptionFromStripe(
                $user,
                $plan,
                $subscriptionId,
                $customerId,
                'active'
            );
        }

        $paymentReference = $invoice->payment_intent ?? $invoice->id;
        $coupon = $this->resolveCouponFromInvoice($invoice);
        $this->upsertPayment([
            'user_id' => $user->id,
            'subscription_id' => $localSubscription?->id,
            'plan_id' => $plan?->id,
            'amount' => $invoice->amount_paid ? $invoice->amount_paid / 100 : 0,
            'currency' => $invoice->currency ?? null,
            'status' => 'paid',
            'provider_reference' => $paymentReference,
            'payment_method' => null,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'subscription_id' => $subscriptionId,
                'coupon_id' => $coupon?->id,
            ],
        ]);

        $payment = Payment::query()
            ->where('provider', 'stripe')
            ->where('provider_reference', $paymentReference)
            ->first();

        $this->upsertInvoiceFromStripe($invoice, $user, $payment, $localSubscription);

        if ($coupon) {
            $this->incrementCouponUsage($coupon);
        }

        $this->notify($user, $notifications, 'Pago confirmado', 'Tu pago fue confirmado por Stripe.', '/member/payments');
        $this->logActivity($activity, 'payment_succeeded', $user, $localSubscription, [
            'invoice_id' => $invoice->id,
        ]);

        $webhooks->dispatchUserEvent($user, 'payment.succeeded', [
            'invoice_id' => $invoice->id,
            'subscription_id' => $localSubscription?->id,
            'amount' => $invoice->amount_paid ? $invoice->amount_paid / 100 : 0,
            'currency' => $invoice->currency ?? null,
        ]);
    }

    private function handleInvoicePaymentFailed($invoice, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks): void
    {
        $user = $this->resolveUserFromInvoice($invoice);
        $plan = $this->resolvePlanFromInvoice($invoice);
        $subscriptionId = $invoice->subscription ?? null;
        $customerId = $invoice->customer ?? null;

        if (! $user) {
            return;
        }

        $localSubscription = null;
        if ($plan && $subscriptionId) {
            $localSubscription = $this->upsertSubscriptionFromStripe(
                $user,
                $plan,
                $subscriptionId,
                $customerId,
                'failed'
            );
        }

        $paymentReference = $invoice->payment_intent ?? $invoice->id;
        $this->upsertPayment([
            'user_id' => $user->id,
            'subscription_id' => $localSubscription?->id,
            'plan_id' => $plan?->id,
            'amount' => $invoice->amount_due ? $invoice->amount_due / 100 : 0,
            'currency' => $invoice->currency ?? null,
            'status' => 'failed',
            'provider_reference' => $paymentReference,
            'payment_method' => null,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'subscription_id' => $subscriptionId,
            ],
        ]);

        $payment = Payment::query()
            ->where('provider', 'stripe')
            ->where('provider_reference', $paymentReference)
            ->first();

        $this->upsertInvoiceFromStripe($invoice, $user, $payment, $localSubscription, 'pending');

        $this->notify($user, $notifications, 'Pago fallido', 'Tu pago no pudo completarse.', '/member/payments');
        $this->logActivity($activity, 'payment_failed', $user, $localSubscription, [
            'invoice_id' => $invoice->id,
        ]);

        app(\App\Services\AutomationService::class)->trigger('billing.payment_failed', [
            'user_id' => $user->id,
            'subscription_id' => $localSubscription?->id,
            'plan_name' => $plan?->name,
            'amount' => $invoice->amount_due ? $invoice->amount_due / 100 : 0,
            'currency' => $invoice->currency ?? null,
            'invoice_id' => $invoice->id,
        ]);

        $webhooks->dispatchUserEvent($user, 'payment.failed', [
            'invoice_id' => $invoice->id,
            'subscription_id' => $localSubscription?->id,
            'amount' => $invoice->amount_due ? $invoice->amount_due / 100 : 0,
            'currency' => $invoice->currency ?? null,
        ]);
    }

    private function handleSubscriptionEvent($subscription, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks): void
    {
        $user = $this->resolveUserFromSubscription($subscription);
        $plan = $this->resolvePlanFromSubscription($subscription);

        if (! $user || ! $plan) {
            return;
        }

        $status = $this->mapStripeSubscriptionStatus($subscription->status ?? null);
        $localSubscription = $this->upsertSubscriptionFromStripe(
            $user,
            $plan,
            $subscription->id,
            $subscription->customer ?? null,
            $status,
            $subscription
        );

        $message = match ($status) {
            'active' => 'Tu suscripcion esta activa.',
            'canceled' => 'Tu suscripcion fue cancelada.',
            'failed' => 'Tu suscripcion tuvo un problema de cobro.',
            default => 'Tu suscripcion fue actualizada.',
        };

        $this->notify($user, $notifications, 'Suscripcion actualizada', $message, '/member/account');

        $type = 'subscription_updated';
        if ($status === 'canceled') {
            $type = 'subscription_canceled';
        } elseif ($localSubscription?->wasRecentlyCreated) {
            $type = 'subscription_created';
        }

        $this->logActivity($activity, $type, $user, $localSubscription, [
            'stripe_subscription_id' => $subscription->id,
            'status' => $status,
        ]);

        $event = match ($type) {
            'subscription_created' => 'subscription.created',
            'subscription_canceled' => 'subscription.canceled',
            default => 'subscription.updated',
        };

        $webhooks->dispatchUserEvent($user, $event, [
            'subscription_id' => $localSubscription?->id,
            'status' => $status,
            'plan_id' => $plan->id,
        ]);

        if ($status === 'canceled') {
            app(\App\Services\AutomationService::class)->trigger('subscription.canceled', [
                'user_id' => $user->id,
                'subscription_id' => $localSubscription?->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
            ]);
        }
    }

    private function resolveUserFromMetadata($metadata): ?User
    {
        $userId = $metadata->user_id ?? null;
        if ($userId) {
            return User::query()->find((int) $userId);
        }

        return null;
    }

    private function resolvePlanFromMetadata($metadata): ?Plan
    {
        $planId = $metadata->plan_id ?? null;
        if ($planId) {
            return Plan::query()->find((int) $planId);
        }

        return null;
    }

    private function resolveUserFromInvoice($invoice): ?User
    {
        $subscriptionId = $invoice->subscription ?? null;
        if ($subscriptionId) {
            $localSubscription = Subscription::query()
                ->where('provider', 'stripe')
                ->where('provider_reference', $subscriptionId)
                ->first();
            if ($localSubscription) {
                return $localSubscription->user;
            }
        }

        $customerId = $invoice->customer ?? null;
        if ($customerId) {
            $localSubscription = Subscription::query()
                ->where('provider', 'stripe')
                ->where('provider_customer_id', $customerId)
                ->first();
            if ($localSubscription) {
                return $localSubscription->user;
            }
        }

        return null;
    }

    private function resolvePlanFromInvoice($invoice): ?Plan
    {
        $line = $invoice->lines->data[0] ?? null;
        $priceId = $line?->price?->id ?? null;
        if ($priceId) {
            return Plan::query()->where('stripe_price_id', $priceId)->first();
        }

        return null;
    }

    private function resolveCouponFromInvoice($invoice): ?Coupon
    {
        $discounts = $invoice->discounts ? $invoice->discounts->data : [];
        $discount = $discounts[0] ?? null;
        if (! $discount) {
            return null;
        }

        $promotionCodeId = $discount->promotion_code ?? null;
        if ($promotionCodeId) {
            return Coupon::query()->where('stripe_promotion_code_id', $promotionCodeId)->first();
        }

        $couponId = $discount->coupon->id ?? null;
        if ($couponId) {
            return Coupon::query()->where('stripe_coupon_id', $couponId)->first();
        }

        return null;
    }

    private function resolveUserFromSubscription($subscription): ?User
    {
        $localSubscription = Subscription::query()
            ->where('provider', 'stripe')
            ->where('provider_reference', $subscription->id)
            ->first();

        if ($localSubscription) {
            return $localSubscription->user;
        }

        $customerId = $subscription->customer ?? null;
        if ($customerId) {
            $localSubscription = Subscription::query()
                ->where('provider', 'stripe')
                ->where('provider_customer_id', $customerId)
                ->first();
            if ($localSubscription) {
                return $localSubscription->user;
            }
        }

        return null;
    }

    private function resolvePlanFromSubscription($subscription): ?Plan
    {
        $item = $subscription->items->data[0] ?? null;
        $priceId = $item?->price?->id ?? null;
        if ($priceId) {
            return Plan::query()->where('stripe_price_id', $priceId)->first();
        }

        return null;
    }

    private function mapStripeSubscriptionStatus(?string $status): string
    {
        return match ($status) {
            'active', 'trialing' => 'active',
            'canceled' => 'canceled',
            'incomplete', 'past_due', 'unpaid' => 'failed',
            default => 'pending',
        };
    }

    private function upsertSubscriptionFromStripe(
        User $user,
        Plan $plan,
        ?string $stripeSubscriptionId,
        ?string $customerId,
        string $status,
        $stripeSubscription = null
    ): ?Subscription {
        $subscription = null;

        if ($stripeSubscriptionId) {
            $subscription = Subscription::query()
                ->where('provider', 'stripe')
                ->where('provider_reference', $stripeSubscriptionId)
                ->first();
        }

        if (! $subscription) {
            $subscription = Subscription::query()
                ->where('user_id', $user->id)
                ->latest('id')
                ->first();
        }

        $payload = [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => $status,
            'starts_at' => now(),
            'price' => $plan->price,
            'billing_period' => $plan->billing_period,
            'provider' => 'stripe',
            'provider_reference' => $stripeSubscriptionId,
            'provider_customer_id' => $customerId,
        ];

        if ($stripeSubscription) {
            $payload['starts_at'] = $stripeSubscription->start_date
                ? now()->setTimestamp($stripeSubscription->start_date)
                : $payload['starts_at'];
            $payload['ends_at'] = $stripeSubscription->current_period_end
                ? now()->setTimestamp($stripeSubscription->current_period_end)
                : null;
            $payload['trial_ends_at'] = $stripeSubscription->trial_end
                ? now()->setTimestamp($stripeSubscription->trial_end)
                : null;
            $payload['canceled_at'] = $stripeSubscription->canceled_at
                ? now()->setTimestamp($stripeSubscription->canceled_at)
                : null;
        }

        if ($subscription) {
            $subscription->update($payload);

            return $subscription;
        }

        return Subscription::create($payload);
    }

    private function upsertPayment(array $payload): void
    {
        $reference = $payload['provider_reference'] ?? null;
        if (! $reference) {
            return;
        }

        $payment = Payment::query()
            ->where('provider', 'stripe')
            ->where('provider_reference', $reference)
            ->first();

        if ($payment) {
            $payment->update([
                'status' => $payload['status'],
                'amount' => $payload['amount'],
                'currency' => $payload['currency'],
                'subscription_id' => $payload['subscription_id'],
                'plan_id' => $payload['plan_id'],
                'payment_method' => $payload['payment_method'],
                'metadata' => $payload['metadata'],
                'paid_at' => $payload['status'] === 'paid' ? now() : $payment->paid_at,
            ]);

            return;
        }

        Payment::create([
            'user_id' => $payload['user_id'],
            'subscription_id' => $payload['subscription_id'],
            'plan_id' => $payload['plan_id'],
            'amount' => $payload['amount'],
            'currency' => $payload['currency'],
            'status' => $payload['status'],
            'provider' => 'stripe',
            'provider_reference' => $reference,
            'payment_method' => $payload['payment_method'],
            'description' => 'Pago Stripe',
            'paid_at' => $payload['status'] === 'paid' ? now() : null,
            'metadata' => $payload['metadata'],
        ]);
    }

    private function upsertInvoiceFromStripe($invoice, User $user, ?Payment $payment, ?Subscription $subscription, ?string $statusOverride = null): void
    {
        $status = $statusOverride ?: $this->mapStripeInvoiceStatus($invoice->status ?? null);

        $record = Invoice::query()
            ->where('provider_reference', $invoice->id)
            ->first();

        $payload = [
            'user_id' => $user->id,
            'payment_id' => $payment?->id,
            'subscription_id' => $subscription?->id,
            'number' => $invoice->number ?? null,
            'type' => 'receipt',
            'status' => $status,
            'amount' => $invoice->amount_paid ? $invoice->amount_paid / 100 : ($invoice->amount_due ? $invoice->amount_due / 100 : null),
            'currency' => $invoice->currency ?? null,
            'issued_at' => $invoice->created ? now()->setTimestamp($invoice->created) : null,
            'due_at' => $invoice->due_date ? now()->setTimestamp($invoice->due_date) : null,
            'paid_at' => $invoice->status === 'paid' && $invoice->status_transitions?->paid_at
                ? now()->setTimestamp($invoice->status_transitions->paid_at)
                : null,
            'file_url' => $invoice->hosted_invoice_url ?? $invoice->invoice_pdf ?? null,
            'provider_reference' => $invoice->id,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'subscription_id' => $invoice->subscription ?? null,
            ],
        ];

        if ($record) {
            $record->update($payload);

            return;
        }

        Invoice::create($payload);
    }

    private function mapStripeInvoiceStatus(?string $status): string
    {
        return match ($status) {
            'paid' => 'paid',
            'open' => 'issued',
            'void', 'uncollectible' => 'canceled',
            default => 'pending',
        };
    }

    private function incrementCouponUsage(Coupon $coupon): void
    {
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return;
        }

        $coupon->increment('used_count');
    }

    private function notify(User $user, UserNotificationService $notifications, string $title, string $message, string $url): void
    {
        $notifications->create($user, 'billing', $title, $message, $url);
    }

    private function logActivity(ActivityService $activity, string $event, User $user, $subject = null, array $metadata = []): void
    {
        $activity->log($event, [
            'user' => $user,
            'actor' => $user,
            'subject' => $subject,
            'description' => $event,
            'metadata' => $metadata,
        ]);
    }
}
