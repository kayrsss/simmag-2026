<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengumumanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('role', 'admin')
            ->orWhereHas('roles', fn ($query) => $query->where('name', 'admin'))
            ->first();

        $items = [
            [
                'title' => 'Pengisian Logbook Harian',
                'content' => 'Mahasiswa peserta magang wajib mengisi logbook harian secara rutin selama periode magang berlangsung.',
                'priority' => 'Penting',
            ],
            [
                'title' => 'Bimbingan Minimal Tiga Kali',
                'content' => 'Mahasiswa wajib melakukan bimbingan dengan Dosen Pembimbing minimal tiga kali selama masa magang.',
                'priority' => 'Penting',
            ],
            [
                'title' => 'Unggah Laporan Akhir',
                'content' => 'Laporan akhir magang dapat diunggah setelah seluruh logbook dan bimbingan selesai dilakukan.',
                'priority' => 'Biasa',
            ],
        ];

        foreach ($items as $item) {
            Announcement::updateOrCreate(
                ['title' => $item['title']],
                [
                    ...$item,
                    'created_by' => $admin?->id,
                ]
            );
        }
    }
}