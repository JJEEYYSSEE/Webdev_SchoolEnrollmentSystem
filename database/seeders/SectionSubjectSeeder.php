<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSubjectSeeder extends Seeder
{
    public function run(): void
    {
        // Helper: get subject id by code
        $sub = fn(string $code) => DB::table('subjects')->where('subject_code', $code)->value('id');

        // Helper: get section id by strand code + name + grade
        $sec = fn(string $strandCode, string $grade, string $name) => DB::table('sections')
            ->join('strands', 'strands.id', '=', 'sections.strand_id')
            ->where('strands.strand_code', $strandCode)
            ->where('sections.grade_level', $grade)
            ->where('sections.section_name', $name)
            ->value('sections.id');

        // Core subjects shared by all strands
        $coreG11 = [
            $sub('CORE-ORALCOM'),
            $sub('CORE-KOMPAN'),
            $sub('CORE-GENMATH'),
            $sub('CORE-EAPP'),
            $sub('CORE-PE1'),
            $sub('CORE-UCSP'),
            $sub('CORE-PR1'),
        ];

        // STEM-specific Grade 11 subjects
        $stemG11 = [
            $sub('STEM-PRECALC'),
            $sub('STEM-GENBIO1'),
            $sub('STEM-GENCHEM1'),
            $sub('STEM-GENPHY1'),
        ];

        // ABM-specific Grade 11 subjects
        $abmG11 = [
            $sub('ABM-FABM1'),
            $sub('ABM-FABM2'),
            $sub('ABM-ORGMAN'),
        ];

        // Map subjects to STEM Grade 11 sections
        $stemG11Sections = ['Kasipagan', 'Kapanatagan', 'Katapangan', 'Kabutihan'];
        foreach ($stemG11Sections as $sectionName) {
            $sectionId = $sec('STEM', '11', $sectionName);
            foreach (array_merge($coreG11, $stemG11) as $subjectId) {
                DB::table('section_subjects')->insert([
                    'section_id' => $sectionId,
                    'subject_id' => $subjectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Map subjects to ABM Grade 11 sections
        $abmG11Sections = ['Kasipagan', 'Kapanatagan', 'Katapangan', 'Kabutihan'];
        foreach ($abmG11Sections as $sectionName) {
            $sectionId = $sec('ABM', '11', $sectionName);
            foreach (array_merge($coreG11, $abmG11) as $subjectId) {
                DB::table('section_subjects')->insert([
                    'section_id' => $sectionId,
                    'subject_id' => $subjectId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
