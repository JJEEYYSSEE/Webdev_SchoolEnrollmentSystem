<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('school_years')->insert([
            ['year_label' => '2025-2026', 'is_active' => false, 'active_semester' => '2nd', 'is_enrollment_open' => false, 'created_at' => now(), 'updated_at' => now()],
            ['year_label' => '2026-2027', 'is_active' => true,  'active_semester' => '1st', 'is_enrollment_open' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
