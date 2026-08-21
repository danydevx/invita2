<?php

use App\Services\SettingService;

if (!function_exists('app_name')) {
    function app_name(): string
    {
        static $name = null;

        if ($name === null) {
            $name = app(SettingService::class)->get('app.name', config('app.name', 'SaaS'));
        }

        return $name;
    }
}
