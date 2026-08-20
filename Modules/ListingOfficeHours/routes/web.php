<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingOfficeHours\Http\Controllers\Member\ScheduleController;

Route::middleware(['auth', 'active'])
    ->prefix('member/businesses/{business}/locations/{location}/schedules')
    ->name('member.businesses.locations.schedules.')
    ->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/create', [ScheduleController::class, 'create'])->name('create');
        Route::post('/', [ScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}/edit', [ScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('update');
        Route::post('/{schedule}/clone', [ScheduleController::class, 'clone'])->name('clone');
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('destroy');
    });

Route::middleware(['auth', 'active'])
    ->prefix('member/businesses/{business}/office-hours')
    ->name('member.businesses.office-hours.')
    ->group(function () {
        Route::get('/', [ScheduleController::class, 'indexAll'])->name('index');
    });