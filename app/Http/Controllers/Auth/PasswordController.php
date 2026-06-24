<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordChangeOtp;
use App\Notifications\PasswordChangeOtpNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordController extends Controller
{
    // validate the change and email a 6-digit code; the new password waits until confirmed
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $code = (string) random_int(100000, 999999);

        // One pending change per user — discard any previous request.
        PasswordChangeOtp::where('user_id', $user->id)->delete();

        PasswordChangeOtp::create([
            'user_id'      => $user->id,
            'code'         => Hash::make($code),
            'new_password' => Hash::make($validated['password']),
            'expires_at'   => now()->addMinutes(10),
        ]);

        $user->notify(new PasswordChangeOtpNotification($code));

        return redirect()->route('password.otp')->with('status', 'password-otp-sent');
    }

    // OTP entry page (only while a code is pending)
    public function showOtp(Request $request): RedirectResponse|View
    {
        $otp = PasswordChangeOtp::where('user_id', $request->user()->id)->first();

        if (! $otp || $otp->isExpired()) {
            return redirect()->route('profile.edit');
        }

        return view('profile.confirm-password-otp');
    }

    // check the code and apply the held password
    public function confirmOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $otp = PasswordChangeOtp::where('user_id', $request->user()->id)->first();

        if (! $otp || $otp->isExpired()) {
            $otp?->delete();

            return redirect()->route('profile.edit')
                ->withErrors(['code' => 'Your code has expired. Please change your password again.']);
        }

        if (! Hash::check($request->code, $otp->code)) {
            return back()->withErrors(['code' => 'The code you entered is incorrect.']);
        }

        // new_password is already hashed; write it raw so the User model's
        // 'hashed' cast does not double-hash it.
        DB::table('users')
            ->where('id', $request->user()->id)
            ->update(['password' => $otp->new_password]);

        $otp->delete();

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }

    // send a fresh code for the pending change
    public function resendOtp(Request $request): RedirectResponse
    {
        $otp = PasswordChangeOtp::where('user_id', $request->user()->id)->first();

        if (! $otp) {
            return redirect()->route('profile.edit');
        }

        $code = (string) random_int(100000, 999999);

        $otp->update([
            'code'       => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        $request->user()->notify(new PasswordChangeOtpNotification($code));

        return back()->with('status', 'password-otp-sent');
    }
}
