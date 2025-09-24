<?php

use Illuminate\Support\Facades\Route;
use Emmanuelikeogu\DevGuard\Http\Controllers\Auth\AuthenticatedSessionController as LoginController;
use Emmanuelikeogu\DevGuard\Http\Controllers\Auth\PasswordResetController;

Route::middleware('guest')->prefix('dev')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('dev.login');
    Route::post('login', [LoginController::class, 'store']);
});
Route::middleware('auth.dev_user')->prefix('dev')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('dev.logout');
   /*  Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])->name('dev.password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])->name('dev.password.update'); */
});