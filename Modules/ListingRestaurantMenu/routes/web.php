<?php

use Illuminate\Support\Facades\Route;
use Modules\Listings\Models\Listing;

Route::prefix('admin/listings/{listing}')->middleware(['auth', 'role:superadmin|admin'])->group(function () {
    Route::resource('menu-categories', \App\Http\Controllers\Admin\MenuCategoryController::class)->names([
        'index' => 'admin.menu.categories.index',
        'store' => 'admin.menu.categories.store',
        'update' => 'admin.menu.categories.update',
        'destroy' => 'admin.menu.categories.destroy',
    ]);

    Route::resource('menu-products', \App\Http\Controllers\Admin\MenuProductController::class)->names([
        'index' => 'admin.menu.products.index',
        'store' => 'admin.menu.products.store',
        'update' => 'admin.menu.products.update',
        'destroy' => 'admin.menu.products.destroy',
    ]);

    Route::post('menu-products/{product}/variants', [\App\Http\Controllers\Admin\MenuProductVariantController::class, 'store'])->name('admin.menu.products.variants.store');
    Route::put('menu-products/{product}/variants/{variant}', [\App\Http\Controllers\Admin\MenuProductVariantController::class, 'update'])->name('admin.menu.products.variants.update');
    Route::delete('menu-products/{product}/variants/{variant}', [\App\Http\Controllers\Admin\MenuProductVariantController::class, 'destroy'])->name('admin.menu.products.variants.destroy');

    Route::post('menu-products/{product}/images', [\App\Http\Controllers\Admin\MenuProductImageController::class, 'store'])->name('admin.menu.products.images.store');
    Route::put('menu-products/{product}/images/{image}', [\App\Http\Controllers\Admin\MenuProductImageController::class, 'update'])->name('admin.menu.products.images.update');
    Route::delete('menu-products/{product}/images/{image}', [\App\Http\Controllers\Admin\MenuProductImageController::class, 'destroy'])->name('admin.menu.products.images.destroy');
});

Route::prefix('member/listings/{listing}')->middleware(['auth', 'role:superadmin|admin|member'])->group(function () {
    Route::resource('menu-categories', \App\Http\Controllers\Member\MenuCategoryController::class)->names([
        'index' => 'member.menu.categories.index',
        'store' => 'member.menu.categories.store',
        'update' => 'member.menu.categories.update',
        'destroy' => 'member.menu.categories.destroy',
    ]);

    Route::resource('menu-products', \App\Http\Controllers\Member\MenuProductController::class)->names([
        'index' => 'member.menu.products.index',
        'store' => 'member.menu.products.store',
        'update' => 'member.menu.products.update',
        'destroy' => 'member.menu.products.destroy',
    ]);

    Route::post('menu-products/{product}/variants', [\App\Http\Controllers\Member\MenuProductVariantController::class, 'store'])->name('member.menu.products.variants.store');
    Route::put('menu-products/{product}/variants/{variant}', [\App\Http\Controllers\Member\MenuProductVariantController::class, 'update'])->name('member.menu.products.variants.update');
    Route::delete('menu-products/{product}/variants/{variant}', [\App\Http\Controllers\Member\MenuProductVariantController::class, 'destroy'])->name('member.menu.products.variants.destroy');

    Route::post('menu-products/{product}/images', [\App\Http\Controllers\Member\MenuProductImageController::class, 'store'])->name('member.menu.products.images.store');
    Route::put('menu-products/{product}/images/{image}', [\App\Http\Controllers\Member\MenuProductImageController::class, 'update'])->name('member.menu.products.images.update');
    Route::delete('menu-products/{product}/images/{image}', [\App\Http\Controllers\Member\MenuProductImageController::class, 'destroy'])->name('member.menu.products.images.destroy');

    Route::post('menu-products/{product}/clone', [\App\Http\Controllers\Member\MenuProductController::class, 'clone'])->name('member.menu.products.clone');
});

Route::get('{businessSlug}/menu', [\App\Http\Controllers\Public\MenuController::class, 'show'])->name('public.menu.show');
