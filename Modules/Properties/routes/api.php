<?php

use Illuminate\Support\Facades\Route;
use Modules\Properties\Http\Controllers\PropertyApiController;

Route::middleware(['api'])->group(function () {
    Route::get('listings/{listing}/properties', [PropertyApiController::class, 'index']);
    Route::get('listings/{listing}/properties/{property}', [PropertyApiController::class, 'show']);
    Route::get('property-types', [PropertyApiController::class, 'types']);
});
