<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $stem = DB::table('strands')->where('strand_code', 'STEM')->value('id');
        $abm  = DB::table('strands')->where('strand_code', 'ABM')->value('id');
        $sy   = DB::table('school_years')->where('year_label', '2026-2027')->value('id');

        // Section names per grade level (fixed by school):
        // Grade 11 AM: Kasipagan, Kapanatagan | PM: Katapangan, Kabutihan
        // Grade 12 AM: Gabriela Silang, Melchora Aquino | PM: Macario Sakay, Vicente Lim

        $sections = [
            // STEM Grade 11
            [$stem, $sy, '11', '1st', 'Kasipagan',       'AM', 40],
            [$stem, $sy, '11', '1st', 'Kapanatagan',     'AM', 40],
            [$stem, $sy, '11', '1st', 'Katapangan',      'PM', 40],
            [$stem, $sy, '11', '1st', 'Kabutihan',       'PM', 40],
            // STEM Grade 12
            [$stem, $sy, '12', '1st', 'Gabriela Silang', 'AM', 40],
            [$stem, $sy, '12', '1st', 'Melchora Aquino', 'AM', 40],
            [$stem, $sy, '12', '1st', 'Macario Sakay',   'PM', 40],
            [$stem, $sy, '12', '1st', 'Vicente Lim',     'PM', 40],
            // ABM Grade 11
            [$abm,  $sy, '11', '1st', 'Kasipagan',       'AM', 40],
            [$abm,  $sy, '11', '1st', 'Kapanatagan',     'AM', 40],
            [$abm,  $sy, '11', '1st', 'Katapangan',      'PM', 40],
            [$abm,  $sy, '11', '1st', 'Kabutihan',       'PM', 40],
        ];

        foreach ($sections as [$strandId, $syId, $grade, $sem, $name, $time, $cap]) {
            DB::table('sections')->insert([
                'strand_id'      => $strandId,
                'school_year_id' => $syId,
                'grade_level'    => $grade,
                'semester'       => $sem,
                'section_name'   => $name,
                'time_period'    => $time,
                'max_capacity'   => $cap,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
