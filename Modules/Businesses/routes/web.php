<?php

use Illuminate\Support\Facades\Route;
use Modules\Businesses\Http\Controllers\BusinessesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('businesses', BusinessesController::class)->names('businesses');
});
