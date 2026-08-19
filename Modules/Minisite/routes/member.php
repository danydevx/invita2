<?php

use Illuminate\Support\Facades\Route;
use Modules\Minisite\Http\Controllers\Member\MinisiteController;
use Modules\Minisite\Http\Controllers\Member\MinisiteSectionController;

Route::middleware(['auth', 'verified', 'active', 'role:member'])
    ->prefix('member/businesses/{business}/minisite')
    ->name('member.businesses.minisite.')
    ->group(function () {
        Route::get('/', [MinisiteController::class, 'index'])->name('index');
        Route::post('/', [MinisiteController::class, 'store'])->name('store');
        Route::put('/', [MinisiteController::class, 'update'])->name('update');

        Route::get('/sections', [MinisiteSectionController::class, 'index'])->name('sections.index');
        Route::get('/sections/create', [MinisiteSectionController::class, 'create'])->name('sections.create');
        Route::post('/sections', [MinisiteSectionController::class, 'store'])->name('sections.store');
        Route::get('/sections/{section}/edit', [MinisiteSectionController::class, 'edit'])->name('sections.edit');
        Route::put('/sections/{section}', [MinisiteSectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [MinisiteSectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('/sections/reorder', [MinisiteSectionController::class, 'reorder'])->name('sections.reorder');
    });
