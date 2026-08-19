<?php

use Illuminate\Support\Facades\Route;
use Modules\ClientFidelity\Http\Controllers\Member\ClientFidelityCardController;
use Modules\ClientFidelity\Http\Controllers\Public\FidelityCardController;

Route::prefix('member/businesses/{business}/fidelity-cards')->middleware(['auth', 'verified', 'active', 'role:superadmin|admin|member'])->group(function () {
    Route::get('/', [ClientFidelityCardController::class, 'index'])->name('member.businesses.fidelity-cards.index');
    Route::get('/create', [ClientFidelityCardController::class, 'create'])->name('member.businesses.fidelity-cards.create');
    Route::post('/', [ClientFidelityCardController::class, 'store'])->name('member.businesses.fidelity-cards.store');
    Route::get('/{card}', [ClientFidelityCardController::class, 'show'])->name('member.businesses.fidelity-cards.show');
    Route::get('/{card}/edit', [ClientFidelityCardController::class, 'edit'])->name('member.businesses.fidelity-cards.edit');
    Route::put('/{card}', [ClientFidelityCardController::class, 'update'])->name('member.businesses.fidelity-cards.update');
    Route::delete('/{card}', [ClientFidelityCardController::class, 'destroy'])->name('member.businesses.fidelity-cards.destroy');
    Route::post('/{card}/scan', [ClientFidelityCardController::class, 'scan'])->name('member.businesses.fidelity-cards.scan');
    Route::post('/{card}/reset', [ClientFidelityCardController::class, 'reset'])->name('member.businesses.fidelity-cards.reset');
    Route::post('/bulk-delete', [ClientFidelityCardController::class, 'bulkDelete'])->name('member.businesses.fidelity-cards.bulk-delete');
    Route::get('/{card}/scan-view', [ClientFidelityCardController::class, 'scanView'])->name('member.businesses.fidelity-cards.scan-view');
    Route::post('/scan-by-code', [ClientFidelityCardController::class, 'scanByCode'])->name('member.businesses.fidelity-cards.scan-by-code');
});

Route::get('/b/{slug}/fidelity/{publicCode}', [FidelityCardController::class, 'show'])->name('public.fidelity.card');
