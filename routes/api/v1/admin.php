<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\BusinessController;
use App\Http\Controllers\Api\V1\Admin\UserController;

Route::prefix('admin')->middleware(['auth:api', 'role:superadmin|admin'])->group(function () {

    Route::get('/businesses', [BusinessController::class, 'index'])
        ->name('api.v1.admin.listings.index');

    Route::get('/listings/{listing}', [BusinessController::class, 'show'])
        ->name('api.v1.admin.listings.show');

    Route::get('/listings/{listing}/stats', [BusinessController::class, 'stats'])
        ->name('api.v1.admin.listings.stats');

    Route::get('/listings/{listing}/locations', [BusinessController::class, 'locations'])
        ->name('api.v1.admin.listings.locations');

    Route::get('/listings/{listing}/gallery', [BusinessController::class, 'gallery'])
        ->name('api.v1.admin.listings.gallery');

    Route::get('/listings/{listing}/faqs', [BusinessController::class, 'faqs'])
        ->name('api.v1.admin.listings.faqs');

    Route::get('/listings/{listing}/seo', [BusinessController::class, 'seo'])
        ->name('api.v1.admin.listings.seo');

    Route::get('/listings/{listing}/branding', [BusinessController::class, 'branding'])
        ->name('api.v1.admin.listings.branding');

    Route::get('/listings/{listing}/hero', [BusinessController::class, 'hero'])
        ->name('api.v1.admin.listings.hero');

    Route::get('/listings/{listing}/about', [BusinessController::class, 'about'])
        ->name('api.v1.admin.listings.about');

    Route::get('/listings/{listing}/services', [BusinessController::class, 'services'])
        ->name('api.v1.admin.listings.services');

    Route::get('/listings/{listing}/products', [BusinessController::class, 'products'])
        ->name('api.v1.admin.listings.products');

    Route::get('/listings/{listing}/reviews', [BusinessController::class, 'reviews'])
        ->name('api.v1.admin.listings.reviews');

    Route::get('/listings/{listing}/leads', [BusinessController::class, 'leads'])
        ->name('api.v1.admin.listings.leads');

    Route::get('/listings/{listing}/appointments', [BusinessController::class, 'appointments'])
        ->name('api.v1.admin.listings.appointments');

    Route::get('/listings/{listing}/appointment-slots', [BusinessController::class, 'appointmentSlots'])
        ->name('api.v1.admin.listings.appointment-slots');

    Route::get('/users', [UserController::class, 'index'])
        ->name('api.v1.admin.users.index');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('api.v1.admin.users.show');

    Route::get('/users/{user}/businesses', [UserController::class, 'businesses'])
        ->name('api.v1.admin.users.businesses');
});
