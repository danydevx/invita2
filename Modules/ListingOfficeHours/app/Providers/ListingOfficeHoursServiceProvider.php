<?php

namespace Modules\ListingOfficeHours\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class ListingOfficeHoursServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ListingOfficeHours';

    protected string $nameLower = 'listingofficehours';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}