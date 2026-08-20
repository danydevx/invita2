<?php

namespace Modules\ListingFeatures\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class FeaturesServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ListingFeatures';

    protected string $nameLower = 'listingfeatures';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
