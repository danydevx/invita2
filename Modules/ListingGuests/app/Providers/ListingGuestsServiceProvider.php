<?php

namespace Modules\ListingGuests\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class ListingGuestsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ListingGuests';
    protected string $nameLower = 'listingguests';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}