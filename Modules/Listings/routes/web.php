<?php

use Illuminate\Support\Facades\Route;
use Modules\Listings\Http\Controllers\ListingsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('listings', ListingsController::class)->names('listings');
});
