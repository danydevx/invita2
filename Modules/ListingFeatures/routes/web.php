<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingFeatures\Http\Controllers\Admin\FeatureCategoryController;
use Modules\ListingFeatures\Http\Controllers\Admin\FeatureController;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('feature-categories', FeatureCategoryController::class)->names('feature-categories');
    Route::resource('features', FeatureController::class)->names('features');
});
