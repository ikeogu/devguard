<?php

use Emmanuelikeogu\DevGuard\Http\Controllers\MonitorController;
use Emmanuelikeogu\DevGuard\Http\Middleware\HandleInertiaRequests as DevGuardInertiaMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::middleware(['auth:dev_user', DevGuardInertiaMiddleware::class])
    ->prefix('dev')
    ->group(function () {
        Route::get('dashboard', fn () => Inertia::render('Dashboard'))
            ->name('it:dashboard');
    });
