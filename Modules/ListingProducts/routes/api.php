<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingProducts\Http\Controllers\ListingProductsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('listingproducts', ListingProductsController::class)->names('listingproducts');
});
