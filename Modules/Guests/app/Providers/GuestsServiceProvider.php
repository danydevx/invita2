<?php

namespace Modules\Guests\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class GuestsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Guests';
    protected string $nameLower = 'guests';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
