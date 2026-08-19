<?php

use Illuminate\Support\Facades\Route;
use Modules\BusinessModules\Http\Controllers\BusinessModulesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('businessmodules', BusinessModulesController::class)->names('businessmodules');
});
