<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\BusinessController;
use App\Http\Controllers\Api\V1\Admin\UserController;

Route::prefix('admin')->middleware(['auth:api', 'role:superadmin|admin'])->group(function () {

    Route::get('/businesses', [BusinessController::class, 'index'])
        ->name('api.v1.admin.businesses.index');

    Route::get('/businesses/{business}', [BusinessController::class, 'show'])
        ->name('api.v1.admin.businesses.show');

    Route::get('/businesses/{business}/stats', [BusinessController::class, 'stats'])
        ->name('api.v1.admin.businesses.stats');

    Route::get('/businesses/{business}/locations', [BusinessController::class, 'locations'])
        ->name('api.v1.admin.businesses.locations');

    Route::get('/businesses/{business}/gallery', [BusinessController::class, 'gallery'])
        ->name('api.v1.admin.businesses.gallery');

    Route::get('/businesses/{business}/faqs', [BusinessController::class, 'faqs'])
        ->name('api.v1.admin.businesses.faqs');

    Route::get('/businesses/{business}/seo', [BusinessController::class, 'seo'])
        ->name('api.v1.admin.businesses.seo');

    Route::get('/businesses/{business}/branding', [BusinessController::class, 'branding'])
        ->name('api.v1.admin.businesses.branding');

    Route::get('/businesses/{business}/hero', [BusinessController::class, 'hero'])
        ->name('api.v1.admin.businesses.hero');

    Route::get('/businesses/{business}/about', [BusinessController::class, 'about'])
        ->name('api.v1.admin.businesses.about');

    Route::get('/businesses/{business}/services', [BusinessController::class, 'services'])
        ->name('api.v1.admin.businesses.services');

    Route::get('/businesses/{business}/products', [BusinessController::class, 'products'])
        ->name('api.v1.admin.businesses.products');

    Route::get('/businesses/{business}/reviews', [BusinessController::class, 'reviews'])
        ->name('api.v1.admin.businesses.reviews');

    Route::get('/businesses/{business}/leads', [BusinessController::class, 'leads'])
        ->name('api.v1.admin.businesses.leads');

    Route::get('/businesses/{business}/appointments', [BusinessController::class, 'appointments'])
        ->name('api.v1.admin.businesses.appointments');

    Route::get('/businesses/{business}/appointment-slots', [BusinessController::class, 'appointmentSlots'])
        ->name('api.v1.admin.businesses.appointment-slots');

    Route::get('/users', [UserController::class, 'index'])
        ->name('api.v1.admin.users.index');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('api.v1.admin.users.show');

    Route::get('/users/{user}/businesses', [UserController::class, 'businesses'])
        ->name('api.v1.admin.users.businesses');
});
