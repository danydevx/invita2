<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\Member\OrderController as MemberOrderController;

Route::prefix('member/businesses/{business}')->middleware(['auth', 'verified', 'active', 'role:superadmin|admin|member'])->group(function () {
    Route::get('/orders', [MemberOrderController::class, 'index'])->name('member.orders.index');
    Route::get('/orders/settings', [MemberOrderController::class, 'settings'])->name('member.orders.settings');
    Route::post('/orders/settings', [MemberOrderController::class, 'updateSettings'])->name('member.orders.settings.update');
    Route::post('/orders/bulk-delete', [MemberOrderController::class, 'bulkDelete'])->name('member.orders.bulk-delete');
    Route::get('/orders/{order}', [MemberOrderController::class, 'show'])->name('member.orders.show');
    Route::post('/orders/{order}/status', [MemberOrderController::class, 'updateStatus'])->name('member.orders.update-status');
});
