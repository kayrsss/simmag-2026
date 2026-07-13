<?php

namespace Database\Seeders;

use App\Models\FrameworkOfReference;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class LogbookSeeder extends Seeder
{
    public function run(): void
    {
        $frameworks = FrameworkOfReference::query()
            ->with('internship')
            ->whereNotNull('internship_id')
            ->orderBy('id')
            ->get()
            ->groupBy('internship_id')
            ->map(
                fn (Collection $items): FrameworkOfReference =>
                    $items
                        ->sortByDesc('id')
                        ->first()
            );

        foreach ($frameworks as $framework) {
            $internship = $framework->internship;

            if (
                ! $internship
                || ! $internship->student_id
                || ! $framework->start_date
            ) {
                continue;
            }

            $startDate = Carbon::parse(
                $framework->start_date
            );

            for ($day = 1; $day <= 10; $day++) {
                $activityDate = $startDate
                    ->copy()
                    ->addDays($day)
                    ->toDateString();

                $validatedAt = now()
                    ->subDays(
                        random_int(1, 10)
                    );

                Logbook::query()->updateOrCreate(
                    [
                        'internship_id' =>
                            $internship->id,

                        'activity_date' =>
                            $activityDate,
                    ],
                    [
                        'framework_of_reference_id' =>
                            $framework->id,

                        'student_id' =>
                            $internship->student_id,

                        'activity' => match ($day) {
                            1 =>
                                'Orientasi lingkungan kerja dan pengenalan SOP.',

                            2 =>
                                'Observasi proses bisnis instansi.',

                            3 =>
                                'Analisis kebutuhan sistem.',

                            4 =>
                                'Perancangan solusi aplikasi.',

                            5 =>
                                'Implementasi modul utama.',

                            6 =>
                                'Perbaikan bug aplikasi.',

                            7 =>
                                'Pengujian fitur.',

                            8 =>
                                'Penyusunan dokumentasi.',

                            9 =>
                                'Presentasi hasil pekerjaan.',

                            default =>
                                'Evaluasi hasil kegiatan magang.',
                        },

                        'evidence_name' =>
                            null,

                        'evidence_path' =>
                            null,

                        'status' =>
                            Logbook::STATUS_VALIDATED,

                        'review_note' =>
                            'Aktivitas sesuai dengan rencana kerja.',

                        'submitted_at' =>
                            $validatedAt
                                ->copy()
                                ->subHours(4),

                        'validated_at' =>
                            $validatedAt,

                        'validated_by' =>
                            null,
                    ]
                );
            }
        }
    }
}