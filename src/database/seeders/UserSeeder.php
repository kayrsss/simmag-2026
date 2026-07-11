<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        /*
        |--------------------------------------------------------------------------
        | Admin Fakultas
        |--------------------------------------------------------------------------
        */
        $admin = User::updateOrCreate(
            ['email' => 'admin@simmag.com'],
            [
                'name' => 'Admin Fakultas SIMMAG',
                'username' => 'ADM-001',
                'identifier' => 'ADM001',
                'role' => 'admin',
                'password' => $password,
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $admin->syncRoles(['admin']);

        /*
        |--------------------------------------------------------------------------
        | Mahasiswa Demo
        |--------------------------------------------------------------------------
        */
        $mahasiswa = User::updateOrCreate(
            ['email' => 'mahasiswa@simmag.com'],
            [
                'name' => 'Mahasiswa Demo SIMMAG',
                'username' => 'MHS-000001',
                'identifier' => '2099999999',
                'nim' => '2099999999',
                'role' => 'mahasiswa',
                'institution_name' => 'Universitas Esa Unggul',
                'password' => $password,
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $mahasiswa->syncRoles(['mahasiswa']);

        /*
        |--------------------------------------------------------------------------
        | Dosen Pembimbing Demo
        |--------------------------------------------------------------------------
        */
        $dosenPembimbing = User::updateOrCreate(
            ['email' => 'dosen@simmag.com'],
            [
                'name' => 'Dosen Pembimbing Demo',
                'username' => 'DSN-0001',
                'identifier' => '9999999999',
                'nidn' => '9999999999',
                'role' => 'dosen_pembimbing',
                'institution_name' => 'Universitas Esa Unggul',
                'password' => $password,
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $dosenPembimbing->syncRoles(['dosen_pembimbing']);

        /*
        |--------------------------------------------------------------------------
        | Pembimbing Lapangan
        |--------------------------------------------------------------------------
        | Email harus sama dengan internships.field_supervisor_email agar scope
        | data Pembimbing Lapangan langsung terhubung.
        */
        $pembimbingLapangan = User::updateOrCreate(
            ['email' => 'PL@simmag.com'],
            [
                'name' => 'Syifa Fauziah',
                'username' => 'PL-00001',
                'identifier' => 'PL001',
                'role' => 'pembimbing_lapangan',
                'institution_name' => 'PT Nusantara Digital Solusi',
                'password' => $password,
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $pembimbingLapangan->syncRoles(['pembimbing_lapangan']);
    }
}