<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanUnverifiedUsers extends Command
{
    protected $signature = 'users:clean-unverified
        {--days=7 : Dias sin verificacion antes de eliminar}
        {--dry-run : Solo muestra que se eliminaria sin eliminar}';

    protected $description = 'Elimina usuarios no verificados despues de X dias';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $cutoff = now()->subDays($days);

        $query = User::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<', $cutoff)
            ->whereDoesntHave('listings');

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->info('No se encontraron usuarios sin verificar para eliminar.');
            return self::SUCCESS;
        }

        $this->info("Se encontraron {$users->count()} usuarios sin verificar:");

        foreach ($users as $user) {
            $this->line("  - {$user->email} (creado: {$user->created_at->diffForHumans()})");
        }

        if ($dryRun) {
            $this->warn('Dry-run: no se elimino nenhum usuario.');
            return self::SUCCESS;
        }

        if (! $this->confirm("Deseas eliminar {$users->count()} usuarios?")) {
            $this->info('Operacion cancelada.');
            return self::SUCCESS;
        }

        $count = $users->count();

        foreach ($users as $user) {
            $user->delete();
        }

        $this->info("Se eliminaron {$count} usuarios.");

        return self::SUCCESS;
    }
}
