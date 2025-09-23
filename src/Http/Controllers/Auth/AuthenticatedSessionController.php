<?php

namespace Emmanuelikeogu\DevGuard\Http\Controllers\Auth;

use Emmanuelikeogu\DevGuard\Http\Controllers\Controller;
use Emmanuelikeogu\DevGuard\Http\Requests\Auth\LoginRequest as AuthLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('dev_user');
    }

    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(AuthLoginRequest $request): RedirectResponse
    {

        $request->authenticate();

        Auth::guard('dev_user')->login(Auth::guard('dev_user')->user());

        $request->session()->regenerate();

        return redirect()->intended(route('it:dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
