<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingGuests\Http\Controllers\Member\GuestController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/member/listings/{listing}/guests', [GuestController::class, 'index'])->name('member.listings.guests.index');
    Route::post('/member/listings/{listing}/guests', [GuestController::class, 'store'])->name('member.listings.guests.store');
    Route::put('/member/listings/{listing}/guests/{id}', [GuestController::class, 'update'])->name('member.listings.guests.update');
    Route::delete('/member/listings/{listing}/guests/{id}', [GuestController::class, 'destroy'])->name('member.listings.guests.destroy');
    Route::post('/member/listings/{listing}/guests/import', [GuestController::class, 'import'])->name('member.listings.guests.import');
});