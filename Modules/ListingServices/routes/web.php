<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingServices\Http\Controllers\ServicesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('services', ServicesController::class)->names('services');
});
