<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SimmagStakeholderSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'syifa fauziah',
                'email' => 'hrd@nusantaradigital.test',
                'password' => 'password123',
                'role' => 'pembimbing_lapangan',
                'identifier' => 'PL001',
            ],
            [
                'name' => 'Dedi Saputra',
                'email' => 'career@tkm.test',
                'password' => 'password123',
                'role' => 'pembimbing_lapangan',
                'identifier' => 'PL002',
            ],
            [
                'name' => 'Salsa Putri',
                'email' => 'admin@kreatifmedia.test',
                'password' => 'password123',
                'role' => 'pembimbing_lapangan',
                'identifier' => 'PL003',
            ],
        ];

        foreach ($users as $item) {
            $password = $item['password'];
            unset($item['password']);

            $user = User::updateOrCreate(
                ['email' => $item['email']],
                array_merge($item, [
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ])
            );

            $user->syncRoles(['pembimbing_lapangan']);
        }
    }
}