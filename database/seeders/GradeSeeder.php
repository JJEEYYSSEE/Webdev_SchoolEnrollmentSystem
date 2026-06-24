<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\SemesterRecord;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    // Encode grades (100-point: 60 lowest, 90+ high) for seeded students,
    // plus Grade 12 students' past Grade 11 records (1st + 2nd sem).
    public function run(): void
    {
        $testers = ['2026-11900', '2025-12900', '2025-12901'];
        $pastSy  = DB::table('school_years')->where('year_label', '2025-2026')->value('id');

        DB::transaction(function () use ($testers, $pastSy) {
            // Grade each approved enrollment's subjects + finalize that semester's average.
            $enrollments = Enrollment::where('status', 'approved')
                ->whereHas('student', fn ($q) => $q->whereNotIn('student_number', $testers))
                ->with(['section', 'enrollmentSubjects'])
                ->get();

            foreach ($enrollments as $enrollment) {
                $grades = [];

                foreach ($enrollment->enrollmentSubjects as $es) {
                    $grade = $this->randomGrade();
                    $es->update([
                        'grade'  => $grade,
                        'status' => $grade >= 75 ? 'passed' : 'failed',
                    ]);
                    $grades[] = $grade;
                }

                if ($grades) {
                    SemesterRecord::updateOrCreate(
                        [
                            'student_id'     => $enrollment->student_id,
                            'school_year_id' => $enrollment->section->school_year_id,
                            'semester'       => $enrollment->section->semester,
                        ],
                        ['gpa' => round(array_sum($grades) / count($grades), 2), 'is_locked' => true],
                    );
                }
            }

            // Grade 12: past Grade 11 records (both semesters of 2025-2026).
            if ($pastSy) {
                $g12 = Student::where('grade_level', '12')
                    ->whereNotIn('student_number', $testers)
                    ->get();

                foreach ($g12 as $student) {
                    foreach (['1st', '2nd'] as $sem) {
                        SemesterRecord::updateOrCreate(
                            ['student_id' => $student->id, 'school_year_id' => $pastSy, 'semester' => $sem],
                            ['gpa' => round(mt_rand(8000, 9500) / 100, 2), 'is_locked' => true],
                        );
                    }
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
