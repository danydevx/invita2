<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingAiChatbot\Http\Controllers\Public\ChatController;
use Modules\ListingAiChatbot\Http\Controllers\Public\WidgetController;
use Modules\ListingAiChatbot\Http\Controllers\Api\WidgetApiController;

Route::middleware(['web'])
    ->prefix('m/{slug}/ai-chatbot')
    ->name('minisite.listing-aichatbot.')
    ->group(function () {
        Route::post('/chat', [ChatController::class, 'chat'])->name('chat');
        Route::post('/stream-chat', [ChatController::class, 'streamChat'])->name('stream-chat');
        Route::post('/capture-lead', [ChatController::class, 'captureLead'])->name('capture-lead');
        Route::get('/settings', [ChatController::class, 'getSettings'])->name('settings');
        Route::get('/conversation', [ChatController::class, 'conversation'])->name('conversation');
    });

Route::middleware(['web'])
    ->prefix('api/widget/{public_key}')
    ->name('widget.')
    ->group(function () {
        Route::get('/widget.js', [WidgetController::class, 'serveWidget'])->name('serve');
        Route::get('/settings', [WidgetApiController::class, 'settings'])->name('settings');
        Route::post('/chat', [WidgetApiController::class, 'chat'])->name('chat');
        Route::post('/event', [WidgetApiController::class, 'event'])->name('event');
    });
