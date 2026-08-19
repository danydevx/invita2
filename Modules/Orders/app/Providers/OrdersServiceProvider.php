<?php

namespace Modules\Orders\Providers;

use Illuminate\Support\ServiceProvider;

class OrdersServiceProvider extends ServiceProvider
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
