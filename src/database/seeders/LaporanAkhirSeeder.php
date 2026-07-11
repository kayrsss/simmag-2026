<?php

namespace Database\Seeders;

use App\Models\FinalReport;
use App\Models\Internship;
use Illuminate\Database\Seeder;

class LaporanAkhirSeeder extends Seeder
{
    public function run(): void
    {
        $internships = Internship::all();

        foreach ($internships as $internship) {
            FinalReport::updateOrCreate(
                [
                    'internship_id' => $internship->id,
                ],
                [
                    'file_path' => 'internships/laporan-akhir/laporan-magang-' . $internship->id . '.pdf',
                    'word_count' => rand(8500, 15000),
                    'status' => 'Disetujui',
                    'revision_notes' => 'Laporan telah memenuhi standar penulisan.',
                    'approved_at' => now()->subDays(rand(1, 5)),
                ]
            );
        }
    }
}