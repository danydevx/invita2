<?php

namespace Modules\Properties\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Properties';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapMemberRoutes();
        $this->mapAdminRoutes();
        $this->mapApiRoutes();
    }

    protected function mapMemberRoutes(): void
    {
        Route::middleware(['web', 'auth', 'verified', 'active'])
            ->group(module_path($this->name, '/routes/member.php'));
    }

    protected function mapAdminRoutes(): void
    {
        Route::middleware(['web', 'auth', 'verified', 'active', 'role:superadmin|admin'])
            ->group(module_path($this->name, '/routes/admin.php'));
    }

    protected function mapApiRoutes(): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(module_path($this->name, '/routes/api.php'));
    }
}
