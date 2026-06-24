<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class FirstPasswordController extends Controller
{
    // forced password change on first login with the default password
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->user()->must_change_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.first-password');
    }

    // save the new password and clear the flag
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $user->password = $validated['password'];   // hashed by the cast
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('dashboard')->with('status', 'password-changed');
    }
}
