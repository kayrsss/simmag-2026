<?php

namespace Database\Seeders;

use App\Models\Period;
use Illuminate\Database\Seeder;

class PeriodeMagangSeeder extends Seeder
{
    public function run(): void
    {
        Period::updateOrCreate(
            [
                'academic_year' => '2025/2026',
                'semester' => 'Genap',
            ],
            [
                'start_date' => '2026-02-01',
                'end_date' => '2026-06-30',
                'status' => 'selesai',
                'is_active' => false,
            ]
        );

        Period::updateOrCreate(
            [
                'academic_year' => '2026/2027',
                'semester' => 'Ganjil',
            ],
            [
                'start_date' => '2026-08-01',
                'end_date' => '2026-12-31',
                'status' => 'aktif',
                'is_active' => true,
            ]
        );
    }
}