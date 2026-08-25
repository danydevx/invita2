<?php

use Illuminate\Support\Facades\Route;
use Modules\Properties\Http\Controllers\Member\PropertyController;

Route::middleware(['auth', 'verified', 'active', 'role:member'])
    ->prefix('member/listings/{listing}/properties')
    ->name('member.listings.properties.')
    ->group(function () {
        Route::get('/', [PropertyController::class, 'index'])->name('index');
        Route::get('/create', [PropertyController::class, 'create'])->name('create');
        Route::post('/', [PropertyController::class, 'store'])->name('store');
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('edit');
        Route::put('/{property}', [PropertyController::class, 'update'])->name('update');
        Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [PropertyController::class, 'reorder'])->name('reorder');
        Route::post('/bulk-delete', [PropertyController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/{property}/duplicate', [PropertyController::class, 'duplicate'])->name('duplicate');
        Route::post('/{property}/change-status', [PropertyController::class, 'changeStatus'])->name('change-status');
        Route::get('/get-form-schema', [PropertyController::class, 'getFormSchema'])->name('get-form-schema');
    });
