<?php

use Illuminate\Support\Facades\Route;
use Modules\BusinessModules\Http\Controllers\BusinessModulesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('businessmodules', BusinessModulesController::class)->names('businessmodules');
});
