<?php

namespace App\Filament\Admin\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait HasSimmagResourceAccess
{
    public static function shouldRegisterNavigation(): bool
    {
        return static::allowsRole(static::getRoleConfig('navigationRoles', ['admin']));
    }

    public static function canViewAny(): bool
    {
        return static::allowsRole(static::getRoleConfig('viewAnyRoles', ['admin']));
    }

    public static function canCreate(): bool
    {
        return static::allowsRole(static::getRoleConfig('createRoles', ['admin']));
    }

    public static function canView(Model $record): bool
    {
        return static::allowsRole(static::getRoleConfig('viewRoles', ['admin']));
    }

    public static function canEdit(Model $record): bool
    {
        return static::allowsRole(static::getRoleConfig('editRoles', ['admin']));
    }

    public static function canDelete(Model $record): bool
    {
        return static::allowsRole(static::getRoleConfig('deleteRoles', ['admin']));
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (! static::useRoleRecordScope()) {
            return $query;
        }

        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if (static::userHasRole($user, 'admin')) {
            return $query;
        }

        return static::applyRecordScope($query);
    }

    protected static function getRoleConfig(string $property, array $default = []): array
    {
        if (! property_exists(static::class, $property)) {
            return $default;
        }

        return static::${$property};
    }

    protected static function useRoleRecordScope(): bool
    {
        if (! property_exists(static::class, 'useRoleRecordScope')) {
            return false;
        }

        return static::$useRoleRecordScope;
    }

    protected static function allowsRole(array $roles): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (in_array('*', $roles, true)) {
            return true;
        }

        foreach ($roles as $role) {
            if (static::userHasRole($user, $role)) {
                return true;
            }
        }

        return false;
    }

    protected static function userHasRole($user, string $role): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
            return true;
        }

        return ($user->role ?? null) === $role;
    }

    protected static function currentRole(): ?string
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        if (method_exists($user, 'getRoleNames')) {
            return $user->getRoleNames()->first() ?: ($user->role ?? null);
        }

        return $user->role ?? null;
    }

    protected static function applyRecordScope(Builder $query): Builder
    {
        $user = auth()->user();
        $role = static::currentRole();

        if (! $user || ! $role) {
            return $query->whereRaw('1 = 0');
        }

        $table = $query->getModel()->getTable();

        if (! Schema::hasTable($table)) {
            return $query;
        }

        if ($table === 'internships') {
            return static::scopeByRoleColumns($query, $table, $role, $user);
        }

        if (Schema::hasColumn($table, 'internship_id')) {
            $internshipIds = static::getAllowedInternshipIds($role, $user);

            if (empty($internshipIds)) {
                return $query->whereRaw('1 = 0');
            }

            return $query->whereIn($table . '.internship_id', $internshipIds);
        }

        return static::scopeByRoleColumns($query, $table, $role, $user);
    }

    protected static function scopeByRoleColumns(Builder $query, string $table, string $role, $user): Builder
    {
        $rules = static::roleColumnRules($role);

        $idColumns = array_values(array_filter(
            $rules['id'],
            fn (string $column): bool => Schema::hasColumn($table, $column)
        ));

        $emailColumns = array_values(array_filter(
            $rules['email'],
            fn (string $column): bool => Schema::hasColumn($table, $column)
        ));

        if (empty($idColumns) && empty($emailColumns)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($table, $idColumns, $emailColumns, $user) {
            foreach ($idColumns as $column) {
                $q->orWhere($table . '.' . $column, $user->id);
            }

            foreach ($emailColumns as $column) {
                $q->orWhere($table . '.' . $column, $user->email);
            }
        });
    }

    protected static function getAllowedInternshipIds(string $role, $user): array
    {
        if (! Schema::hasTable('internships')) {
            return [];
        }

        $rules = static::roleColumnRules($role);

        $idColumns = array_values(array_filter(
            $rules['id'],
            fn (string $column): bool => Schema::hasColumn('internships', $column)
        ));

        $emailColumns = array_values(array_filter(
            $rules['email'],
            fn (string $column): bool => Schema::hasColumn('internships', $column)
        ));

        if (empty($idColumns) && empty($emailColumns)) {
            return [];
        }

        return DB::table('internships')
            ->where(function ($q) use ($idColumns, $emailColumns, $user) {
                foreach ($idColumns as $column) {
                    $q->orWhere($column, $user->id);
                }

                foreach ($emailColumns as $column) {
                    $q->orWhere($column, $user->email);
                }
            })
            ->pluck('id')
            ->toArray();
    }

    protected static function roleColumnRules(string $role): array
    {
        return match ($role) {
            'mahasiswa' => [
                'id' => [
                    'student_id',
                    'user_id',
                    'mahasiswa_id',
                    'student_user_id',
                    'mahasiswa_user_id',
                    'created_by',
                    'created_by_id',
                ],
                'email' => [
                    'student_email',
                    'mahasiswa_email',
                ],
            ],

            'dosen_pembimbing' => [
                'id' => [
                    'supervisor_lecturer_id',
                    'lecturer_id',
                    'dosen_id',
                    'dosen_pembimbing_id',
                    'lecturer_user_id',
                    'dosen_pembimbing_user_id',
                ],
                'email' => [
                    'supervisor_lecturer_email',
                    'lecturer_email',
                    'dosen_pembimbing_email',
                ],
            ],

            'pembimbing_lapangan' => [
                'id' => [
                    'field_supervisor_id',
                    'supervisor_id',
                    'pembimbing_lapangan_id',
                    'field_supervisor_user_id',
                    'pembimbing_lapangan_user_id',
                ],
                'email' => [
                    'field_supervisor_email',
                    'supervisor_email',
                    'pembimbing_lapangan_email',
                ],
            ],

            default => [
                'id' => [],
                'email' => [],
            ],
        };
    }
}