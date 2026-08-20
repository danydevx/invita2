<?php

namespace Modules\ListingPromotions\Providers;

use Illuminate\Support\ServiceProvider;

class ListingPromotionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}
