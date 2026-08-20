<?php

namespace Modules\ListingProducts\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ListingProductsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ListingProducts';

    protected string $nameLower = 'listingproducts';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
