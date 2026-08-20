<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingMinisite\Http\Controllers\Member\ListingMinisiteController;
use Modules\ListingMinisite\Http\Controllers\Member\ListingMinisiteSectionController;

Route::middleware(['auth', 'verified', 'active', 'role:member'])
    ->prefix('member/listings/{listing}/minisite')
    ->name('member.listings.minisite.')
    ->group(function () {
        Route::get('/', [ListingMinisiteController::class, 'index'])->name('index');
        Route::post('/', [ListingMinisiteController::class, 'store'])->name('store');
        Route::put('/', [ListingMinisiteController::class, 'update'])->name('update');

        Route::get('/sections', [ListingMinisiteSectionController::class, 'index'])->name('sections.index');
        Route::get('/sections/create', [ListingMinisiteSectionController::class, 'create'])->name('sections.create');
        Route::post('/sections', [ListingMinisiteSectionController::class, 'store'])->name('sections.store');
        Route::get('/sections/{section}/edit', [ListingMinisiteSectionController::class, 'edit'])->name('sections.edit');
        Route::put('/sections/{section}', [ListingMinisiteSectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [ListingMinisiteSectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('/sections/reorder', [ListingMinisiteSectionController::class, 'reorder'])->name('sections.reorder');
    });
