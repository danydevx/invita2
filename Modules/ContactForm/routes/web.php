<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Member\ContactFormController;
use App\Http\Controllers\Public\BusinessController;

Route::middleware(['auth', 'verified', 'active', 'role:member'])->prefix('member/businesses/{business}')->name('member.business.')->group(function () {
    Route::get('/contact-forms', [ContactFormController::class, 'index'])->name('contact-forms.index');
    Route::get('/contact-forms/create', [ContactFormController::class, 'create'])->name('contact-forms.create');
    Route::post('/contact-forms', [ContactFormController::class, 'store'])->name('contact-forms.store');
    Route::get('/contact-forms/{form}/edit', [ContactFormController::class, 'edit'])->name('contact-forms.edit');
    Route::put('/contact-forms/{form}', [ContactFormController::class, 'update'])->name('contact-forms.update');
    Route::delete('/contact-forms/{form}', [ContactFormController::class, 'destroy'])->name('contact-forms.destroy');
    Route::post('/contact-forms/{form}/fields', [ContactFormController::class, 'storeField'])->name('contact-forms.fields.store');
    Route::put('/contact-forms/{form}/fields/{field}', [ContactFormController::class, 'updateField'])->name('contact-forms.fields.update');
    Route::delete('/contact-forms/{form}/fields/{field}', [ContactFormController::class, 'destroyField'])->name('contact-forms.fields.destroy');
    Route::post('/contact-forms/{form}/reorder', [ContactFormController::class, 'reorder'])->name('contact-forms.reorder');
    Route::get('/contact-forms/{form}/submissions', [ContactFormController::class, 'submissions'])->name('contact-forms.submissions');
    Route::get('/contact-forms/export', [ContactFormController::class, 'export'])->name('contact-forms.export');
});

Route::middleware(['web'])->prefix('b/{slug}')->name('public.business.')->group(function () {
    Route::get('/form/{shortcode}', [BusinessController::class, 'formByShortcode'])->name('form.shortcode');
    Route::post('/form/{shortcode}', [BusinessController::class, 'storeFormByShortcode'])->name('form.shortcode.store');
});
