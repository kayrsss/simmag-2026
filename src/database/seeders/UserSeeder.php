<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    private const DEFAULT_PASSWORD =
        'Simmag123!';

    public function run(): void
    {
        foreach (
            [
                'admin',
                'mahasiswa',
                'dosen_pembimbing',
                'pembimbing_lapangan',
            ] as $roleName
        ) {
            Role::findOrCreate(
                $roleName,
                'web'
            );
        }

        $programStudyId = null;

        if (
            Schema::hasTable(
                'program_studies'
            )
        ) {
            $programStudyId = DB::table(
                'program_studies'
            )
                ->whereIn(
                    'code',
                    [
                        'SI',
                        'TI',
                    ]
                )
                ->orderByRaw(
                    "FIELD(code, 'SI', 'TI')"
                )
                ->value('id');

            $programStudyId ??=
                DB::table(
                    'program_studies'
                )
                    ->orderBy('id')
                    ->value('id');
        }

        $accounts = [
            [
                'role' => 'admin',

                'identity' => [
                    'email' =>
                        'admin@simmag.com',

                    'username' =>
                        'ADM-001',

                    'identifier' =>
                        'ADM001',

                    'nip' =>
                        '198501012010011001',
                ],

                'attributes' => [
                    'name' =>
                        'Admin Fakultas SIMMAG',

                    'email' =>
                        'admin@simmag.com',

                    'username' =>
                        'ADM-001',

                    'identifier' =>
                        'ADM001',

                    'nip' =>
                        '198501012010011001',

                    'role' =>
                        'admin',

                    'institution_name' =>
                        'Universitas Esa Unggul',
                ],
            ],

            [
                'role' => 'mahasiswa',

                'identity' => [
                    'email' =>
                        'farhan.ramadhan@student.simmag.test',

                    'username' =>
                        '2023101006',

                    'identifier' =>
                        '2023101006',

                    'nim' =>
                        '2023101006',
                ],

                'attributes' => [
                    'name' =>
                        'Farhan Ramadhan',

                    'email' =>
                        'farhan.ramadhan@student.simmag.test',

                    'username' =>
                        '2023101006',

                    'identifier' =>
                        '2023101006',

                    'nim' =>
                        '2023101006',

                    'role' =>
                        'mahasiswa',

                    'institution_name' =>
                        'Universitas Esa Unggul',

                    'program_study_id' =>
                        $programStudyId,
                ],
            ],

            [
                'role' => 'dosen_pembimbing',

                'identity' => [
                    'email' =>
                        'drandipratamaskommkom@lecturer.simmag.test',

                    'username' =>
                        '0410019001',

                    'identifier' =>
                        '0410019001',

                    'nidn' =>
                        '0410019001',
                ],

                'attributes' => [
                    'name' =>
                        'Dr. Andi Pratama, S.Kom., M.Kom.',

                    'email' =>
                        'drandipratamaskommkom@lecturer.simmag.test',

                    'username' =>
                        '0410019001',

                    'identifier' =>
                        '0410019001',

                    'nidn' =>
                        '0410019001',

                    'role' =>
                        'dosen_pembimbing',

                    'institution_name' =>
                        'Universitas Esa Unggul',

                    'program_study_id' =>
                        $programStudyId,
                ],
            ],

            [
                'role' => 'pembimbing_lapangan',

                'identity' => [
                    'email' =>
                        'pl@simmag.com',

                    'username' =>
                        'PL-00001',

                    'identifier' =>
                        'PL001',
                ],

                'attributes' => [
                    'name' =>
                        'Syifa Fauziah',

                    'email' =>
                        'pl@simmag.com',

                    'username' =>
                        'PL-00001',

                    'identifier' =>
                        'PL001',

                    'role' =>
                        'pembimbing_lapangan',

                    'institution_name' =>
                        'PT Nusantara Digital Solusi',
                ],
            ],
        ];

        DB::transaction(
            function () use (
                $accounts
            ): void {
                foreach ($accounts as $account) {
                    $this->createOrRepairAccount(
                        role: $account['role'],
                        identity: $account['identity'],
                        attributes: $account['attributes'],
                    );
                }

                if (
                    Schema::hasTable(
                        'sessions'
                    )
                ) {
                    DB::table('sessions')
                        ->delete();
                }
            }
        );

        $this->command?->newLine();

        $this->command?->info(
            'AKUN SIMMAG BERHASIL DIPERBAIKI'
        );

        $this->command?->table(
            [
                'Peran',
                'Nama',
                'ID Login',
                'Password',
            ],
            [
                [
                    'Admin Fakultas',
                    'Admin Fakultas SIMMAG',
                    '198501012010011001',
                    self::DEFAULT_PASSWORD,
                ],
                [
                    'Mahasiswa',
                    'Farhan Ramadhan',
                    '2023101006',
                    self::DEFAULT_PASSWORD,
                ],
                [
                    'Dosen Pembimbing',
                    'Dr. Andi Pratama',
                    '0410019001',
                    self::DEFAULT_PASSWORD,
                ],
                [
                    'Pembimbing Lapangan',
                    'Syifa Fauziah',
                    'PL001',
                    self::DEFAULT_PASSWORD,
                ],
            ]
        );
    }

    private function createOrRepairAccount(
        string $role,
        array $identity,
        array $attributes
    ): void {
        $matches = $this->findMatchingUsers(
            $identity
        );

        $user = $matches->first();

        if (! $user) {
            $user = new User();
        }

        $data = [];

        foreach (
            $attributes
            as $column => $value
        ) {
            if (
                ! Schema::hasColumn(
                    'users',
                    $column
                )
            ) {
                continue;
            }

            if (
                $value === null
                && $column ===
                    'program_study_id'
            ) {
                continue;
            }

            if (
                $this->isUniqueIdentityColumn(
                    $column
                )
                && $this->identityOwnedByOtherUser(
                    column: $column,
                    value: $value,
                    userId: $user->exists
                        ? $user->id
                        : null
                )
            ) {
                continue;
            }

            $data[$column] = $value;
        }

        $data['password'] =
            Hash::make(
                self::DEFAULT_PASSWORD
            );

        if (
            Schema::hasColumn(
                'users',
                'email_verified_at'
            )
        ) {
            $data['email_verified_at'] =
                now();
        }

        if (
            Schema::hasColumn(
                'users',
                'is_active'
            )
        ) {
            $data['is_active'] = true;
        }

        $user->forceFill($data);

        $user->save();

        $user->syncRoles([
            $role,
        ]);

        $allMatches = $this
            ->findMatchingUsers(
                $identity
            )
            ->push($user)
            ->unique('id');

        foreach ($allMatches as $matchingUser) {
            $matchingUser->forceFill([
                'password' =>
                    Hash::make(
                        self::DEFAULT_PASSWORD
                    ),
            ]);

            if (
                Schema::hasColumn(
                    'users',
                    'role'
                )
            ) {
                $matchingUser->role =
                    $role;
            }

            if (
                Schema::hasColumn(
                    'users',
                    'is_active'
                )
            ) {
                $matchingUser->is_active =
                    true;
            }

            if (
                Schema::hasColumn(
                    'users',
                    'email_verified_at'
                )
            ) {
                $matchingUser
                    ->email_verified_at =
                    now();
            }

            $matchingUser->save();

            $matchingUser->syncRoles([
                $role,
            ]);
        }

        $verifiedUser = $user->fresh();

        if (
            ! $verifiedUser
            || ! Hash::check(
                self::DEFAULT_PASSWORD,
                (string) $verifiedUser->password
            )
        ) {
            throw new RuntimeException(
                "Password akun {$attributes['name']} gagal diverifikasi."
            );
        }

        if (
            ! $verifiedUser->hasRole(
                $role
            )
        ) {
            throw new RuntimeException(
                "Role akun {$attributes['name']} gagal diverifikasi."
            );
        }

        $loginId =
            $identity['nim']
            ?? $identity['nidn']
            ?? $identity['nip']
            ?? $identity['identifier']
            ?? $identity['username']
            ?? $identity['email'];

        $this->command?->info(
            "VERIFIED: {$loginId} / "
            . self::DEFAULT_PASSWORD
        );
    }

    private function findMatchingUsers(
        array $identity
    ): Collection {
        $availableIdentity = collect(
            $identity
        )
            ->filter(
                function (
                    mixed $value,
                    string $column
                ): bool {
                    return filled($value)
                        && Schema::hasColumn(
                            'users',
                            $column
                        );
                }
            );

        if ($availableIdentity->isEmpty()) {
            return collect();
        }

        return User::query()
            ->where(
                function ($query) use (
                    $availableIdentity
                ): void {
                    $query->whereRaw(
                        '1 = 0'
                    );

                    foreach (
                        $availableIdentity
                        as $column => $value
                    ) {
                        if ($column === 'email') {
                            $query->orWhereRaw(
                                'LOWER(TRIM(email)) = ?',
                                [
                                    mb_strtolower(
                                        trim(
                                            (string) $value
                                        )
                                    ),
                                ]
                            );

                            continue;
                        }

                        $query->orWhere(
                            $column,
                            $value
                        );
                    }
                }
            )
            ->orderByDesc('id')
            ->get();
    }

    private function isUniqueIdentityColumn(
        string $column
    ): bool {
        return in_array(
            $column,
            [
                'email',
                'username',
                'identifier',
                'nim',
                'nidn',
                'nip',
            ],
            true
        );
    }

    private function identityOwnedByOtherUser(
        string $column,
        mixed $value,
        ?int $userId
    ): bool {
        if (! filled($value)) {
            return false;
        }

        return User::query()
            ->where(
                $column,
                $value
            )
            ->when(
                $userId !== null,
                fn ($query) =>
                    $query->where(
                        'id',
                        '!=',
                        $userId
                    )
            )
            ->exists();
    }
}