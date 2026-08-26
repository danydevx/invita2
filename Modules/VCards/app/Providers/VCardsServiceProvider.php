<?php

namespace Modules\VCards\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class VCardsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'VCards';
    protected string $nameLower = 'vcards';

    protected array $providers = [
        RouteServiceProvider::class,
    ];
}
