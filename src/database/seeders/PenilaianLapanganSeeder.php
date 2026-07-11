<?php

namespace Database\Seeders;

use App\Models\FieldAssessment;
use App\Models\Internship;
use Illuminate\Database\Seeder;

class PenilaianLapanganSeeder extends Seeder
{
    public function run(): void
    {
        $internships = Internship::query()->get();

        foreach ($internships as $internship) {
            $scores = [
                'discipline_score' => rand(80, 95),
                'initiative_score' => rand(80, 95),
                'teamwork_score' => rand(80, 95),
                'communication_score' => rand(80, 95),
                'adaptability_score' => rand(80, 95),
                'diligence_score' => rand(80, 95),
                'appearance_score' => rand(80, 95),
                'honesty_score' => rand(80, 95),
                'critical_thinking_score' => rand(80, 95),
                'responsibility_score' => rand(80, 95),
            ];

            FieldAssessment::updateOrCreate(
                [
                    'internship_id' => $internship->id,
                ],
                [
                    'evaluator_id' => $internship->submitted_by ?? $internship->student_id,
                    ...$scores,
                    'overall_score' => round(array_sum($scores) / count($scores), 2),
                    'notes' => 'Mahasiswa menunjukkan kinerja yang baik selama pelaksanaan magang.',
                    'assessed_at' => now()->subDays(rand(1, 5)),
                ]
            );
        }
    }
}