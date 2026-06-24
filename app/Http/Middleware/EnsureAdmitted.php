<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmitted
{
    // A student with no School ID is still an applicant — keep them in the application flow.
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'student' && ! $user->isAdmitted()) {
            return redirect()->route('application.show');
        }

        return $next($request);
    }
}
