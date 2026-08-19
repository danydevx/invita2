<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateSuperAdmin extends Command
{
    protected $signature = 'saas:create-superadmin
        {--name= : Nombre del usuario}
        {--email= : Email del usuario}
        {--password= : Password del usuario}';

    protected $description = 'Crea o actualiza el usuario super-admin inicial.';

    public function handle(): int
    {
        // Solicita datos si no se proporcionan por opciones.
        $name = $this->option('name') ?: $this->ask('Nombre');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->option('password') ?: $this->secret('Password');

        if (! $name || ! $email || ! $password) {
            $this->error('Faltan datos requeridos.');

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => strtolower($email),
                'password' => Hash::make($password),
                'is_active' => true,
            ]);

            $user->profile()->create();
        } else {
            $user->update([
                'name' => $name,
                'email' => strtolower($email),
                'password' => Hash::make($password),
                'is_active' => true,
            ]);
        }

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $role = Role::firstOrCreate(['name' => 'superadmin']);
        $user->assignRole($role);

        $this->info('Superuser listo: '.$user->email);

        return self::SUCCESS;
    }
}
