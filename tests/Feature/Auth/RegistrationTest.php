<?php

namespace Tests\Feature\Auth;

use App\Models\Strand;
use App\Models\User;
use Database\Seeders\StrandSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->seed(StrandSeeder::class);

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_students_can_register_and_receive_a_verification_email(): void
    {
        Notification::fake();
        $this->seed(StrandSeeder::class);
        $strand = Strand::firstOrFail();

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => 'test@example.com',
            'strand_id'  => $strand->id,
            'grade_level' => '11',
            'password'   => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);

        $response->assertRedirect(route('student.showDashboard'));
    }
}
