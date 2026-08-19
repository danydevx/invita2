<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'key' => 'user_welcome',
                'name' => 'Bienvenida de usuario',
                'description' => 'Email de bienvenida para nuevos usuarios.',
                'subject' => 'Bienvenido a {{app_name}}',
                'content' => "Hola {{user_name}}\nTu cuenta ya esta activa y lista para usarse.\nSi tienes alguna pregunta, responde a este correo.",
            ],
            [
                'key' => 'password_changed',
                'name' => 'Password cambiada',
                'description' => 'Aviso de cambio de password.',
                'subject' => 'Tu contrasena fue actualizada',
                'content' => "Hola {{user_name}}\nTu contrasena fue actualizada correctamente.\nSi no realizaste este cambio, contacta soporte de inmediato.",
            ],
            [
                'key' => 'payment_failed',
                'name' => 'Pago fallido',
                'description' => 'Aviso cuando un pago falla.',
                'subject' => 'Pago fallido',
                'content' => "Hola {{user_name}}\nTu pago del plan {{plan_name}} no pudo completarse.\nPuedes actualizar tu metodo de pago en el panel.",
            ],
            [
                'key' => 'subscription_activated',
                'name' => 'Suscripcion activada',
                'description' => 'Confirmacion de suscripcion activa.',
                'subject' => 'Suscripcion activada',
                'content' => "Hola {{user_name}}\nTu suscripcion al plan {{plan_name}} fue activada el {{date}}.",
            ],
            [
                'key' => 'trial_ending',
                'name' => 'Trial por terminar',
                'description' => 'Recordatorio de fin de trial.',
                'subject' => 'Tu trial esta por terminar',
                'content' => "Hola {{user_name}}\nTu periodo de prueba termina el {{date}}.\nRevisa tu plan en el panel.",
            ],
            [
                'key' => 'ticket_reply',
                'name' => 'Respuesta de ticket',
                'description' => 'Aviso de respuesta en ticket.',
                'subject' => 'Respuesta a tu ticket',
                'content' => "Hola {{user_name}}\nTu ticket {{ticket_id}} tiene una nueva respuesta.\nIngresa al panel para verla.",
            ],
        ];

        foreach ($items as $item) {
            MessageTemplate::firstOrCreate([
                'key' => $item['key'],
            ], [
                'name' => $item['name'],
                'description' => $item['description'],
                'subject' => $item['subject'],
                'content' => $item['content'],
                'is_active' => true,
            ]);
        }
    }
}
