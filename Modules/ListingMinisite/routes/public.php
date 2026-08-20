<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingMinisite\Http\Controllers\Public\ListingMinisiteController;

Route::middleware(['web'])
    ->prefix('m')
    ->name('minisite.')
    ->group(function () {
        Route::get('/{slug}', [ListingMinisiteController::class, 'show'])->name('show');
        Route::get('/{slug}/servicios', [ListingMinisiteController::class, 'services'])->name('services');
        Route::get('/{slug}/servicios/{serviceSlug}', [ListingMinisiteController::class, 'serviceDetail'])->name('service.detail');
        Route::get('/{slug}/productos', [ListingMinisiteController::class, 'products'])->name('products');
        Route::get('/{slug}/productos/{productSlug}', [ListingMinisiteController::class, 'productDetail'])->name('product.detail');
        Route::get('/{slug}/galeria', [ListingMinisiteController::class, 'gallery'])->name('gallery');
        Route::get('/{slug}/citas', [ListingMinisiteController::class, 'appointments'])->name('appointments');
        Route::get('/{slug}/promociones', [ListingMinisiteController::class, 'promotions'])->name('promotions');
        Route::get('/{slug}/promociones/{promotionSlug}', [ListingMinisiteController::class, 'promotionDetail'])->name('promotion.detail');
        Route::get('/{slug}/ubicaciones', [ListingMinisiteController::class, 'locations'])->name('locations');
        Route::get('/{slug}/resenas', [ListingMinisiteController::class, 'reviews'])->name('reviews');
        Route::get('/{slug}/preguntas-frecuentes', [ListingMinisiteController::class, 'faqs'])->name('faqs');
        Route::get('/{slug}/contacto', [ListingMinisiteController::class, 'contact'])->name('contact');
        Route::get('/{slug}/menu', [ListingMinisiteController::class, 'menu'])->name('menu');
        Route::get('/{slug}/propiedades', [ListingMinisiteController::class, 'properties'])->name('properties');
        Route::get('/{slug}/propiedades/{propertySlug}', [ListingMinisiteController::class, 'propertyDetail'])->name('property.detail');
    });