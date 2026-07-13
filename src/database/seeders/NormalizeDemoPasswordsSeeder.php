<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NormalizeDemoPasswordsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            User::query()
                ->select([
                    'id',
                    'password',
                ])
                ->chunkById(
                    100,
                    function ($users): void {
                        foreach ($users as $user) {
                            $user->forceFill([
                                'password' => Hash::make(
                                    'password'
                                ),
                            ])->save();
                        }
                    }
                );
        });

        $this->command?->info(
            'Semua password akun berhasil diubah menjadi: password'
        );
    }
}