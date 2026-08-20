<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingLocations\Http\Controllers\LocationDataController;
use Modules\ListingLocations\Http\Controllers\ListingLocationsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('locations', ListingLocationsController::class)->names('locations');
});

Route::get('v1/location-data/states', [LocationDataController::class, 'states']);
Route::get('v1/location-data/municipalities/{stateCode}', [LocationDataController::class, 'municipalities']);
