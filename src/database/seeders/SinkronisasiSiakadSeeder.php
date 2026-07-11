<?php

namespace Database\Seeders;

use App\Models\SiakadSyncLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class SinkronisasiSiakadSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('role', 'admin')
            ->first();

        SiakadSyncLog::updateOrCreate(
            [
                'sync_type' => 'dummy',
                'started_at' => now()->subMinutes(2),
            ],
            [
                'status' => 'success',
                'total_inserted' => 32,
                'total_updated' => 0,
                'total_failed' => 0,
                'message' => 'Sinkronisasi dummy SIAKAD berhasil. Data mahasiswa, dosen, program studi, dan penugasan dosen pembimbing telah diperbarui.',
                'executed_by' => $admin?->id,
                'finished_at' => now(),
            ]
        );
    }
}