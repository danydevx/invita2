<?php

use Illuminate\Support\Facades\Route;
use Modules\ClientFidelity\Http\Controllers\Member\ClientFidelityCardController;
use Modules\ClientFidelity\Http\Controllers\Public\FidelityCardController;

Route::prefix('member/listings/{listing}/fidelity-cards')->middleware(['auth', 'verified', 'active', 'role:superadmin|admin|member'])->group(function () {
    Route::get('/', [ClientFidelityCardController::class, 'index'])->name('member.listings.fidelity-cards.index');
    Route::get('/create', [ClientFidelityCardController::class, 'create'])->name('member.listings.fidelity-cards.create');
    Route::post('/', [ClientFidelityCardController::class, 'store'])->name('member.listings.fidelity-cards.store');
    Route::get('/{card}', [ClientFidelityCardController::class, 'show'])->name('member.listings.fidelity-cards.show');
    Route::get('/{card}/edit', [ClientFidelityCardController::class, 'edit'])->name('member.listings.fidelity-cards.edit');
    Route::put('/{card}', [ClientFidelityCardController::class, 'update'])->name('member.listings.fidelity-cards.update');
    Route::delete('/{card}', [ClientFidelityCardController::class, 'destroy'])->name('member.listings.fidelity-cards.destroy');
    Route::post('/{card}/scan', [ClientFidelityCardController::class, 'scan'])->name('member.listings.fidelity-cards.scan');
    Route::post('/{card}/reset', [ClientFidelityCardController::class, 'reset'])->name('member.listings.fidelity-cards.reset');
    Route::post('/bulk-delete', [ClientFidelityCardController::class, 'bulkDelete'])->name('member.listings.fidelity-cards.bulk-delete');
    Route::get('/{card}/scan-view', [ClientFidelityCardController::class, 'scanView'])->name('member.listings.fidelity-cards.scan-view');
    Route::post('/scan-by-code', [ClientFidelityCardController::class, 'scanByCode'])->name('member.listings.fidelity-cards.scan-by-code');
});

Route::get('/b/{slug}/fidelity/{publicCode}', [FidelityCardController::class, 'show'])->name('public.fidelity.card');
