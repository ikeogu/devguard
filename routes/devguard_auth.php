<?php

use Illuminate\Support\Facades\Route;
use Emmanuelikeogu\DevGuard\Http\Controllers\Auth\AuthenticatedSessionController as LoginController;
use Emmanuelikeogu\DevGuard\Http\Middleware\HandleInertiaRequests as DevGuardInertiaMiddleware;
use Emmanuelikeogu\DevGuard\Http\Middleware\RedirectIfDevUserAuthenticated;
use Inertia\Inertia;

Route::middleware(['web','redirect.if.dev_user', 'guest:dev_user', DevGuardInertiaMiddleware::class])
    ->prefix('dev')
    ->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('it:login');
        Route::post('login', [LoginController::class, 'store'])->name('login');
    });

// Authenticated routes (must be logged into dev_user)
Route::middleware(['web', 'auth:dev_user', DevGuardInertiaMiddleware::class])
    ->prefix('dev')
    ->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('it:logout');

        Route::get('dashboard', fn () => Inertia::render('Dashboard'))
            ->name('it:dashboard');
    });