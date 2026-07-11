<?php

namespace Database\Seeders;

use App\Models\ProgramStudy;
use Illuminate\Database\Seeder;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        ProgramStudy::updateOrCreate(
            ['code' => 'SI'],
            [
                'name' => 'Sistem Informasi',
                'is_active' => true,
            ]
        );

        ProgramStudy::updateOrCreate(
            ['code' => 'TI'],
            [
                'name' => 'Teknik Informatika',
                'is_active' => true,
            ]
        );
    }
}