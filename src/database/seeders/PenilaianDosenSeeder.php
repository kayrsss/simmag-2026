<?php

namespace Database\Seeders;

use App\Models\Internship;
use App\Models\LecturerAssessment;
use Illuminate\Database\Seeder;

class PenilaianDosenSeeder extends Seeder
{
    public function run(): void
    {
        $internships = Internship::query()->get();

        foreach ($internships as $internship) {
            $scores = [
                'consistency_score' => rand(80, 95),
                'logbook_completeness_score' => rand(80, 95),
                'neatness_score' => rand(80, 95),
                'content_completeness_score' => rand(80, 95),
                'writing_flow_score' => rand(80, 95),
                'grammar_score' => rand(80, 95),
            ];

            LecturerAssessment::updateOrCreate(
                [
                    'internship_id' => $internship->id,
                ],
                [
                    'evaluator_id' => $internship->supervisor_lecturer_id,
                    ...$scores,
                    'overall_score' => round(array_sum($scores) / count($scores), 2),
                    'notes' => 'Mahasiswa telah menyelesaikan laporan dan dokumentasi magang dengan baik.',
                    'assessed_at' => now()->subDays(rand(1, 5)),
                ]
            );
        }
    }
}