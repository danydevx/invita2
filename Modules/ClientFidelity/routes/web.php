<?php

use Illuminate\Support\Facades\Route;
use Modules\ClientFidelity\Http\Controllers\Member\ClientFidelityCardController;
use Modules\ClientFidelity\Http\Controllers\Member\FidelityRewardController;
use Modules\ClientFidelity\Http\Controllers\Public\FidelityCardController;

Route::prefix('member/listings/{listing}/fidelity-cards')->middleware(['auth', 'verified', 'active', 'role:superadmin|admin|member'])->group(function () {
    Route::get('/', [ClientFidelityCardController::class, 'index'])->name('member.listings.fidelity-cards.index');
    Route::get('/create', [ClientFidelityCardController::class, 'create'])->name('member.listings.fidelity-cards.create');
    Route::post('/', [ClientFidelityCardController::class, 'store'])->name('member.listings.fidelity-cards.store');
    Route::get('/history', [ClientFidelityCardController::class, 'history'])->name('member.listings.fidelity-cards.history');
    Route::post('/bulk-delete', [ClientFidelityCardController::class, 'bulkDelete'])->name('member.listings.fidelity-cards.bulk-delete');
    Route::post('/scan-by-code', [ClientFidelityCardController::class, 'scanByCode'])->name('member.listings.fidelity-cards.scan-by-code');
    Route::get('/scan-view', [ClientFidelityCardController::class, 'scanView'])->name('member.listings.fidelity-cards.scan-view');
    Route::get('/{card}', [ClientFidelityCardController::class, 'show'])->name('member.listings.fidelity-cards.show');
    Route::get('/{card}/edit', [ClientFidelityCardController::class, 'edit'])->name('member.listings.fidelity-cards.edit');
    Route::put('/{card}', [ClientFidelityCardController::class, 'update'])->name('member.listings.fidelity-cards.update');
    Route::delete('/{card}', [ClientFidelityCardController::class, 'destroy'])->name('member.listings.fidelity-cards.destroy');
    Route::post('/{card}/scan', [ClientFidelityCardController::class, 'scan'])->name('member.listings.fidelity-cards.scan');
    Route::post('/{card}/reset', [ClientFidelityCardController::class, 'reset'])->name('member.listings.fidelity-cards.reset');
});

Route::prefix('member/listings/{listing}/fidelity-rewards')->middleware(['auth', 'verified', 'active', 'role:superadmin|admin|member'])->group(function () {
    Route::get('/', [FidelityRewardController::class, 'index'])->name('member.listings.fidelity-rewards.index');
    Route::get('/create', [FidelityRewardController::class, 'create'])->name('member.listings.fidelity-rewards.create');
    Route::post('/', [FidelityRewardController::class, 'store'])->name('member.listings.fidelity-rewards.store');
    Route::get('/{reward}/edit', [FidelityRewardController::class, 'edit'])->name('member.listings.fidelity-rewards.edit');
    Route::put('/{reward}', [FidelityRewardController::class, 'update'])->name('member.listings.fidelity-rewards.update');
    Route::delete('/{reward}', [FidelityRewardController::class, 'destroy'])->name('member.listings.fidelity-rewards.destroy');
});

Route::get('/b/{slug}/fidelity/{publicCode}', [FidelityCardController::class, 'show'])->name('public.fidelity.card');
