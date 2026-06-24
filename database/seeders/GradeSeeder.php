<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\SemesterRecord;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    // Grade 12 students' PAST Grade 11 records (1st + 2nd sem) — full section +
    // graded subjects so My Records can show a breakdown. The current semester
    // stays ungraded (they're still enrolling).
    public function run(): void
    {
        $testers = ['2026-11900', '2025-12900', '2025-12901'];
        $pastSy  = SchoolYear::where('year_label', '2025-2026')->first();
        if (! $pastSy) {
            return;
        }

        $core       = Subject::where('subject_code', 'like', 'CORE-%')->pluck('id')->all();
        $strandSubs = fn (?string $code) => $code
            ? Subject::where('subject_code', 'like', $code.'-%')->pluck('id')->all()
            : [];

        DB::transaction(function () use ($testers, $pastSy, $core, $strandSubs) {
            $g12 = Student::with('strand')
                ->where('grade_level', '12')
                ->whereNotIn('student_number', $testers)
                ->get();

            $sectionCache = [];

            // Resolve (or create) a past Grade 11 section for a strand + semester.
            $sectionFor = function (Student $student, string $sem) use (&$sectionCache, $pastSy, $core, $strandSubs) {
                $key = $student->strand_id.'-'.$sem;
                if (isset($sectionCache[$key])) {
                    return $sectionCache[$key];
                }

                $name = $sem === '1st' ? 'Kasipagan' : 'Katapangan';
                $section = Section::firstOrCreate(
                    [
                        'strand_id'      => $student->strand_id,
                        'school_year_id' => $pastSy->id,
                        'grade_level'    => '11',
                        'semester'       => $sem,
                        'section_name'   => $name,
                    ],
                    ['time_period' => 'AM', 'max_capacity' => 50],
                );

                $subjects = array_values(array_unique(array_merge($core, $strandSubs(optional($student->strand)->strand_code))));
                $section->subjects()->syncWithoutDetaching($subjects);

                return $sectionCache[$key] = ['section' => $section, 'subjects' => $subjects];
            };

            foreach ($g12 as $student) {
                foreach (['1st', '2nd'] as $sem) {
                    ['section' => $section, 'subjects' => $subjects] = $sectionFor($student, $sem);

                    $enrollment = Enrollment::create([
                        'student_id'   => $student->id,
                        'section_id'   => $section->id,
                        'status'       => 'approved',
                        'submitted_at' => now(),
                        'reviewed_at'  => now(),
                    ]);

                    $rows   = [];
                    $grades = [];
                    foreach ($subjects as $subjectId) {
                        $grade    = $this->randomGrade();
                        $grades[] = $grade;
                        $rows[]   = [
                            'enrollment_id' => $enrollment->id,
                            'subject_id'    => $subjectId,
                            'grade'         => $grade,
                            'status'        => $grade >= 75 ? 'passed' : 'failed',
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ];
                    }
                    DB::table('enrollment_subjects')->insert($rows);

                    SemesterRecord::updateOrCreate(
                        ['student_id' => $student->id, 'school_year_id' => $pastSy->id, 'semester' => $sem],
                        ['gpa' => round(array_sum($grades) / count($grades), 2), 'is_locked' => true],
                    );
                }
            }
        });
    }

    // Mostly high (80s–90s), with the occasional dip toward the 70s.
    private function randomGrade(): int
    {
        return mt_rand(1, 6) === 1 ? mt_rand(72, 84) : mt_rand(85, 97);
    }
}
