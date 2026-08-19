<?php

use Illuminate\Support\Facades\Route;
use Modules\Businesses\Http\Controllers\BusinessesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('businesses', BusinessesController::class)->names('businesses');
});
