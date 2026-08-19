<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallSaas extends Command
{
    protected $signature = 'saas:install
        {--seeders= : Lista de seeders separados por coma}
        {--no-migrate : Omite la migracion}';

    protected $description = 'Ejecuta la instalacion base del starter kit.';

    public function handle(): int
    {
        // Define los seeders base del starter kit.
        $defaultSeeders = [
            'Database\\Seeders\\RolesAndPermissionsSeeder',
            'Database\\Seeders\\SystemModuleSeeder',
            'Database\\Seeders\\MessageTemplateSeeder',
            'Database\\Seeders\\AutomationSeeder',
            'Database\\Seeders\\FeatureFlagSeeder',
            'Database\\Seeders\\SettingSeeder',
        ];

        $seeders = $defaultSeeders;
        if ($this->option('seeders')) {
            $seeders = array_filter(array_map('trim', explode(',', $this->option('seeders'))));
        }

        if (! $this->option('no-migrate')) {
            $this->info('Ejecutando migraciones...');
            Artisan::call('migrate', ['--force' => true]);
            $this->output->write(Artisan::output());
        }

        foreach ($seeders as $seeder) {
            $this->info('Ejecutando seeder: '.$seeder);
            Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
            $this->output->write(Artisan::output());
        }

        $this->info('Instalacion base completada.');
        $this->line('Siguiente paso sugerido: php artisan saas:create-superadmin');

        return self::SUCCESS;
    }
}
