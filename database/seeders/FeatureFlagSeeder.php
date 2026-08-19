<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    public function run(): void
    {
        $flags = [
            [
                'key' => 'can_use_api',
                'name' => 'API keys',
                'description' => 'Permite generar API keys en el area member.',
                'type' => 'boolean',
                'default_value' => 'false',
            ],
            [
                'key' => 'can_use_webhooks',
                'name' => 'Webhooks',
                'description' => 'Permite crear webhooks salientes.',
                'type' => 'boolean',
                'default_value' => 'false',
            ],
            [
                'key' => 'features.api_enabled',
                'name' => 'API habilitada',
                'description' => 'Habilita la API en general para el SaaS.',
                'type' => 'boolean',
                'default_value' => 'true',
            ],
            [
                'key' => 'features.webhooks_enabled',
                'name' => 'Webhooks habilitados',
                'description' => 'Habilita webhooks en general para el SaaS.',
                'type' => 'boolean',
                'default_value' => 'true',
            ],
            [
                'key' => 'module_support',
                'name' => 'Soporte',
                'description' => 'Habilita el modulo de soporte en member.',
                'type' => 'boolean',
                'default_value' => 'true',
            ],
            [
                'key' => 'can_create_tickets',
                'name' => 'Crear tickets',
                'description' => 'Permite crear tickets de soporte.',
                'type' => 'boolean',
                'default_value' => 'true',
            ],
            [
                'key' => 'can_upload_files',
                'name' => 'Subida de archivos',
                'description' => 'Permite subir archivos en el area member.',
                'type' => 'boolean',
                'default_value' => 'true',
            ],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::firstOrCreate([
                'key' => $flag['key'],
            ], [
                'name' => $flag['name'],
                'description' => $flag['description'],
                'type' => $flag['type'],
                'default_value' => $flag['default_value'],
                'is_active' => true,
            ]);
        }
    }
}
