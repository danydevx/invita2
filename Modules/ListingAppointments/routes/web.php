<?php

use Illuminate\Support\Facades\Route;
use Modules\ListingAppointments\Http\Controllers\AppointmentsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('appointments', AppointmentsController::class)->names('appointments');
});
