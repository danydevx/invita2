<?php

namespace Modules\ListingLocations\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ListingLocationsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ListingLocations';

    protected string $nameLower = 'listinglocations';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
