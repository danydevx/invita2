<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingContactForm\Http\Controllers\ListingContactFormController;

Route::middleware(['auth:sanctum'])->prefix('api/v1')->name('api.listingcontactform.')->group(function () {
    Route::get('/contact-forms/{shortcode}', [ListingContactFormController::class, 'show'])->name('show');
});
