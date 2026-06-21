<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * Registration only creates the login (an applicant). The full DepEd
     * application form — and the student profile / School ID — come later,
     * after the email is verified and the registrar approves the application.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'birthdate'  => ['required', 'date', 'before:today'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'      => $validated['first_name'].' '.$validated['last_name'],
            'email'     => $validated['email'],
            'birthdate' => $validated['birthdate'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'student',
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Not yet verified — the verified middleware sends them to verify their
        // email before they can reach the application form.
        return redirect()->route('verification.notice');
    }
}
