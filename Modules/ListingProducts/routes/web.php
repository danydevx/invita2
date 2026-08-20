<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingProducts\Http\Controllers\ListingProductsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('listingproducts', ListingProductsController::class)->names('listingproducts');
});
