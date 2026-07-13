<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class FieldSupervisorAccountSeeder extends Seeder
{
    public function run(): void
    {
        $roleId = DB::table('roles')
            ->where(
                'name',
                'pembimbing_lapangan'
            )
            ->where(
                'guard_name',
                'web'
            )
            ->value('id');

        if (! $roleId) {
            $roleId = DB::table('roles')
                ->insertGetId([
                    'name' =>
                        'pembimbing_lapangan',

                    'guard_name' =>
                        'web',

                    'created_at' =>
                        now(),

                    'updated_at' =>
                        now(),
                ]);
        }

        $accounts = [
            [
                'name' =>
                    'Rina Marlina',

                'email' =>
                    'rina@pl.simmag.com',

                'old_email' =>
                    'hrd@nusantaradigital.test',

                'username' =>
                    'rina.pl',

                'identifier' =>
                    'PL-RINA',

                'nip' =>
                    'PL-NIP-RINA',
            ],
            [
                'name' =>
                    'Dedi Saputra',

                'email' =>
                    'dedi@pl.simmag.com',

                'old_email' =>
                    'career@tkm.test',

                'username' =>
                    'dedi.pl',

                'identifier' =>
                    'PL-DEDI',

                'nip' =>
                    'PL-NIP-DEDI',
            ],
            [
                'name' =>
                    'Salsa Putri',

                'email' =>
                    'salsa@pl.simmag.com',

                'old_email' =>
                    'admin@kreatifmedia.test',

                'username' =>
                    'salsa.pl',

                'identifier' =>
                    'PL-SALSA',

                'nip' =>
                    'PL-NIP-SALSA',
            ],
        ];

        DB::transaction(
            function () use (
                $accounts,
                $roleId
            ): void {
                foreach ($accounts as $account) {
                    $newEmail = strtolower(
                        trim($account['email'])
                    );

                    $oldEmail = strtolower(
                        trim($account['old_email'])
                    );

                    $existingUser = DB::table(
                        'users'
                    )
                        ->where(
                            function ($query) use (
                                $account,
                                $newEmail,
                                $oldEmail
                            ): void {
                                $query
                                    ->whereRaw(
                                        'LOWER(email) = ?',
                                        [$newEmail]
                                    )
                                    ->orWhereRaw(
                                        'LOWER(email) = ?',
                                        [$oldEmail]
                                    )
                                    ->orWhere(
                                        'name',
                                        $account['name']
                                    );
                            }
                        )
                        ->orderByDesc('id')
                        ->first();

                    $userId = $existingUser?->id;

                    $userData = [
                        'name' =>
                            $account['name'],

                        'email' =>
                            $newEmail,

                        'password' =>
                            Hash::make('password'),

                        'updated_at' =>
                            now(),
                    ];

                    if (
                        Schema::hasColumn(
                            'users',
                            'username'
                        )
                    ) {
                        $userData['username'] =
                            $this->uniqueUserValue(
                                'username',
                                $account['username'],
                                $userId
                            );
                    }

                    if (
                        Schema::hasColumn(
                            'users',
                            'identifier'
                        )
                    ) {
                        $userData['identifier'] =
                            $this->uniqueUserValue(
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
                        $userData['nip'] =
                            $this->uniqueUserValue(
                                'nip',
                                $account['nip'],
                                $userId
                            );
                    }

                    if (
                        Schema::hasColumn(
                            'users',
                            'email_verified_at'
                        )
                    ) {
                        $userData[
                            'email_verified_at'
                        ] = $existingUser
                            ?->email_verified_at
                            ?? now();
                    }

                    if (
                        Schema::hasColumn(
                            'users',
                            'is_active'
                        )
                    ) {
                        $userData['is_active'] =
                            true;
                    }

                    if ($userId) {
                        DB::table('users')
                            ->where(
                                'id',
                                $userId
                            )
                            ->update($userData);
                    } else {
                        $userData['created_at'] =
                            now();

                        $userId = DB::table(
                            'users'
                        )->insertGetId(
                            $userData
                        );
                    }

                    DB::table(
                        'model_has_roles'
                    )
                        ->where(
                            'model_id',
                            $userId
                        )
                        ->where(
                            'model_type',
                            User::class
                        )
                        ->delete();

                    DB::table(
                        'model_has_roles'
                    )->insert([
                        'role_id' =>
                            $roleId,

                        'model_type' =>
                            User::class,

                        'model_id' =>
                            $userId,
                    ]);

                    DB::table('internships')
                        ->where(
                            function ($query) use (
                                $account,
                                $newEmail,
                                $oldEmail,
                                $userId
                            ): void {
                                $query
                                    ->whereRaw(
                                        'LOWER(field_supervisor_name) = ?',
                                        [
                                            strtolower(
                                                $account['name']
                                            ),
                                        ]
                                    )
                                    ->orWhereRaw(
                                        'LOWER(field_supervisor_email) = ?',
                                        [$newEmail]
                                    )
                                    ->orWhereRaw(
                                        'LOWER(field_supervisor_email) = ?',
                                        [$oldEmail]
                                    )
                                    ->orWhere(
                                        'field_supervisor_id',
                                        $userId
                                    );
                            }
                        )
                        ->update([
                            'field_supervisor_id' =>
                                $userId,

                            'field_supervisor_name' =>
                                $account['name'],

                            'field_supervisor_email' =>
                                $newEmail,

                            'updated_at' =>
                                now(),
                        ]);
                }
            }
        );

        $this->command?->info(
            'Akun PL berhasil disimpan dengan password: password'
        );
    }

    private function uniqueUserValue(
        string $column,
        string $baseValue,
        ?int $exceptUserId
    ): string {
        $candidate = $baseValue;

        $counter = 1;

        while (
            DB::table('users')
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