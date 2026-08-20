<?php

namespace Modules\ListingCheckin\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class ListingCheckinServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ListingCheckin';
    protected string $nameLower = 'listingcheckin';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}