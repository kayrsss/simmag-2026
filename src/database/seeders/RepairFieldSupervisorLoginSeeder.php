<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Spatie\Permission\Models\Role;

class RepairFieldSupervisorLoginSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Rina Marlina',
                'email' => 'rina@pl.simmag.com',
                'username' => 'rina.pl',
                'identifier' => 'PL-RINA',
                'nip' => 'PL-NIP-RINA',
            ],
            [
                'name' => 'Dedi Saputra',
                'email' => 'dedi@pl.simmag.com',
                'username' => 'dedi.pl',
                'identifier' => 'PL-DEDI',
                'nip' => 'PL-NIP-DEDI',
            ],
            [
                'name' => 'Salsa Putri',
                'email' => 'salsa@pl.simmag.com',
                'username' => 'salsa.pl',
                'identifier' => 'PL-SALSA',
                'nip' => 'PL-NIP-SALSA',
            ],
        ];

        $role = Role::findOrCreate(
            'pembimbing_lapangan',
            'web'
        );

        DB::transaction(
            function () use (
                $accounts,
                $role
            ): void {
                foreach ($accounts as $account) {
                    $user = User::query()
                        ->where(function ($query) use (
                            $account
                        ): void {
                            $query
                                ->whereRaw(
                                    'LOWER(email) = ?',
                                    [
                                        strtolower(
                                            $account['email']
                                        ),
                                    ]
                                )
                                ->orWhere(
                                    'username',
                                    $account['username']
                                )
                                ->orWhere(
                                    'name',
                                    $account['name']
                                );
                        })
                        ->orderByDesc('id')
                        ->first();

                    if (! $user) {
                        $user = new User();
                    }

                    $userId = $user->exists
                        ? $user->id
                        : null;

                    $user->name =
                        $account['name'];

                    $user->email =
                        strtolower(
                            $account['email']
                        );

                    if (
                        Schema::hasColumn(
                            'users',
                            'username'
                        )
                    ) {
                        $user->username =
                            $account['username'];
                    }

                    if (
                        Schema::hasColumn(
                            'users',
                            'identifier'
                        )
                    ) {
                        $user->identifier =
                            $this->uniqueValue(
                                'identifier',
                                $account['identifier'],
                                $userId
                            );
                    }

                    if (
                        Schema::hasColumn(
                            'users',
                            'nip'
                        )
                    ) {
                        $user->nip =
                            $this->uniqueValue(
                                'nip',
                                $account['nip'],
                                $userId
                            );
                    }

                    $user->password =
                        Hash::make('password');

                    if (
                        Schema::hasColumn(
                            'users',
                            'email_verified_at'
                        )
                    ) {
                        $user->email_verified_at =
                            now();
                    }

                    if (
                        Schema::hasColumn(
                            'users',
                            'is_active'
                        )
                    ) {
                        $user->is_active = true;
                    }

                    $user->save();

                    $user->syncRoles([
                        $role->name,
                    ]);

                    DB::table('internships')
                        ->whereRaw(
                            'LOWER(field_supervisor_name) = ?',
                            [
                                strtolower(
                                    $account['name']
                                ),
                            ]
                        )
                        ->update([
                            'field_supervisor_id' =>
                                $user->id,

                            'field_supervisor_email' =>
                                strtolower(
                                    $account['email']
                                ),

                            'updated_at' =>
                                now(),
                        ]);
                }
            }
        );

        foreach ($accounts as $account) {
            $user = User::query()
                ->where(
                    'username',
                    $account['username']
                )
                ->first();

            if (! $user) {
                throw new RuntimeException(
                    "Akun {$account['username']} tidak ditemukan setelah proses repair."
                );
            }

            if (
                ! Hash::check(
                    'password',
                    $user->password
                )
            ) {
                throw new RuntimeException(
                    "Password akun {$account['username']} gagal diverifikasi."
                );
            }

            if (
                ! $user->hasRole(
                    'pembimbing_lapangan'
                )
            ) {
                throw new RuntimeException(
                    "Role akun {$account['username']} belum benar."
                );
            }

            $this->command?->info(
                "VERIFIED: {$account['username']} / password"
            );
        }
    }

    private function uniqueValue(
        string $column,
        string $baseValue,
        ?int $exceptUserId
    ): string {
        $candidate = $baseValue;

        $counter = 1;

        while (
            User::query()
                ->where(
                    $column,
                    $candidate
                )
                ->when(
                    $exceptUserId !== null,
                    function ($query) use (
                        $exceptUserId
                    ): void {
                        $query->where(
                            'id',
                            '!=',
                            $exceptUserId
                        );
                    }
                )
                ->exists()
        ) {
            $candidate =
                $baseValue
                . '-'
                . $counter;

            $counter++;
        }

        return $candidate;
    }
}