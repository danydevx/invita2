<?php

use Illuminate\Support\Facades\Route;
use Modules\Features\Http\Controllers\Member\FeatureController;

Route::middleware(['auth', 'verified'])->prefix('member/businesses/{business}')->name('member.businesses.')->group(function () {
    Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
    Route::post('/features', [FeatureController::class, 'store'])->name('features.store');
    Route::post('/features/import/{feature}', [FeatureController::class, 'import'])->name('features.import');
    Route::put('/features/{feature}', [FeatureController::class, 'update'])->name('features.update');
    Route::delete('/features/{feature}', [FeatureController::class, 'destroy'])->name('features.destroy');
    Route::put('/features/reorder', [FeatureController::class, 'reorder'])->name('features.reorder');
    Route::put('/feature-assignments', [FeatureController::class, 'updateAssignment'])->name('feature-assignments.update');
    Route::delete('/feature-assignments/{assignment}', [FeatureController::class, 'removeAssignment'])->name('feature-assignments.remove');
});
