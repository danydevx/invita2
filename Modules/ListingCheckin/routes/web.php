<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingCheckin\Http\Controllers\Member\CheckinController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/member/listings/{listing}/checkin', [CheckinController::class, 'index'])->name('member.listings.checkin.index');
    Route::post('/member/listings/{listing}/checkin', [CheckinController::class, 'store'])->name('member.listings.checkin.store');
    Route::delete('/member/listings/{listing}/checkin/{id}', [CheckinController::class, 'destroy'])->name('member.listings.checkin.destroy');
});