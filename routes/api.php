<?php

use App\Http\Controllers\Public\BookingWidgetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Locations\Http\Controllers\Admin\LocationController;

Route::middleware(['api_key', 'throttle:api-key'])->group(function () {
    Route::get('/me', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'id' => $user?->id,
            'name' => $user?->name,
            'email' => $user?->email,
        ]);
    })->name('api.me');
});

Route::get('v1/location-data/countries', [LocationController::class, 'getCountries']);
Route::get('v1/location-data/states', [LocationController::class, 'getStates']);
Route::get('v1/location-data/states/{countryCode}', [LocationController::class, 'getStates']);
Route::get('v1/location-data/municipalities/{stateCode}', [LocationController::class, 'getMunicipalities']);

Route::prefix('book')->group(function () {
    Route::get('businesses/active', [BookingWidgetController::class, 'activeBusinesses'])
        ->name('api.book.businesses.active');

    Route::get('business/{businessSlug}/services', [BookingWidgetController::class, 'services'])
        ->name('api.book.services');

    Route::get('business/{businessSlug}/packages', [BookingWidgetController::class, 'packages'])
        ->name('api.book.packages');

    Route::get('business/{businessSlug}/slots', [BookingWidgetController::class, 'slots'])
        ->name('api.book.slots');

    Route::post('business/{businessSlug}', [BookingWidgetController::class, 'store'])
        ->name('api.book.store');
});

require __DIR__ . '/api/v1/admin.php';
