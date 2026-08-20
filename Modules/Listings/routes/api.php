<?php

use Illuminate\Support\Facades\Route;
use Modules\Listings\Http\Controllers\ListingsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('listings', ListingsController::class)->names('listings');
});
