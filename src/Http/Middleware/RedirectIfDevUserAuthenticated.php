<?php

namespace ZojaTech\DevGuard\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfDevUserAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (auth()->guard('dev_user')->check()) {

            return redirect()->route('it:dashboard');
        }

        return $next($request);
    }
}
