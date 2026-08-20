<?php

namespace Modules\ListingMinisite\Providers;

use Illuminate\Support\ServiceProvider;

class ListingMinisiteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/member.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/public.php');
    }
}