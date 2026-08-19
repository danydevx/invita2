<?php

namespace Modules\OfficeHours\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class OfficeHoursServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'OfficeHours';

    protected string $nameLower = 'officehours';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
