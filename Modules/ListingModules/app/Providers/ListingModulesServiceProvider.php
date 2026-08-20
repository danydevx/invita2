<?php

namespace Modules\ListingModules\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ListingModulesServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ListingModules';

    protected string $nameLower = 'listingmodules';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
