<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_applicants_can_register_and_receive_a_verification_email(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name'  => 'User',
            'birthdate'  => '2009-01-01',
            'email'      => 'test@example.com',
            'password'   => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertNull($user->school_id);          // applicant, not yet admitted
        $this->assertNull($user->student);            // no student profile at registration
        Notification::assertSentTo($user, VerifyEmail::class);

        $response->assertRedirect(route('verification.notice'));
    }
}
