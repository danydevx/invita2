<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingFeatures\Http\Controllers\Public\FeatureController;

Route::middleware(['public'])->prefix('b/{slug}')->name('public.')->group(function () {
    Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
});
