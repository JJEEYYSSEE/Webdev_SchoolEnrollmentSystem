<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Registrar;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Strand;
use App\Models\Student;
use App\Models\User;
use App\Notifications\ApplicationQualifiedNotification;
use App\Notifications\ApplicationReturnedNotification;
use App\Notifications\ApplicationWaitlistedNotification;
use Database\Seeders\StrandSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    private function applicant(): User
    {
        return User::factory()->create(['role' => 'student', 'school_id' => null]);
    }

    private function registrar(): User
    {
        $user = User::factory()->create(['role' => 'registrar']);
        Registrar::create(['user_id' => $user->id, 'first_name' => 'Reg', 'last_name' => 'Istrar']);

        return $user;
    }

    private function makeSection(Strand $strand, int $capacity): Section
    {
        $sy = SchoolYear::create([
            'year_label' => '2026-2027', 'is_active' => true,
            'active_semester' => '1st', 'is_enrollment_open' => true,
        ]);

        return Section::create([
            'strand_id' => $strand->id, 'school_year_id' => $sy->id,
            'grade_level' => '11', 'semester' => '1st',
            'section_name' => 'A', 'time_period' => 'AM', 'max_capacity' => $capacity,
        ]);
    }

    private function pendingApplication(User $user, Strand $strand): Application
    {
        return Application::create([
            'user_id' => $user->id, 'status' => 'pending',
            'first_name' => 'Juan', 'last_name' => 'Cruz', 'birthdate' => '2009-01-01',
            'mobile' => '09171234567',
            'current_address' => '1 St', 'current_barangay' => 'B', 'current_city' => 'C', 'current_province' => 'P',
            'strand_id' => $strand->id, 'grade_level' => '11', 'submitted_at' => now(),
        ]);
    }

    // ── Gate + wizard ────────────────────────────────────────────────────────

    public function test_unadmitted_student_is_redirected_to_application(): void
    {
        $user = $this->applicant();

        $this->actingAs($user)->get('/student/dashboard')
            ->assertRedirect(route('application.show'));
    }

    public function test_application_wizard_renders_for_applicant(): void
    {
        $user = $this->applicant();

        $this->actingAs($user)->get('/application')->assertOk();
        $this->assertDatabaseHas('applications', ['user_id' => $user->id, 'status' => 'draft']);
    }

    public function test_applicant_can_complete_and_submit_the_application(): void
    {
        Storage::fake('public');
        $this->seed(StrandSeeder::class);
        $user   = $this->applicant();
        $strand = Strand::firstOrFail();

        $this->actingAs($user)->get('/application');

        $this->actingAs($user)->post('/application/save', [
            'step' => 1, 'direction' => 'next',
            'first_name' => 'Juan', 'last_name' => 'Cruz', 'birthdate' => '2009-05-05',
            'sex' => 'Male', 'place_of_birth' => 'Manila', 'mother_tongue' => 'Tagalog',
            'mobile' => '09171234567',
            'current_address' => '1 St', 'current_barangay' => 'Brgy', 'current_city' => 'City', 'current_province' => 'Prov',
        ])->assertSessionHasNoErrors();

        $this->actingAs($user)->post('/application/save', [
            'step' => 2, 'direction' => 'next',
            'jhs_name' => 'Some JHS', 'jhs_year_graduated' => '2025', 'general_average' => '90',
            'elementary_name' => 'Some Elem', 'strand_id' => $strand->id,
        ])->assertSessionHasNoErrors();

        $docs = collect(['sf10', 'sf9', 'good_moral', 'psa', 'photo'])
            ->mapWithKeys(fn ($t) => [$t => UploadedFile::fake()->create("$t.pdf", 100, 'application/pdf')])
            ->all();

        $this->actingAs($user)->post('/application/save', [
            'step' => 3, 'direction' => 'next', 'documents' => $docs,
        ])->assertSessionHasNoErrors();

        $this->actingAs($user)->post('/application/submit')
            ->assertRedirect(route('application.status'));

        $this->assertDatabaseHas('applications', ['user_id' => $user->id, 'status' => 'pending']);
        $this->assertDatabaseCount('application_documents', 5);
    }

    // ── Registrar decisions ──────────────────────────────────────────────────

    public function test_registrar_can_return_application_for_compliance(): void
    {
        Notification::fake();
        $applicant   = $this->applicant();
        $application = Application::create([
            'user_id' => $applicant->id, 'status' => 'pending',
            'first_name' => 'Juan', 'last_name' => 'Cruz', 'submitted_at' => now(),
        ]);

        $this->actingAs($this->registrar())
            ->post(route('registrar.returnApplication', $application), ['remarks' => 'Blurry SF10, re-upload.'])
            ->assertRedirect(route('registrar.showApplications'));

        $this->assertDatabaseHas('applications', [
            'id' => $application->id, 'status' => 'invalid', 'remarks' => 'Blurry SF10, re-upload.',
        ]);
        Notification::assertSentTo($applicant, ApplicationReturnedNotification::class);
    }

    public function test_registrar_can_qualify_and_issue_school_id(): void
    {
        Notification::fake();
        $strand = Strand::create(['strand_code' => 'STEM', 'strand_name' => 'Science']);
        $this->makeSection($strand, 40);

        $applicant   = $this->applicant();
        $application = $this->pendingApplication($applicant, $strand);

        $this->actingAs($this->registrar())
            ->post(route('registrar.qualifyApplication', $application))
            ->assertRedirect(route('registrar.showApplications'));

        $applicant->refresh();
        $this->assertNotNull($applicant->school_id);
        $this->assertTrue($applicant->must_change_password);
        $this->assertDatabaseHas('students', ['user_id' => $applicant->id]);
        $this->assertDatabaseHas('applications', ['id' => $application->id, 'status' => 'qualified']);
        Notification::assertSentTo($applicant, ApplicationQualifiedNotification::class);
    }

    public function test_qualify_waitlists_when_no_slots_remain(): void
    {
        Notification::fake();
        $strand = Strand::create(['strand_code' => 'ABM', 'strand_name' => 'Business']);
        $this->makeSection($strand, 1);

        // fill the single seat
        $filler = User::factory()->create(['role' => 'student', 'school_id' => '2026-00001']);
        Student::create([
            'user_id' => $filler->id, 'student_number' => '2026-00001',
            'first_name' => 'F', 'last_name' => 'L', 'strand_id' => $strand->id, 'grade_level' => '11',
        ]);

        $applicant   = $this->applicant();
        $application = $this->pendingApplication($applicant, $strand);

        $this->actingAs($this->registrar())
            ->post(route('registrar.qualifyApplication', $application));

        $applicant->refresh();
        $this->assertNull($applicant->school_id);
        $this->assertDatabaseMissing('students', ['user_id' => $applicant->id]);
        $this->assertDatabaseHas('applications', ['id' => $application->id, 'status' => 'waitlisted']);
        Notification::assertSentTo($applicant, ApplicationWaitlistedNotification::class);
    }

    // ── Forced first-login password change ───────────────────────────────────

    public function test_admitted_student_with_default_password_is_forced_to_change(): void
    {
        $user = User::factory()->create([
            'role' => 'student', 'school_id' => '2026-07777', 'must_change_password' => true,
        ]);
        Student::create([
            'user_id' => $user->id, 'student_number' => '2026-07777',
            'first_name' => 'A', 'last_name' => 'B', 'grade_level' => '11',
        ]);

        $this->actingAs($user)->get('/student/dashboard')
            ->assertRedirect(route('password.first'));

        $this->actingAs($user)
            ->post(route('password.first.update'), [
                'password' => 'newpass123', 'password_confirmation' => 'newpass123',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertFalse($user->refresh()->must_change_password);
    }
}
