<?php

use Illuminate\Support\Facades\Route;
use Modules\Minisite\Http\Controllers\Public\MinisiteController;

Route::middleware(['web'])
    ->prefix('m')
    ->name('minisite.')
    ->group(function () {
        Route::get('/{slug}', [MinisiteController::class, 'show'])->name('show');
        Route::get('/{slug}/servicios', [MinisiteController::class, 'services'])->name('services');
        Route::get('/{slug}/servicios/{serviceSlug}', [MinisiteController::class, 'serviceDetail'])->name('service.detail');
        Route::get('/{slug}/productos', [MinisiteController::class, 'products'])->name('products');
        Route::get('/{slug}/productos/{productSlug}', [MinisiteController::class, 'productDetail'])->name('product.detail');
        Route::get('/{slug}/galeria', [MinisiteController::class, 'gallery'])->name('gallery');
        Route::get('/{slug}/citas', [MinisiteController::class, 'appointments'])->name('appointments');
        Route::get('/{slug}/promociones', [MinisiteController::class, 'promotions'])->name('promotions');
        Route::get('/{slug}/promociones/{promotionSlug}', [MinisiteController::class, 'promotionDetail'])->name('promotion.detail');
        Route::get('/{slug}/ubicaciones', [MinisiteController::class, 'locations'])->name('locations');
        Route::get('/{slug}/resenas', [MinisiteController::class, 'reviews'])->name('reviews');
        Route::get('/{slug}/preguntas-frecuentes', [MinisiteController::class, 'faqs'])->name('faqs');
        Route::get('/{slug}/contacto', [MinisiteController::class, 'contact'])->name('contact');
        Route::get('/{slug}/menu', [MinisiteController::class, 'menu'])->name('menu');
        Route::get('/{slug}/propiedades', [MinisiteController::class, 'properties'])->name('properties');
        Route::get('/{slug}/propiedades/{propertySlug}', [MinisiteController::class, 'propertyDetail'])->name('property.detail');
    });
