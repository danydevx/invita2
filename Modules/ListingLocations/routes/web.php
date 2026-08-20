<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingLocations\Http\Controllers\ListingLocationsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('locations', ListingLocationsController::class)->names('locations');
});
