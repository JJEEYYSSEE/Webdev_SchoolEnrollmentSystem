<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Registrars
        $reg1 = DB::table('users')->insertGetId([
            'name'              => 'Liza Fernandez',
            'email'             => 'registrar1@school.edu.ph',
            'email_verified_at' => now(),
            'school_id'         => 'REG-0001',   // staff ID login
            'password'          => Hash::make('password'),
            'role'              => 'registrar',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
        $reg2 = DB::table('users')->insertGetId([
            'name'              => 'Mark Villanueva',
            'email'             => 'registrar2@school.edu.ph',
            'email_verified_at' => now(),
            'school_id'         => 'REG-0002',   // staff ID login
            'password'          => Hash::make('password'),
            'role'              => 'registrar',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        DB::table('registrars')->insert([
            ['user_id' => $reg1, 'first_name' => 'Liza', 'last_name' => 'Fernandez', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $reg2, 'first_name' => 'Mark', 'last_name' => 'Villanueva', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $stem = DB::table('strands')->where('strand_code', 'STEM')->value('id');
        $abm  = DB::table('strands')->where('strand_code', 'ABM')->value('id');

        // Students
        $studentUsers = [
            ['name' => 'Juan Dela Cruz',  'email' => 'juan.delacruz@student.edu.ph',  'number' => '2026-00001', 'first' => 'Juan',  'last' => 'Dela Cruz', 'phone' => '09171234567', 'birth' => '2009-03-15', 'address' => 'Imus, Cavite',       'strand' => $stem, 'grade' => '11'],
            ['name' => 'Maria Santos',    'email' => 'maria.santos@student.edu.ph',    'number' => '2026-00002', 'first' => 'Maria', 'last' => 'Santos',    'phone' => '09181234567', 'birth' => '2009-07-22', 'address' => 'Bacoor, Cavite',     'strand' => $stem, 'grade' => '11'],
            ['name' => 'Pedro Reyes',     'email' => 'pedro.reyes@student.edu.ph',     'number' => '2026-00003', 'first' => 'Pedro', 'last' => 'Reyes',     'phone' => '09191234567', 'birth' => '2008-11-05', 'address' => 'Dasmariñas, Cavite', 'strand' => $abm,  'grade' => '11'],
            ['name' => 'Ana Garcia',      'email' => 'ana.garcia@student.edu.ph',      'number' => '2026-00004', 'first' => 'Ana',   'last' => 'Garcia',    'phone' => '09201234567', 'birth' => '2009-01-30', 'address' => 'Kawit, Cavite',      'strand' => $stem, 'grade' => '11'],
            ['name' => 'Jose Ramos',      'email' => 'jose.ramos@student.edu.ph',      'number' => '2026-00005', 'first' => 'Jose',  'last' => 'Ramos',     'phone' => '09211234567', 'birth' => '2008-09-18', 'address' => 'Imus, Cavite',       'strand' => $abm,  'grade' => '11'],
        ];

        foreach ($studentUsers as $s) {
            $uid = DB::table('users')->insertGetId([
                'name'              => $s['name'],
                'email'             => $s['email'],
                'email_verified_at' => now(),
                'birthdate'         => $s['birth'],
                'school_id'         => $s['number'],   // seeded students are already admitted
                'password'          => Hash::make('password'),
                'role'              => 'student',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
            DB::table('students')->insert([
                'user_id'        => $uid,
                'student_number' => $s['number'],
                'first_name'     => $s['first'],
                'last_name'      => $s['last'],
                'phone'          => $s['phone'],
                'birthdate'      => $s['birth'],
                'address'        => $s['address'],
                'strand_id'      => $s['strand'],
                'grade_level'    => $s['grade'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
