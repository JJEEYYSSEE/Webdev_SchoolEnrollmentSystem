<?php

namespace Tests\Feature;

use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Strand;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EnrollmentRequirementTest extends TestCase
{
    use RefreshDatabase;

    private Strand $strand;
    private SchoolYear $sy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strand = Strand::create(['strand_code' => 'STEM', 'strand_name' => 'Science']);
        $this->sy = SchoolYear::create([
            'year_label' => '2026-2027', 'is_active' => true,
            'active_semester' => '1st', 'is_enrollment_open' => true,
        ]);
    }

    private function section(string $grade): Section
    {
        return Section::create([
            'strand_id' => $this->strand->id, 'school_year_id' => $this->sy->id,
            'grade_level' => $grade, 'semester' => '1st',
            'section_name' => 'A', 'time_period' => 'AM', 'max_capacity' => 40,
        ]);
    }

    private function student(string $grade): User
    {
        $user = User::factory()->create([
            'role' => 'student', 'school_id' => '2026-'.fake()->unique()->numerify('#####'),
            'must_change_password' => false,
        ]);
        Student::create([
            'user_id' => $user->id, 'student_number' => $user->school_id,
            'first_name' => 'S', 'last_name' => 'T',
            'strand_id' => $this->strand->id, 'grade_level' => $grade,
        ]);

        return $user;
    }

    public function test_grade12_must_upload_requirements_to_enroll(): void
    {
        $section = $this->section('12');
        $user    = $this->student('12');

        $this->actingAs($user)
            ->post(route('student.postEnrollForm'), ['section_id' => $section->id])
            ->assertSessionHasErrors(['documents.sf9', 'documents.photo']);

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_grade12_enrolls_with_requirements(): void
    {
        Storage::fake('public');
        $section = $this->section('12');
        $user    = $this->student('12');

        $this->actingAs($user)
            ->post(route('student.postEnrollForm'), [
                'section_id' => $section->id,
                'documents'  => [
                    'sf9'   => UploadedFile::fake()->create('sf9.pdf', 100, 'application/pdf'),
                    'photo' => UploadedFile::fake()->image('photo.jpg'),
                ],
            ])
            ->assertRedirect(route('student.showEnrollStatus'));

        $this->assertDatabaseHas('enrollments', ['student_id' => $user->student->id, 'status' => 'pending']);
        $this->assertDatabaseCount('enrollment_documents', 2);
    }

    public function test_grade11_enrolls_without_documents(): void
    {
        $section = $this->section('11');
        $user    = $this->student('11');

        $this->actingAs($user)
            ->post(route('student.postEnrollForm'), ['section_id' => $section->id])
            ->assertRedirect(route('student.showEnrollStatus'));

        $this->assertDatabaseHas('enrollments', ['student_id' => $user->student->id, 'status' => 'pending']);
        $this->assertDatabaseCount('enrollment_documents', 0);
    }
}
