<?php

namespace Database\Seeders;

use App\Models\FrameworkOfReference;
use App\Models\Internship;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LogbookSeeder extends Seeder
{
    public function run(): void
    {
        $frameworks = FrameworkOfReference::with('internship')->get();

        foreach ($frameworks as $framework) {

            $start = Carbon::parse($framework->start_date);

            for ($i = 1; $i <= 10; $i++) {

                Logbook::updateOrCreate(
                    [
                        'internship_id' => $framework->internship_id,
                        'activity_date' => $start->copy()->addDays($i),
                    ],
                    [
                        'framework_of_reference_id' => $framework->id,

                        'description' => match ($i) {
                            1 => 'Orientasi lingkungan kerja dan pengenalan SOP.',
                            2 => 'Observasi proses bisnis instansi.',
                            3 => 'Analisis kebutuhan sistem.',
                            4 => 'Perancangan solusi aplikasi.',
                            5 => 'Implementasi modul utama.',
                            6 => 'Perbaikan bug aplikasi.',
                            7 => 'Pengujian fitur.',
                            8 => 'Penyusunan dokumentasi.',
                            9 => 'Presentasi hasil pekerjaan.',
                            default => 'Evaluasi hasil kegiatan magang.',
                        },

                        'attachment_file' => null,

                        'status' => 'Tervalidasi',

                        'validation_notes' => 'Aktivitas sesuai dengan rencana kerja.',

                        'validated_at' => now()->subDays(rand(1, 10)),
                    ]
                );
            }
        }
    }
}