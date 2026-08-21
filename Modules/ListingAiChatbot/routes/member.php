<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingAiChatbot\Http\Controllers\Member\AiChatbotController;

Route::middleware(['auth', 'verified', 'active', 'role:member'])
    ->prefix('member/listings/{listing}/ai-chatbot')
    ->name('member.listings.listing-aichatbot.')
    ->group(function () {
        Route::get('/', [AiChatbotController::class, 'index'])->name('index');
        Route::post('/settings', [AiChatbotController::class, 'saveSettings'])->name('settings');
        Route::post('/contexts', [AiChatbotController::class, 'storeContext'])->name('contexts.store');
        Route::put('/contexts/{contextId}', [AiChatbotController::class, 'updateContext'])->name('contexts.update');
        Route::delete('/contexts/{contextId}', [AiChatbotController::class, 'destroyContext'])->name('contexts.destroy');
        Route::post('/reindex', [AiChatbotController::class, 'reindex'])->name('reindex');
        Route::post('/extract-url', [AiChatbotController::class, 'extractUrl'])->name('extract-url');
        Route::get('/widget/settings', [AiChatbotController::class, 'widgetSettings'])->name('widget.settings');
        Route::post('/widget/settings', [AiChatbotController::class, 'saveWidgetSettings'])->name('widget.settings.save');
        Route::post('/widget/regenerate', [AiChatbotController::class, 'regenerateWidgetKey'])->name('widget.regenerate');
        Route::get('/history-json', [AiChatbotController::class, 'historyJson'])->name('history.json');
        Route::get('/history-json/{sessionId}', [AiChatbotController::class, 'historyDetailJson'])->name('history.detail.json');
    });
