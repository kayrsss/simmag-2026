<?php

namespace Database\Seeders;

use App\Models\FrameworkOfReference;
use App\Models\Internship;
use Illuminate\Database\Seeder;

class KerangkaAcuanSeeder extends Seeder
{
    public function run(): void
    {
        $internships = Internship::query()
            ->with(['student', 'company'])
            ->get();

        foreach ($internships as $internship) {
            FrameworkOfReference::updateOrCreate(
                [
                    'internship_id' => $internship->id,
                    'version' => 1,
                ],
                [
                    'title' => 'Pengembangan Sistem Informasi pada ' . ($internship->company?->name ?? $internship->company_name),
                    'description' => 'Kerangka acuan ini berisi rencana pelaksanaan magang mahasiswa pada instansi mitra.',
                    'start_date' => $internship->started_at,
                    'target_end_date' => $internship->ended_at,
                    'work_plan' => "1. Observasi proses bisnis instansi.\n2. Analisis kebutuhan sistem.\n3. Membantu pengembangan fitur aplikasi.\n4. Menyusun dokumentasi hasil pekerjaan.",
                    'ownership_clause' => 'Hasil pekerjaan selama magang mengikuti ketentuan instansi mitra dan digunakan untuk kepentingan akademik.',
                    'confidentiality_clause' => 'Mahasiswa wajib menjaga kerahasiaan data dan informasi milik instansi mitra.',
                    'remuneration_clause' => 'Ketentuan kompensasi mengikuti kebijakan masing-masing instansi mitra.',
                    'file_path' => null,
                    'status' => 'Disetujui',
                    'field_supervisor_approved_at' => now()->subDays(14),
                    'lecturer_approved_at' => now()->subDays(13),
                    'field_supervisor_notes' => 'Kerangka acuan sudah sesuai dengan kebutuhan instansi.',
                    'lecturer_notes' => 'Kerangka acuan disetujui untuk pelaksanaan magang.',
                    'previous_version_id' => null,
                ]
            );
        }
    }
}