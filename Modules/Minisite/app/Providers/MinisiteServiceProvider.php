<?php

namespace Modules\Minisite\Providers;

use Illuminate\Support\ServiceProvider;

class MinisiteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/member.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/public.php');
    }
}
