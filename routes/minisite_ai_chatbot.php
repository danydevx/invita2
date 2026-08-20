<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingAiChatbot\Http\Controllers\Public\ChatController;
use Modules\ListingAiChatbot\Http\Middleware\RateLimitChatbot;

Route::middleware(['web', RateLimitChatbot::class])
    ->prefix('m/{slug}/ai-chatbot')
    ->name('minisite.ai-chatbot.')
    ->group(function () {
        Route::post('/chat', [ChatController::class, 'chat'])->name('chat');
        Route::post('/stream-chat', [ChatController::class, 'streamChat'])->name('stream-chat');
    });

Route::middleware(['web'])
    ->prefix('m/{slug}/ai-chatbot')
    ->name('minisite.ai-chatbot.')
    ->group(function () {
        Route::get('/settings', [ChatController::class, 'getSettings'])->name('settings');
        Route::get('/conversation', [ChatController::class, 'conversation'])->name('conversation');
        Route::post('/capture-lead', [ChatController::class, 'captureLead'])->name('capture-lead');
    });
