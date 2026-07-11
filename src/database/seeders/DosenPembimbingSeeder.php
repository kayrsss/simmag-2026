<?php

namespace Database\Seeders;

use App\Models\ProgramStudy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DosenPembimbingSeeder extends Seeder
{
    public function run(): void
    {
        $si = ProgramStudy::where('code', 'SI')->firstOrFail();
        $ti = ProgramStudy::where('code', 'TI')->firstOrFail();

        $password = Hash::make('password');

        $lecturers = [

            // ==========================
            // SISTEM INFORMASI
            // ==========================

            ['0410019001', 'Dr. Andi Pratama, S.Kom., M.Kom.', 'SI'],
            ['0410019002', 'Dr. Budi Hartono, S.Kom., M.Kom.', 'SI'],

            // ==========================
            // TEKNIK INFORMATIKA
            // ==========================

            ['0410019003', 'Dr. Rina Wulandari, S.T., M.Kom.', 'TI'],
            ['0410019004', 'Dr. Agus Setiawan, S.T., M.T.', 'TI'],
        ];

        foreach ($lecturers as [$nidn, $name, $prodi]) {

            $programStudy = $prodi === 'SI'
                ? $si
                : $ti;

            $user = User::updateOrCreate(
                [
                    'nidn' => $nidn,
                ],
                [
                    'name' => $name,
                    'email' => strtolower(str_replace([' ', ',', '.'], ['', '', ''], $name)) . '@lecturer.simmag.test',

                    'username' => $nidn,
                    'identifier' => $nidn,

                    'nidn' => $nidn,

                    'program_study_id' => $programStudy->id,

                    'institution_name' => 'Universitas Esa Unggul',

                    'role' => 'dosen_pembimbing',

                    'password' => $password,

                    'email_verified_at' => now(),

                    'is_active' => true,
                ]
            );

            $user->syncRoles([
                'dosen_pembimbing',
            ]);
        }
    }
}