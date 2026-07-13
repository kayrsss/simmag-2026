<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SimmagRole
{
    public const ADMIN = 'admin';

    public const MAHASISWA = 'mahasiswa';

    public const DOSEN_PEMBIMBING = 'dosen_pembimbing';

    public const PEMBIMBING_LAPANGAN = 'pembimbing_lapangan';

    public static function all(): array
    {
        return [
            self::ADMIN,
            self::MAHASISWA,
            self::DOSEN_PEMBIMBING,
            self::PEMBIMBING_LAPANGAN,
        ];
    }

    public static function normalize(
        string $role
    ): string {
        $normalized = Str::of($role)
            ->trim()
            ->lower()
            ->replace([
                ' ',
                '-',
            ], '_')
            ->toString();

        return match ($normalized) {
            'administrator',
            'admin_fakultas',
            'super_admin' =>
                self::ADMIN,

            'student',
            'mahasiswa_peserta_magang' =>
                self::MAHASISWA,

            'dosen',
            'dospem',
            'supervisor_lecturer' =>
                self::DOSEN_PEMBIMBING,

            'pl',
            'field_supervisor',
            'mentor_lapangan' =>
                self::PEMBIMBING_LAPANGAN,

            default =>
                $normalized,
        };
    }

    public static function resolve(
        User $user
    ): string {
        return self::roles($user)
            ->first()
            ?? self::MAHASISWA;
    }

    public static function roles(
        User $user
    ): Collection {
        $roles = collect();

        if (
            method_exists(
                $user,
                'getRoleNames'
            )
        ) {
            $roles = $user
                ->getRoleNames()
                ->map(
                    fn (mixed $role): string =>
                        self::normalize(
                            (string) $role
                        )
                );
        }

        if (
            filled($user->role ?? null)
        ) {
            $roles->push(
                self::normalize(
                    (string) $user->role
                )
            );
        }

        return $roles
            ->filter()
            ->unique()
            ->values();
    }

    public static function has(
        User $user,
        string $role
    ): bool {
        return self::roles($user)
            ->contains(
                self::normalize($role)
            );
    }

    public static function hasAny(
        User $user,
        array|string $roles
    ): bool {
        $allowedRoles = collect(
            is_array($roles)
                ? $roles
                : [$roles]
        )
            ->flatMap(
                function (mixed $role): array {
                    if (
                        is_string($role)
                        && str_contains(
                            $role,
                            '|'
                        )
                    ) {
                        return explode(
                            '|',
                            $role
                        );
                    }

                    if (
                        is_string($role)
                        && str_contains(
                            $role,
                            ','
                        )
                    ) {
                        return explode(
                            ',',
                            $role
                        );
                    }

                    return [
                        (string) $role,
                    ];
                }
            )
            ->map(
                fn (mixed $role): string =>
                    self::normalize(
                        (string) $role
                    )
            )
            ->filter()
            ->unique()
            ->values();

        if ($allowedRoles->isEmpty()) {
            return false;
        }

        return self::roles($user)
            ->intersect($allowedRoles)
            ->isNotEmpty();
    }

    public static function dashboardRouteName(
        User $user
    ): string {
        $role = self::resolve($user);

        $candidates = match ($role) {
            self::ADMIN => [
                'dashboard.admin',
            ],

            self::DOSEN_PEMBIMBING => [
                'dashboard.dosen',
                'dashboard.dosen-pembimbing',
            ],

            self::PEMBIMBING_LAPANGAN => [
                'dashboard.pembimbing-lapangan',
            ],

            default => [
                'dashboard.mahasiswa',
            ],
        };

        foreach ($candidates as $candidate) {
            if (Route::has($candidate)) {
                return $candidate;
            }
        }

        return match ($role) {
            self::ADMIN =>
                'dashboard.admin',

            self::DOSEN_PEMBIMBING =>
                'dashboard.dosen-pembimbing',

            self::PEMBIMBING_LAPANGAN =>
                'dashboard.pembimbing-lapangan',

            default =>
                'dashboard.mahasiswa',
        };
    }

    public static function dashboardPath(
        User $user
    ): string {
        return match (self::resolve($user)) {
            self::ADMIN =>
                '/dashboard/admin',

            self::DOSEN_PEMBIMBING =>
                '/dashboard/dosen-pembimbing',

            self::PEMBIMBING_LAPANGAN =>
                '/dashboard/pembimbing-lapangan',

            default =>
                '/dashboard/mahasiswa',
        };
    }

    public static function isValid(
        string $role
    ): bool {
        return in_array(
            self::normalize($role),
            self::all(),
            true
        );
    }
}