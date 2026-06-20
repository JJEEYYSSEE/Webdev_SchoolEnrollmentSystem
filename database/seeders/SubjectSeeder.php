<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('subjects')->insert([
            // Core subjects (shared across all strands)
            ['subject_code' => 'CORE-ORALCOM',  'subject_name' => 'Oral Communication',                               'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'CORE-KOMPAN',   'subject_name' => 'Komunikasyon at Pananaliksik',                     'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'CORE-GENMATH',  'subject_name' => 'General Mathematics',                              'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'CORE-EAPP',     'subject_name' => 'Earth and Life Science',                           'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'CORE-PE1',      'subject_name' => 'Physical Education and Health 1',                  'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'CORE-UCSP',     'subject_name' => 'Understanding Culture, Society and Politics',      'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'CORE-PR1',      'subject_name' => 'Practical Research 1',                             'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'CORE-PR2',      'subject_name' => 'Practical Research 2',                             'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'CORE-PE2',      'subject_name' => 'Physical Education and Health 2',                  'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            // STEM
            ['subject_code' => 'STEM-PRECALC',  'subject_name' => 'Pre-Calculus',                                     'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'STEM-BASICCALC','subject_name' => 'Basic Calculus',                                   'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'STEM-GENBIO1',  'subject_name' => 'General Biology 1',                                'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'STEM-GENCHEM1', 'subject_name' => 'General Chemistry 1',                              'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'STEM-GENPHY1',  'subject_name' => 'General Physics 1',                                'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            // ABM
            ['subject_code' => 'ABM-FABM1',     'subject_name' => 'Fundamentals of ABM 1',                            'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'ABM-FABM2',     'subject_name' => 'Fundamentals of ABM 2',                            'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'ABM-BUSFIN',    'subject_name' => 'Business Finance',                                 'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'ABM-ORGMAN',    'subject_name' => 'Organization and Management',                      'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            // HUMSS
            ['subject_code' => 'HUMSS-CREWRIT',  'subject_name' => 'Creative Writing',                                'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'HUMSS-CWORLDLIT','subject_name' => 'Creative Nonfiction',                             'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'HUMSS-PHILGOV',  'subject_name' => 'Philippine Politics and Governance',              'units' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['subject_code' => 'HUMSS-COMMRES',  'subject_name' => 'Community Engagement, Solidarity and Citizenship','units' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
