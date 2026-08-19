<?php

use Illuminate\Support\Facades\Route;
use Modules\ClientFidelity\Http\Controllers\Public\FidelityCardController;

Route::get('/fidelity/{business}/find', [FidelityCardController::class, 'findByCode'])->name('api.fidelity.find');
