<?php

namespace Modules\Checkin\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class CheckinServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Checkin';
    protected string $nameLower = 'checkin';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
