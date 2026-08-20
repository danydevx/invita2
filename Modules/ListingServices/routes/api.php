<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingServices\Http\Controllers\ServicesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('services', ServicesController::class)->names('services');
});
