<?php

namespace Modules\ListingAppointments\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ListingAppointmentsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'ListingAppointments';

    protected string $nameLower = 'listingappointments';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
