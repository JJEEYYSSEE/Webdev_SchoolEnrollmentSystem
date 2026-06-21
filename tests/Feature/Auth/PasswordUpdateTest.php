<?php

namespace Tests\Feature\Auth;

use App\Models\PasswordChangeOtp;
use App\Models\User;
use App\Notifications\PasswordChangeOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_change_request_emails_otp_without_changing_password(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('password.otp'));

        // Password is NOT applied yet — only a pending OTP exists.
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
        $this->assertDatabaseHas('password_change_otps', ['user_id' => $user->id]);
        Notification::assertSentTo($user, PasswordChangeOtpNotification::class);
    }

    public function test_correct_password_must_be_provided_to_request_change(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');

        $this->assertDatabaseCount('password_change_otps', 0);
    }

    public function test_valid_otp_applies_the_new_password(): void
    {
        $user = User::factory()->create();
        PasswordChangeOtp::create([
            'user_id'      => $user->id,
            'code'         => Hash::make('123456'),
            'new_password' => Hash::make('new-password'),
            'expires_at'   => now()->addMinutes(10),
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/password/otp', ['code' => '123456']);

        $response->assertRedirect(route('profile.edit'));
        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
        $this->assertDatabaseCount('password_change_otps', 0);
    }

    public function test_incorrect_otp_is_rejected_and_keeps_pending_change(): void
    {
        $user = User::factory()->create();
        PasswordChangeOtp::create([
            'user_id'      => $user->id,
            'code'         => Hash::make('123456'),
            'new_password' => Hash::make('new-password'),
            'expires_at'   => now()->addMinutes(10),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/password/otp')
            ->post('/password/otp', ['code' => '000000']);

        $response->assertSessionHasErrors('code');
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
        $this->assertDatabaseCount('password_change_otps', 1);
    }

    public function test_expired_otp_is_rejected_and_cleared(): void
    {
        $user = User::factory()->create();
        PasswordChangeOtp::create([
            'user_id'      => $user->id,
            'code'         => Hash::make('123456'),
            'new_password' => Hash::make('new-password'),
            'expires_at'   => now()->subMinute(),
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/password/otp', ['code' => '123456']);

        $response
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('code');

        $this->assertTrue(Hash::check('password', $user->refresh()->password));
        $this->assertDatabaseCount('password_change_otps', 0);
    }
}
