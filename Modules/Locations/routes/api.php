<?php

use Illuminate\Support\Facades\Route;
use Modules\Locations\Http\Controllers\LocationDataController;
use Modules\Locations\Http\Controllers\LocationsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('locations', LocationsController::class)->names('locations');
});

Route::get('v1/location-data/states', [LocationDataController::class, 'states']);
Route::get('v1/location-data/municipalities/{stateCode}', [LocationDataController::class, 'municipalities']);
