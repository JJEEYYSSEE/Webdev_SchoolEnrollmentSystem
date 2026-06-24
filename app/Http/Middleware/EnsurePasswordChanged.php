<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    // Make students still on the system default password set a new one first.
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password
            && ! $request->routeIs('password.first')
            && ! $request->routeIs('password.first.update')
            && ! $request->routeIs('logout')) {
            return redirect()->route('password.first');
        }

        return $next($request);
    }
}
