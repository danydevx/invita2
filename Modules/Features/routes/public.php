<?php

use Illuminate\Support\Facades\Route;
use Modules\Features\Http\Controllers\Public\FeatureController;

Route::middleware(['public'])->prefix('b/{slug}')->name('public.')->group(function () {
    Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
});
