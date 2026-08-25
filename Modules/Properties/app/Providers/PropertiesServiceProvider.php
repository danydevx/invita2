<?php

namespace Modules\Properties\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Properties\Console\Commands\SetupBaseFields;

class PropertiesServiceProvider extends ServiceProvider
{
    protected string $name = 'Properties';

    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->commands([SetupBaseFields::class]);
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/views/lang', $this->name);
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path($this->name . '/config.php'),
        ], 'config');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', $this->name);
    }
}
