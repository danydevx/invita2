<?php

namespace Modules\Promotions\Providers;

use Illuminate\Support\ServiceProvider;

class PromotionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}
