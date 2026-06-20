<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StrandSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('strands')->insert([
            ['strand_code' => 'STEM',  'strand_name' => 'Science, Technology, Engineering and Mathematics', 'created_at' => now(), 'updated_at' => now()],
            ['strand_code' => 'ABM',   'strand_name' => 'Accountancy, Business and Management',            'created_at' => now(), 'updated_at' => now()],
            ['strand_code' => 'HUMSS', 'strand_name' => 'Humanities and Social Sciences',                  'created_at' => now(), 'updated_at' => now()],
            ['strand_code' => 'GAS',   'strand_name' => 'General Academic Strand',                         'created_at' => now(), 'updated_at' => now()],
            ['strand_code' => 'TVL',   'strand_name' => 'Technical-Vocational-Livelihood',                 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
