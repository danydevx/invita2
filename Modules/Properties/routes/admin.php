<?php

use Illuminate\Support\Facades\Route;
use Modules\Properties\Http\Controllers\Admin\GeneralFieldController;
use Modules\Properties\Http\Controllers\Admin\GeneralFieldOptionController;
use Modules\Properties\Http\Controllers\Admin\GeneralFieldSectionController;
use Modules\Properties\Http\Controllers\Admin\PropertyAmenityController;
use Modules\Properties\Http\Controllers\Admin\PropertyFieldController;
use Modules\Properties\Http\Controllers\Admin\PropertyFieldSectionController;
use Modules\Properties\Http\Controllers\Admin\PropertyTypeAmenityController;
use Modules\Properties\Http\Controllers\Admin\PropertyTypeController;
use Modules\Properties\Http\Controllers\Admin\PropertyTypeFieldController;

Route::middleware(['auth', 'verified', 'active', 'role:superadmin|admin'])
    ->prefix('admin/modules/properties')
    ->name('admin.modules.properties.')
    ->group(function () {
        Route::get('/types', [PropertyTypeController::class, 'index'])->name('types.index');
        Route::post('/types', [PropertyTypeController::class, 'store'])->name('types.store');
        Route::put('/types/{propertyType}', [PropertyTypeController::class, 'update'])->name('types.update');
        Route::delete('/types/{propertyType}', [PropertyTypeController::class, 'destroy'])->name('types.destroy');

        Route::get('/types/{propertyType}/sections', [PropertyTypeFieldController::class, 'edit'])->name('types.sections');
        Route::post('/types/{propertyType}/assign-section', [PropertyTypeFieldController::class, 'assignSection'])->name('types.assign-section');
        Route::post('/types/{propertyType}/assign-sections', [PropertyTypeFieldController::class, 'assignSections'])->name('types.assign-sections');
        Route::delete('/types/{propertyType}/unassign-section/{generalFieldSection}', [PropertyTypeFieldController::class, 'unassignSection'])->name('types.unassign-section');
        Route::post('/types/{propertyType}/customizations', [PropertyTypeFieldController::class, 'updateCustomizations'])->name('types.customizations');
        Route::post('/types/{propertyType}/reorder-sections', [PropertyTypeFieldController::class, 'reorderSections'])->name('types.reorder-sections');

        Route::get('/types/{propertyType}/fields', [PropertyFieldController::class, 'index'])->name('types.fields.index');
        Route::post('/types/{propertyType}/fields', [PropertyFieldController::class, 'store'])->name('types.fields.store');
        Route::put('/types/{propertyType}/fields/{field}', [PropertyFieldController::class, 'update'])->name('types.fields.update');
        Route::delete('/types/{propertyType}/fields/{field}', [PropertyFieldController::class, 'destroy'])->name('types.fields.destroy');

        Route::post('/types/{propertyType}/sections', [PropertyFieldSectionController::class, 'store'])->name('types.sections.store');
        Route::put('/types/{propertyType}/sections/{section}', [PropertyFieldSectionController::class, 'update'])->name('types.sections.update');
        Route::delete('/types/{propertyType}/sections/{section}', [PropertyFieldSectionController::class, 'destroy'])->name('types.sections.destroy');

        Route::get('/general-sections', [GeneralFieldSectionController::class, 'index'])->name('general-sections.index');
        Route::post('/general-sections', [GeneralFieldSectionController::class, 'store'])->name('general-sections.store');
        Route::put('/general-sections/{generalFieldSection}', [GeneralFieldSectionController::class, 'update'])->name('general-sections.update');
        Route::delete('/general-sections/{generalFieldSection}', [GeneralFieldSectionController::class, 'destroy'])->name('general-sections.destroy');
        Route::post('/general-sections/reorder', [GeneralFieldSectionController::class, 'reorder'])->name('general-sections.reorder');

        Route::get('/general-sections/{generalFieldSection}/fields', [GeneralFieldController::class, 'index'])->name('general-sections.fields.index');
        Route::post('/general-sections/{generalFieldSection}/fields', [GeneralFieldController::class, 'store'])->name('general-sections.fields.store');
        Route::get('/general-sections/{generalFieldSection}/fields/{generalField}/edit', [GeneralFieldController::class, 'edit'])->name('general-sections.fields.edit');
        Route::put('/general-sections/{generalFieldSection}/fields/{generalField}', [GeneralFieldController::class, 'update'])->name('general-sections.fields.update');
        Route::delete('/general-sections/{generalFieldSection}/fields/{generalField}', [GeneralFieldController::class, 'destroy'])->name('general-sections.fields.destroy');
        Route::post('/general-sections/{generalFieldSection}/fields/reorder', [GeneralFieldController::class, 'reorder'])->name('general-sections.fields.reorder');

        Route::post('/general-fields/{generalField}/options', [GeneralFieldOptionController::class, 'store'])->name('general-fields.options.store');
        Route::put('/general-fields/{generalField}/options/{generalFieldOption}', [GeneralFieldOptionController::class, 'update'])->name('general-fields.options.update');
        Route::delete('/general-fields/{generalField}/options/{generalFieldOption}', [GeneralFieldOptionController::class, 'destroy'])->name('general-fields.options.destroy');

        Route::get('/amenities', [PropertyAmenityController::class, 'index'])->name('amenities.index');
        Route::post('/amenities', [PropertyAmenityController::class, 'store'])->name('amenities.store');
        Route::put('/amenities/{amenity}', [PropertyAmenityController::class, 'update'])->name('amenities.update');
        Route::delete('/amenities/{amenity}', [PropertyAmenityController::class, 'destroy'])->name('amenities.destroy');
        Route::post('/amenities/reorder', [PropertyAmenityController::class, 'reorder'])->name('amenities.reorder');

        Route::get('/types/{propertyType}/amenities', [PropertyTypeAmenityController::class, 'edit'])->name('types.amenities.edit');
        Route::post('/types/{propertyType}/amenities', [PropertyTypeAmenityController::class, 'sync'])->name('types.amenities.sync');
    });
