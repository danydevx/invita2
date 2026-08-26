<?php

namespace Modules\VCards\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Nwidart\Modules\Facades\Module;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'VCards';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(function () {
                require module_path($this->name, 'routes/web.php');
                require module_path($this->name, 'routes/member.php');
                require module_path($this->name, 'routes/public.php');
            });
    }
}
