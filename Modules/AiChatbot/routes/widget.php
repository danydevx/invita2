<?php

use Illuminate\Support\Facades\Route;
use Modules\AiChatbot\Http\Controllers\Public\WidgetController;
use Modules\AiChatbot\Http\Controllers\Api\WidgetApiController;

Route::prefix('api/widget/{public_key}')->group(function () {
    Route::get('/widget.js', [WidgetController::class, 'serveWidget'])->name('widget.serve');
    Route::get('/settings', [WidgetApiController::class, 'settings'])->name('widget.settings');
    Route::post('/chat', [WidgetApiController::class, 'chat'])->name('widget.chat');
    Route::post('/event', [WidgetApiController::class, 'event'])->name('widget.event');
});
