<?php

namespace Database\Seeders;

use App\Models\AuditTrail;
use App\Models\FrameworkOfReference;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditTrailSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('role', 'admin')
            ->first();

        $student = User::query()
            ->where('role', 'mahasiswa')
            ->first();

        $lecturer = User::query()
            ->where('role', 'dosen_pembimbing')
            ->first();

        $internship = Internship::query()->first();
        $framework = FrameworkOfReference::query()->first();
        $logbook = Logbook::query()->first();

        $items = [
            [
                'user_id' => $admin?->id,
                'action' => 'sync_siakad',
                'entity_type' => 'SiakadSyncLog',
                'entity_id' => null,
                'previous_status' => null,
                'new_status' => 'success',
                'notes' => 'Admin menjalankan sinkronisasi data dummy SIAKAD.',
            ],
            [
                'user_id' => $student?->id,
                'action' => 'submit_framework',
                'entity_type' => FrameworkOfReference::class,
                'entity_id' => $framework?->id,
                'previous_status' => 'Draft',
                'new_status' => 'Menunggu_Review',
                'notes' => 'Mahasiswa mengajukan Kerangka Acuan untuk ditinjau.',
            ],
            [
                'user_id' => $lecturer?->id,
                'action' => 'approve_framework',
                'entity_type' => FrameworkOfReference::class,
                'entity_id' => $framework?->id,
                'previous_status' => 'Disetujui_PL',
                'new_status' => 'Disetujui',
                'notes' => 'Dosen Pembimbing menyetujui Kerangka Acuan.',
            ],
            [
                'user_id' => $student?->id,
                'action' => 'submit_logbook',
                'entity_type' => Logbook::class,
                'entity_id' => $logbook?->id,
                'previous_status' => null,
                'new_status' => 'Menunggu_Validasi',
                'notes' => 'Mahasiswa mengirim Logbook harian.',
            ],
            [
                'user_id' => $admin?->id,
                'action' => 'activate_internship',
                'entity_type' => Internship::class,
                'entity_id' => $internship?->id,
                'previous_status' => 'menunggu_ka',
                'new_status' => 'aktif',
                'notes' => 'Admin mengaktifkan status magang mahasiswa.',
            ],
        ];

        foreach ($items as $item) {
            AuditTrail::create([
                ...$item,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'SIMMAG Seeder',
            ]);
        }
    }
}