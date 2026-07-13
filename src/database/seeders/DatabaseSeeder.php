<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Seeder Utama SIMMAG
        |--------------------------------------------------------------------------
        |
        | Seeder transaksi seperti MahasiswaSeeder, MagangSeeder, LogbookSeeder,
        | dan PenilaianSeeder tidak dijalankan otomatis agar data yang sudah
        | tersimpan tidak dibuat ulang.
        |
        */

        $this->call([
            UserSeeder::class,
        ]);
    }
}