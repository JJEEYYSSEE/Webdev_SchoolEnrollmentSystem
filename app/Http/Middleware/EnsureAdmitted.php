<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmitted
{
    /**
     * Keep not-yet-admitted students inside the application flow.
     *
     * A student without a School ID is still an applicant — bounce them to the
     * application form/status page until the registrar issues their School ID.
     * Other roles (registrar) pass through untouched.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'student' && ! $user->isAdmitted()) {
            return redirect()->route('application.show');
        }

        return $next($request);
    }
}
