<?php

namespace Database\Seeders;

use App\Models\ProgramStudy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $si = ProgramStudy::where('code', 'SI')->firstOrFail();
        $ti = ProgramStudy::where('code', 'TI')->firstOrFail();

        $password = Hash::make('password');

        $students = [

            // ==========================
            // SISTEM INFORMASI
            // ==========================

            ['2023101001', 'Ahmad Fauzan', 'SI'],
            ['2023101002', 'Budi Santoso', 'SI'],
            ['2023101003', 'Citra Lestari', 'SI'],
            ['2023101004', 'Dimas Saputra', 'SI'],
            ['2023101005', 'Eka Putri', 'SI'],
            ['2023101006', 'Farhan Ramadhan', 'SI'],
            ['2023101007', 'Gina Maharani', 'SI'],
            ['2023101008', 'Hendra Wijaya', 'SI'],
            ['2023101009', 'Indah Permata', 'SI'],
            ['2023101010', 'Joko Prasetyo', 'SI'],

            // ==========================
            // TEKNIK INFORMATIKA
            // ==========================

            ['2023201001', 'Kevin Saputra', 'TI'],
            ['2023201002', 'Laila Nur Azizah', 'TI'],
            ['2023201003', 'Muhammad Rizky', 'TI'],
            ['2023201004', 'Aulia aja', 'TI'],
            ['2023201005', 'Oka Pratama', 'TI'],
            ['2023201006', 'Putri Maharani', 'TI'],
            ['2023201007', 'Rafi Kurniawan', 'TI'],
            ['2023201008', 'Salsabila Putri', 'TI'],
            ['2023201009', 'Taufik Hidayat', 'TI'],
            ['2023201010', 'Zahra Amalia', 'TI'],
            ['2024080319', 'Citra Lestari', 'TI']
        ];

        foreach ($students as [$nim, $name, $prodi]) {

            $programStudy = $prodi === 'SI'
                ? $si
                : $ti;

            $user = User::updateOrCreate(
                [
                    'nim' => $nim,
                ],
                [
                    'name' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)) . '@student.simmag.test',

                    'username' => $nim,
                    'identifier' => $nim,

                    'nim' => $nim,

                    'program_study_id' => $programStudy->id,

                    'institution_name' => 'Universitas Esa Unggul',

                    'role' => 'mahasiswa',

                    'password' => $password,

                    'email_verified_at' => now(),

                    'is_active' => true,
                ]
            );

            $user->syncRoles([
                'mahasiswa',
            ]);
        }
    }
}