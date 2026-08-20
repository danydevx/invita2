<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingModules\Http\Controllers\ListingModulesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('listingmodules', ListingModulesController::class)->names('listingmodules');
});
