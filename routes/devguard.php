<?php

use Emmanuelikeogu\DevGuard\Http\Controllers\MonitorController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware('auth.dev_user')->group(function () {

    Route::get('/dashboard', [MonitorController::class, 'index'])->withView('devguard::layouts.app')->name('it:dashboard');
});