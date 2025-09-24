<?php

namespace Emmanuelikeogu\DevGuard\Http\Middleware;

use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     */
    public function rootView($request): string
    {
        return 'devguard::layouts.app';   // instead of 'app'
    }
}
