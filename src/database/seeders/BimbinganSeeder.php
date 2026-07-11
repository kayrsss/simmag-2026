<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\Internship;
use Illuminate\Database\Seeder;

class BimbinganSeeder extends Seeder
{
    public function run(): void
    {
        $internships = Internship::query()
            ->with(['student', 'supervisorLecturer'])
            ->get();

        foreach ($internships as $internship) {
            for ($i = 1; $i <= 3; $i++) {
                Consultation::updateOrCreate(
                    [
                        'internship_id' => $internship->id,
                        'student_id' => $internship->student_id,
                        'lecturer_id' => $internship->supervisor_lecturer_id,
                        'consultation_date' => now()->subDays(30 - ($i * 7))->toDateString(),
                    ],
                    [
                        'topic' => match ($i) {
                            1 => 'Arahan awal pelaksanaan magang',
                            2 => 'Monitoring progres pekerjaan magang',
                            default => 'Evaluasi akhir kegiatan magang',
                        },
                        'notes' => match ($i) {
                            1 => 'Mahasiswa diarahkan memahami ruang lingkup pekerjaan dan aturan instansi.',
                            2 => 'Mahasiswa melaporkan progres pekerjaan dan kendala teknis yang ditemukan.',
                            default => 'Mahasiswa menyampaikan hasil akhir kegiatan dan persiapan laporan akhir.',
                        },
                        'follow_up' => match ($i) {
                            1 => 'Mahasiswa diminta menyusun rencana kerja mingguan.',
                            2 => 'Mahasiswa diminta melengkapi dokumentasi pekerjaan.',
                            default => 'Mahasiswa diminta menyelesaikan laporan akhir.',
                        },
                        'meeting_link' => 'https://meet.google.com/simmag-' . $internship->id . '-' . $i,
                        'status' => 'Selesai',
                    ]
                );
            }
        }
    }
}