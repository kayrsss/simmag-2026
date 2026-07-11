<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

class InstansiSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'PT Nusantara Digital Solusi',
                'address' => 'Jl. Merdeka No. 10, Jakarta',
                'phone' => '021-5551001',
                'email' => 'hrd@nusantaradigital.test',
                'pic_name' => 'Rina Marlina',
                'pic_position' => 'HR Manager',
                'pic_phone' => '081234567001',
                'is_active' => true,
            ],
            [
                'name' => 'PT Teknologi Karya Mandiri',
                'address' => 'Jl. Sudirman No. 25, Tangerang',
                'phone' => '021-5551002',
                'email' => 'career@tkm.test',
                'pic_name' => 'Dedi Saputra',
                'pic_position' => 'Talent Acquisition',
                'pic_phone' => '081234567002',
                'is_active' => true,
            ],
            [
                'name' => 'CV Kreatif Media Indonesia',
                'address' => 'Jl. Veteran No. 7, Bekasi',
                'phone' => '021-5551003',
                'email' => 'admin@kreatifmedia.test',
                'pic_name' => 'Salsa Putri',
                'pic_position' => 'Operational Manager',
                'pic_phone' => '081234567003',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            CompanyProfile::updateOrCreate(
                ['email' => $company['email']],
                $company
            );
        }
    }
}