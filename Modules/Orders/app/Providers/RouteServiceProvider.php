<?php

namespace Modules\Orders\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')
                ->group(module_path('Orders', '/routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(module_path('Orders', '/routes/api.php'));
        });
    }
}
