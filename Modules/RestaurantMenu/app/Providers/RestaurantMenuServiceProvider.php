<?php

namespace Modules\RestaurantMenu\Providers;

use Illuminate\Support\ServiceProvider;

class RestaurantMenuServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}