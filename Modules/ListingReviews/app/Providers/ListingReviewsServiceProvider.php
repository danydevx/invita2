<?php

namespace Modules\ListingReviews\Providers;

use Illuminate\Support\ServiceProvider;

class ListingReviewsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}
