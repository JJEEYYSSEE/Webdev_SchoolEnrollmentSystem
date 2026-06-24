<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SchoolYearSeeder::class,   // school_years
            StrandSeeder::class,       // strands (STEM, ABM, HUMSS, GAS, TVL)
            SubjectSeeder::class,      // subjects master list
            UserSeeder::class,         // users + registrars + students
            SectionSeeder::class,      // sections (needs strands + school_years)
            SectionSubjectSeeder::class, // section → subject mapping
            EnrollmentSeeder::class,   // enrollments + enrollment_subjects snapshot
            Grade12ScenarioSeeder::class, // G12 students, varied section fills + schedules
            GradeSeeder::class,           // encoded grades + G12 past Grade 11 records
        ]);
    }
}
