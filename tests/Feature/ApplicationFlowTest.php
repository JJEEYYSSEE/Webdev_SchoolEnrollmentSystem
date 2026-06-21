<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Registrar;
use App\Models\Strand;
use App\Models\User;
use Database\Seeders\StrandSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    private function applicant(): User
    {
        return User::factory()->create(['role' => 'student', 'school_id' => null]);
    }

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

        $this->actingAs($user)->get('/application'); // creates the draft

        // Step 1 — personal
        $this->actingAs($user)->post('/application/save', [
            'step' => 1, 'direction' => 'next',
            'first_name' => 'Juan', 'last_name' => 'Cruz', 'birthdate' => '2009-05-05',
            'sex' => 'Male', 'place_of_birth' => 'Manila', 'mother_tongue' => 'Tagalog',
            'mobile' => '09171234567',
            'current_address' => '1 St', 'current_barangay' => 'Brgy', 'current_city' => 'City', 'current_province' => 'Prov',
        ])->assertSessionHasNoErrors();

        // Step 2 — education
        $this->actingAs($user)->post('/application/save', [
            'step' => 2, 'direction' => 'next',
            'jhs_name' => 'Some JHS', 'jhs_year_graduated' => '2025', 'general_average' => '90',
            'elementary_name' => 'Some Elem', 'strand_id' => $strand->id,
        ])->assertSessionHasNoErrors();

        // Step 3 — documents
        $docs = collect(['sf10', 'sf9', 'good_moral', 'psa', 'photo'])
            ->mapWithKeys(fn ($t) => [$t => UploadedFile::fake()->create("$t.pdf", 100, 'application/pdf')])
            ->all();

        $this->actingAs($user)->post('/application/save', [
            'step' => 3, 'direction' => 'next', 'documents' => $docs,
        ])->assertSessionHasNoErrors();

        // Step 4 — submit
        $this->actingAs($user)->post('/application/submit')
            ->assertRedirect(route('application.status'));

        $this->assertDatabaseHas('applications', ['user_id' => $user->id, 'status' => 'pending']);
        $this->assertDatabaseCount('application_documents', 5);
    }

    public function test_registrar_can_return_application_for_compliance(): void
    {
        $registrarUser = User::factory()->create(['role' => 'registrar']);
        Registrar::create(['user_id' => $registrarUser->id, 'first_name' => 'Reg', 'last_name' => 'Istrar']);

        $applicant   = $this->applicant();
        $application = Application::create([
            'user_id' => $applicant->id,
            'status'  => 'pending',
            'first_name' => 'Juan', 'last_name' => 'Cruz',
            'submitted_at' => now(),
        ]);

        $this->actingAs($registrarUser)
            ->post(route('registrar.returnApplication', $application), ['remarks' => 'Blurry SF10, re-upload.'])
            ->assertRedirect(route('registrar.showApplications'));

        $this->assertDatabaseHas('applications', [
            'id' => $application->id, 'status' => 'invalid', 'remarks' => 'Blurry SF10, re-upload.',
        ]);
    }
}
