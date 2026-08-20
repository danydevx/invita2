<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingLocations\Http\Controllers\ListingLocationsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('locations', ListingLocationsController::class)->names('locations');
});
