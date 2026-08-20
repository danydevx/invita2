<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingModules\Http\Controllers\ListingModulesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('listingmodules', ListingModulesController::class)->names('listingmodules');
});
