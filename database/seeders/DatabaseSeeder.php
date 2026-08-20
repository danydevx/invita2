<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crea un usuario de prueba solo si se solicita explicitamente.
        if (env('SAAS_SEED_TEST_USER', false)) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $this->call([
            RolesAndPermissionsSeeder::class,
            FeatureFlagSeeder::class,
            SettingSeeder::class,
            SystemModuleSeeder::class,
            MessageTemplateSeeder::class,
            AutomationSeeder::class,
            ModuleDefinitionSeeder::class,
            ContactFormSubmissionSeeder::class,
            \Modules\Properties\Database\Seeders\PropertiesDatabaseSeeder::class,
        ]);
    }
}
