<?php

use Illuminate\Support\Facades\Route;
use Modules\Services\Http\Controllers\ServicesController;

Route::middleware(['auth', 'verified', 'employee'])->group(function () {
    Route::resource('services', ServicesController::class)->names('services');
});
