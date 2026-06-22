<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $sec = fn(string $strandCode, string $grade, string $name) => DB::table('sections')
            ->join('strands', 'strands.id', '=', 'sections.strand_id')
            ->where('strands.strand_code', $strandCode)
            ->where('sections.grade_level', $grade)
            ->where('sections.section_name', $name)
            ->value('sections.id');

        $student = fn(string $number) => DB::table('students')->where('student_number', $number)->value('id');
        $registrar = fn(string $email) => DB::table('registrars')
            ->join('users', 'users.id', '=', 'registrars.user_id')
            ->where('users.email', $email)
            ->value('registrars.id');

        $stemKasipagan    = $sec('STEM', '11', 'Kasipagan');
        $stemKapanatagan  = $sec('STEM', '11', 'Kapanatagan');
        $abmKasipagan     = $sec('ABM',  '11', 'Kasipagan');
        $reg1             = $registrar('registrar1@school.edu.ph');
        $reg2             = $registrar('registrar2@school.edu.ph');

        $enrollments = [
            // Juan -> STEM G11 Kasipagan, approved
            ['student_id' => $student('2026-00001'), 'section_id' => $stemKasipagan,   'status' => 'approved', 'remarks' => null,                               'approved_by' => $reg1, 'reviewed_at' => now()],
            // Maria -> STEM G11 Kasipagan, pending
            ['student_id' => $student('2026-00002'), 'section_id' => $stemKasipagan,   'status' => 'pending',  'remarks' => null,                               'approved_by' => null,  'reviewed_at' => null],
            // Pedro -> ABM G11 Kasipagan, approved
            ['student_id' => $student('2026-00003'), 'section_id' => $abmKasipagan,    'status' => 'approved', 'remarks' => null,                               'approved_by' => $reg1, 'reviewed_at' => now()],
            // Ana -> STEM G11 Kapanatagan, returned as invalid
            ['student_id' => $student('2026-00004'), 'section_id' => $stemKapanatagan, 'status' => 'invalid', 'remarks' => 'Incomplete requirements — missing Form 138', 'approved_by' => $reg2, 'reviewed_at' => now()],
            // Jose -> ABM G11 Kasipagan, pending
            ['student_id' => $student('2026-00005'), 'section_id' => $abmKasipagan,    'status' => 'pending',  'remarks' => null,                               'approved_by' => null,  'reviewed_at' => null],
        ];

        foreach ($enrollments as $e) {
            $enrollmentId = DB::table('enrollments')->insertGetId([
                'student_id'   => $e['student_id'],
                'section_id'   => $e['section_id'],
                'status'       => $e['status'],
                'remarks'      => $e['remarks'],
                'approved_by'  => $e['approved_by'],
                'submitted_at' => now()->subDays(rand(1, 5)),
                'reviewed_at'  => $e['reviewed_at'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // Auto-copy section_subjects into enrollment_subjects for approved enrollments
            if ($e['status'] === 'approved') {
                $subjects = DB::table('section_subjects')
                    ->where('section_id', $e['section_id'])
                    ->pluck('subject_id');

                foreach ($subjects as $subjectId) {
                    DB::table('enrollment_subjects')->insert([
                        'enrollment_id' => $enrollmentId,
                        'subject_id'    => $subjectId,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        }
    }
}
