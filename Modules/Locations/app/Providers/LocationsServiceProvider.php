<?php

namespace Modules\Locations\Providers;

use Illuminate\Support\ServiceProvider;

class LocationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function register(): void
    {
    }
}
