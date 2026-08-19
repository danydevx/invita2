<?php

use Illuminate\Support\Facades\Route;
use Modules\ContactForm\Http\Controllers\ContactFormController;

Route::middleware(['auth:sanctum'])->prefix('api/v1')->name('api.contactform.')->group(function () {
    Route::get('/contact-forms/{shortcode}', [ContactFormController::class, 'show'])->name('show');
});
