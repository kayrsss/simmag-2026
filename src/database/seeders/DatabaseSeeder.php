<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,

            ProgramStudiSeeder::class,
            PeriodeMagangSeeder::class,

            UserSeeder::class,
            MahasiswaSeeder::class,
            DosenPembimbingSeeder::class,

            InstansiSeeder::class,
            MagangSeeder::class,

            KerangkaAcuanSeeder::class,
            LogbookSeeder::class,
            BimbinganSeeder::class,

            LaporanAkhirSeeder::class,
            PenilaianLapanganSeeder::class,
            PenilaianDosenSeeder::class,

            PengumumanSeeder::class,
            AuditTrailSeeder::class,
            SinkronisasiSiakadSeeder::class,
        ]);
    }
}