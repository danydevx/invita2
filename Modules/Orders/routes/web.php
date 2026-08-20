<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\Member\OrderController as MemberOrderController;

Route::prefix('member/listings/{listing}/orders')->middleware(['auth', 'verified', 'active', 'role:superadmin|admin|member'])->group(function () {
    Route::get('/', [MemberOrderController::class, 'index'])->name('member.orders.index');
    Route::get('/settings', [MemberOrderController::class, 'settings'])->name('member.orders.settings');
    Route::post('/settings', [MemberOrderController::class, 'updateSettings'])->name('member.orders.settings.update');
    Route::post('/bulk-delete', [MemberOrderController::class, 'bulkDelete'])->name('member.orders.bulk-delete');
    Route::get('/{order}', [MemberOrderController::class, 'show'])->name('member.orders.show');
    Route::post('/{order}/status', [MemberOrderController::class, 'updateStatus'])->name('member.orders.update-status');
});
