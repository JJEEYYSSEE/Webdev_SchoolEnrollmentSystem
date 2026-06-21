<?php

namespace Tests\Feature;

use App\Models\Registrar;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Strand;
use App\Models\Subject;
use App\Models\User;
use App\Services\ScheduleGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function sectionWithSubjects(int $subjectCount, string $period = 'AM'): Section
    {
        $strand = Strand::create(['strand_code' => 'STEM', 'strand_name' => 'Science']);
        $sy = SchoolYear::create([
            'year_label' => '2026-2027', 'is_active' => true,
            'active_semester' => '1st', 'is_enrollment_open' => true,
        ]);
        $section = Section::create([
            'strand_id' => $strand->id, 'school_year_id' => $sy->id,
            'grade_level' => '11', 'semester' => '1st',
            'section_name' => 'A', 'time_period' => $period, 'max_capacity' => 40,
        ]);

        $ids = collect(range(1, $subjectCount))->map(fn ($i) => Subject::create([
            'subject_code' => "SUB{$i}", 'subject_name' => "Subject {$i}", 'units' => 3,
        ])->id);
        $section->subjects()->sync($ids);

        return $section;
    }

    public function test_generator_assigns_unique_weekday_slots(): void
    {
        $section = $this->sectionWithSubjects(6);

        $count = app(ScheduleGenerator::class)->generate($section);
        $this->assertSame(6, $count);

        $section->load('subjects');

        // every subject got a day, time, and room
        $section->subjects->each(function ($s) {
            $this->assertNotNull($s->pivot->day_of_week);
            $this->assertNotNull($s->pivot->start_time);
            $this->assertNotNull($s->pivot->room);
        });

        // no two subjects share the same day + start time
        $slots = $section->subjects->map(fn ($s) => $s->pivot->day_of_week.' '.$s->pivot->start_time);
        $this->assertSame($slots->count(), $slots->unique()->count());

        // AM window starts at 07:30
        $this->assertTrue($section->subjects->contains(fn ($s) => $s->pivot->start_time === '07:30:00'));
    }

    public function test_pm_section_schedules_in_the_afternoon(): void
    {
        $section = $this->sectionWithSubjects(3, 'PM');

        app(ScheduleGenerator::class)->generate($section);
        $section->load('subjects');

        $section->subjects->each(fn ($s) => $this->assertGreaterThanOrEqual('13:00:00', $s->pivot->start_time));
    }

    public function test_registrar_can_generate_and_view_schedule(): void
    {
        $section = $this->sectionWithSubjects(5);

        $reg = User::factory()->create(['role' => 'registrar']);
        Registrar::create(['user_id' => $reg->id, 'first_name' => 'R', 'last_name' => 'G']);

        $this->actingAs($reg)
            ->post(route('registrar.sections.generateSchedule', $section->id))
            ->assertRedirect(route('registrar.sections.showSchedule', $section->id));

        $this->assertTrue($section->fresh()->hasSchedule());

        $this->actingAs($reg)
            ->get(route('registrar.sections.showSchedule', $section->id))
            ->assertOk();
    }
}
