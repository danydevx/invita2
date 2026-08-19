<?php

namespace Database\Seeders;

use App\Models\SystemModule;
use Illuminate\Database\Seeder;

class SystemModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['key' => 'users', 'name' => 'Usuarios', 'description' => 'Gestion de usuarios y cuentas.'],
            ['key' => 'roles', 'name' => 'Roles', 'description' => 'Gestion de roles y permisos.'],
            ['key' => 'permissions', 'name' => 'Permisos', 'description' => 'Permisos del sistema.'],
            ['key' => 'settings', 'name' => 'Settings', 'description' => 'Configuracion global del sistema.'],
            ['key' => 'billing', 'name' => 'Billing', 'description' => 'Suscripciones, pagos y facturacion.'],
            ['key' => 'support', 'name' => 'Soporte', 'description' => 'Tickets y help center.'],
            ['key' => 'activity', 'name' => 'Actividad', 'description' => 'Activity log del sistema.'],
            ['key' => 'security-events', 'name' => 'Seguridad', 'description' => 'Eventos de seguridad.'],
            ['key' => 'system-errors', 'name' => 'Errores', 'description' => 'Errores del sistema.'],
            ['key' => 'exports', 'name' => 'Exportaciones', 'description' => 'Exportaciones de datos.'],
            ['key' => 'media', 'name' => 'Archivos', 'description' => 'Archivos y media del usuario.'],
            ['key' => 'api', 'name' => 'API', 'description' => 'API keys e integraciones.'],
            ['key' => 'webhooks', 'name' => 'Webhooks', 'description' => 'Webhooks salientes.'],
            ['key' => 'automations', 'name' => 'Automatizaciones', 'description' => 'Motor de automatizaciones.'],
            ['key' => 'legal', 'name' => 'Legales', 'description' => 'Documentos legales.'],
            ['key' => 'notifications', 'name' => 'Notificaciones', 'description' => 'Notificaciones internas y email.'],
            ['key' => 'announcements', 'name' => 'Anuncios', 'description' => 'Anuncios del sistema.'],
            ['key' => 'feature-flags', 'name' => 'Feature Flags', 'description' => 'Flags y switches del sistema.'],
            ['key' => 'integrations', 'name' => 'Integraciones', 'description' => 'Portal de integraciones.'],
            ['key' => 'queues', 'name' => 'Colas', 'description' => 'Jobs y colas del sistema.'],
            ['key' => 'invitations', 'name' => 'Invitaciones', 'description' => 'Invitaciones internas.'],
        ];

        foreach ($modules as $module) {
            SystemModule::firstOrCreate([
                'key' => $module['key'],
            ], [
                'name' => $module['name'],
                'description' => $module['description'],
                'is_active' => true,
            ]);
        }
    }
}
