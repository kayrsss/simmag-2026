<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Spatie\Permission\Models\Role;

class RepairFarhanLoginSeeder extends Seeder
{
    private const NIM = '2023101006';

    private const EMAIL =
        'farhan.ramadhan@student.simmag.test';

    private const PASSWORD =
        'Simmag123!';

    public function run(): void
    {
        $role = Role::findOrCreate(
            'mahasiswa',
            'web'
        );

        DB::transaction(
            function () use ($role): void {
                $user = User::query()
                    ->where(
                        function ($query): void {
                            $query
                                ->where(
                                    'id',
                                    10
                                )
                                ->orWhere(
                                    'name',
                                    'Farhan Ramadhan'
                                )
                                ->orWhereRaw(
                                    'LOWER(email) = ?',
                                    [
                                        strtolower(
                                            self::EMAIL
                                        ),
                                    ]
                                );

                            if (
                                Schema::hasColumn(
                                    'users',
                                    'nim'
                                )
                            ) {
                                $query->orWhere(
                                    'nim',
                                    self::NIM
                                );
                            }

                            if (
                                Schema::hasColumn(
                                    'users',
                                    'username'
                                )
                            ) {
                                $query->orWhere(
                                    'username',
                                    self::NIM
                                );
                            }

                            if (
                                Schema::hasColumn(
                                    'users',
                                    'identifier'
                                )
                            ) {
                                $query->orWhere(
                                    'identifier',
                                    self::NIM
                                );
                            }
                        }
                    )
                    ->orderBy('id')
                    ->first();

                if (! $user) {
                    $user = new User();
                }

                $attributes = [
                    'name' =>
                        'Farhan Ramadhan',

                    'email' =>
                        self::EMAIL,

                    'password' =>
                        Hash::make(
                            self::PASSWORD
                        ),
                ];

                if (
                    Schema::hasColumn(
                        'users',
                        'username'
                    )
                ) {
                    $attributes['username'] =
                        self::NIM;
                }

                if (
                    Schema::hasColumn(
                        'users',
                        'identifier'
                    )
                ) {
                    $attributes['identifier'] =
                        self::NIM;
                }

                if (
                    Schema::hasColumn(
                        'users',
                        'nim'
                    )
                ) {
                    $attributes['nim'] =
                        self::NIM;
                }

                if (
                    Schema::hasColumn(
                        'users',
                        'role'
                    )
                ) {
                    $attributes['role'] =
                        'mahasiswa';
                }

                if (
                    Schema::hasColumn(
                        'users',
                        'institution_name'
                    )
                ) {
                    $attributes[
                        'institution_name'
                    ] = 'Universitas Esa Unggul';
                }

                if (
                    Schema::hasColumn(
                        'users',
                        'email_verified_at'
                    )
                ) {
                    $attributes[
                        'email_verified_at'
                    ] = now();
                }

                if (
                    Schema::hasColumn(
                        'users',
                        'is_active'
                    )
                ) {
                    $attributes['is_active'] =
                        true;
                }

                $user->forceFill(
                    $attributes
                );

                $user->save();

                $user->syncRoles([
                    $role->name,
                ]);

                $user = $user->fresh();

                if (! $user) {
                    throw new RuntimeException(
                        'Akun Farhan gagal disimpan.'
                    );
                }

                if (
                    ! Hash::check(
                        self::PASSWORD,
                        (string) $user->password
                    )
                ) {
                    throw new RuntimeException(
                        'Password akun Farhan gagal diverifikasi.'
                    );
                }

                if (
                    ! $user->hasRole(
                        'mahasiswa'
                    )
                ) {
                    throw new RuntimeException(
                        'Role mahasiswa Farhan gagal diverifikasi.'
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

                Cache::flush();

                $this->command?->newLine();

                $this->command?->info(
                    'AKUN FARHAN BERHASIL DIPERBAIKI'
                );

                $this->command?->table(
                    [
                        'ID',
                        'Nama',
                        'NIM',
                        'Email',
                        'Password',
                        'Role',
                    ],
                    [
                        [
                            $user->id,
                            $user->name,
                            self::NIM,
                            $user->email,
                            self::PASSWORD,
                            'mahasiswa',
                        ],
                    ]
                );
            }
        );
    }
}