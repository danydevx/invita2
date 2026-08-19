<?php

namespace Modules\ClientFidelity\Providers;

use Illuminate\Support\ServiceProvider;

class ClientFidelityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
