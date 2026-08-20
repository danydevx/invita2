<?php

use Illuminate\Support\Facades\Route;
use Modules\Locations\Http\Controllers\Admin\LocationController;

Route::middleware(['auth', 'verified', 'active', 'role:admin|superadmin'])->prefix('admin/locations')->group(function () {
    Route::get('/countries', [LocationController::class, 'countriesIndex'])->name('admin.locations.countries.index');
    Route::post('/countries', [LocationController::class, 'countriesStore'])->name('admin.locations.countries.store');
    Route::put('/countries/{country}', [LocationController::class, 'countriesUpdate'])->name('admin.locations.countries.update');

    Route::get('/states', [LocationController::class, 'statesIndex'])->name('admin.locations.states.index');
    Route::post('/states', [LocationController::class, 'statesStore'])->name('admin.locations.states.store');
    Route::put('/states/{state}', [LocationController::class, 'statesUpdate'])->name('admin.locations.states.update');

    Route::get('/municipalities', [LocationController::class, 'municipalitiesIndex'])->name('admin.locations.municipalities.index');
    Route::post('/municipalities', [LocationController::class, 'municipalitiesStore'])->name('admin.locations.municipalities.store');
    Route::put('/municipalities/{municipality}', [LocationController::class, 'municipalitiesUpdate'])->name('admin.locations.municipalities.update');
});

Route::get('/api/v1/location-data/countries', [LocationController::class, 'getStates'])->name('api.location.countries');
Route::get('/api/v1/location-data/states/{countryCode}', [LocationController::class, 'getStates'])->name('api.location.states');
Route::get('/api/v1/location-data/municipalities/{stateCode}', [LocationController::class, 'getMunicipalities'])->name('api.location.municipalities');
