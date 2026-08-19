<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\Public\OrderController as PublicOrderController;

Route::post('/orders', [PublicOrderController::class, 'store'])->name('api.orders.store');
