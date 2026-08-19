<?php

namespace Modules\Features\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class FeaturesServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Features';

    protected string $nameLower = 'features';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
