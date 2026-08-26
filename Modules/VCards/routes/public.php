<?php

use Illuminate\Support\Facades\Route;
use Modules\VCards\Http\Controllers\Public\VCardPublicController;

Route::get('/v/{slug}', [VCardPublicController::class, 'show'])
    ->name('vcards.public.show');
Route::get('/v/{slug}/qr', [VCardPublicController::class, 'qr'])
    ->name('vcards.public.qr');
Route::get('/v/{slug}/download', [VCardPublicController::class, 'download'])
    ->name('vcards.public.download');
