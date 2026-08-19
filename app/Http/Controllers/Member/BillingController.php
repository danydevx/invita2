<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Services\FeatureService;
use App\Services\UserNotificationService;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class BillingController extends Controller
{
    public function portal(Request $request, ActivityService $activity, UserNotificationService $notifications, FeatureService $features)
    {
        if (! $features->enabled($request->user(), 'can_access_billing', true)) {
            return back()->with('error', 'No tienes acceso a facturacion.');
        }

        $user = $request->user();
        $subscription = $user->currentSubscription;

        if (! $subscription || $subscription->provider !== 'stripe') {
            return back()->with('error', 'No tienes una suscripcion activa para gestionar.');
        }

        if (empty($subscription->provider_customer_id)) {
            return back()->with('error', 'No se pudo abrir el portal de facturacion.');
        }

        $stripeSecret = config('services.stripe.secret');
        if (empty($stripeSecret)) {
            return back()->with('error', 'Stripe no esta configurado correctamente.');
        }

        try {
            $stripe = new StripeClient($stripeSecret);
            $session = $stripe->billingPortal->sessions->create([
                'customer' => $subscription->provider_customer_id,
                'return_url' => url('/member/account'),
            ]);

            $activity->log('billing_portal_opened', [
                'user' => $user,
                'actor' => $user,
                'subject' => $subscription,
                'description' => 'Portal de facturacion abierto',
                'request' => $request,
            ]);

            $notifications->create(
                $user,
                'billing',
                'Portal de facturacion',
                'Abriste el portal de facturacion de Stripe.',
                '/member/account'
            );

            return redirect()->away($session->url);
        } catch (\Throwable $e) {
            $activity->log('billing_portal_failed', [
                'user' => $user,
                'actor' => $user,
                'subject' => $subscription,
                'description' => 'Error al abrir portal',
                'metadata' => [
                    'message' => $e->getMessage(),
                ],
                'request' => $request,
            ]);

            return back()->with('error', 'No fue posible abrir el portal de facturacion.');
        }
    }
}
