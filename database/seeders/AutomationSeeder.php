<?php

namespace Database\Seeders;

use App\Models\Automation;
use Illuminate\Database\Seeder;

class AutomationSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Trial ending reminder',
                'event_key' => 'billing.trial_ending',
                'action_key' => 'send_email',
                'is_active' => true,
                'config' => [
                    'days_before' => 3,
                    'template_key' => 'trial_ending',
                    'subject' => 'Tu trial esta por terminar',
                    'message' => 'Tu periodo de prueba terminara pronto. Revisa tu plan en el panel.',
                ],
            ],
            [
                'name' => 'Payment failed notification',
                'event_key' => 'billing.payment_failed',
                'action_key' => 'create_notification',
                'is_active' => true,
                'config' => [
                    'category' => 'billing',
                    'template_key' => 'payment_failed',
                    'title' => 'Pago fallido',
                    'message' => 'Detectamos un pago fallido. Actualiza tu metodo de pago.',
                    'url' => '/member/payments',
                ],
            ],
            [
                'name' => 'Ticket idle reminder',
                'event_key' => 'support.ticket_idle',
                'action_key' => 'notify_admin',
                'is_active' => true,
                'config' => [
                    'idle_hours' => 48,
                    'title' => 'Ticket sin respuesta',
                    'message' => 'Hay un ticket sin actividad reciente.',
                    'url' => '/admin/support',
                ],
            ],
            [
                'name' => 'Webhook failure alert',
                'event_key' => 'webhook.failed_many_times',
                'action_key' => 'notify_admin',
                'is_active' => true,
                'config' => [
                    'threshold' => 3,
                    'window_hours' => 24,
                    'title' => 'Fallos de webhook',
                    'message' => 'Se detectaron multiples fallos de webhook.',
                    'url' => '/admin/webhooks',
                ],
            ],
            [
                'name' => 'Profile incomplete reminder',
                'event_key' => 'user.profile_incomplete',
                'action_key' => 'create_notification',
                'is_active' => true,
                'config' => [
                    'category' => 'product',
                    'title' => 'Completa tu perfil',
                    'message' => 'Actualiza tu perfil para aprovechar el sistema.',
                    'url' => '/member/profile',
                    'days_since_signup' => 7,
                ],
            ],
            [
                'name' => 'Subscription expired',
                'event_key' => 'subscription.expired',
                'action_key' => 'create_notification',
                'is_active' => true,
                'config' => [
                    'category' => 'billing',
                    'title' => 'Suscripcion expirada',
                    'message' => 'Tu suscripcion expiro. Revisa los planes disponibles.',
                    'url' => '/pricing',
                ],
            ],
            [
                'name' => 'Subscription canceled',
                'event_key' => 'subscription.canceled',
                'action_key' => 'create_notification',
                'is_active' => true,
                'config' => [
                    'category' => 'billing',
                    'title' => 'Suscripcion cancelada',
                    'message' => 'Tu suscripcion fue cancelada.',
                    'url' => '/member/account',
                ],
            ],
        ];

        foreach ($items as $item) {
            Automation::firstOrCreate([
                'name' => $item['name'],
                'event_key' => $item['event_key'],
                'action_key' => $item['action_key'],
            ], [
                'is_active' => $item['is_active'],
                'config' => $item['config'],
            ]);
        }
    }
}
