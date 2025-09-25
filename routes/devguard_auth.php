<?php

use Illuminate\Support\Facades\Route;
use Emmanuelikeogu\DevGuard\Http\Controllers\Auth\AuthenticatedSessionController as LoginController;
use Emmanuelikeogu\DevGuard\Http\Controllers\Auth\PasswordResetController;
use Emmanuelikeogu\DevGuard\Http\Middleware\HandleInertiaRequests as DevGuardInertiaMiddleware;

Route::middleware(['guest','web', DevGuardInertiaMiddleware::class])->prefix('dev')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('it:login');
    Route::post('login', [LoginController::class, 'store'])->name('login');
});
Route::middleware('auth.dev_user', DevGuardInertiaMiddleware::class)->prefix('dev')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('dev.logout');
   /*  Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])->name('dev.password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('dev.password.update'); */
});