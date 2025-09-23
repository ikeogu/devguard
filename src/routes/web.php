<?php

use Emmanuelikeogu\DevMonitoringGuard\Http\Controllers\MonitorController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['web', 'auth:dev'])->group(function () {
    Route::get('/dev/dashboard', fn () => Inertia::render('Dashboard'))
        ->name('dev.dashboard');
});
