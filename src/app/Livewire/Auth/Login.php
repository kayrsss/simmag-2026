<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    public string $username = '';

    public string $password = '';

    public bool $remember = false;

    protected function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
            ],
            'remember' => [
                'boolean',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'username.required' =>
                'NIM, NIDN, NIP, username, atau email wajib diisi.',

            'password.required' =>
                'Password wajib diisi.',

            'password.min' =>
                'Password minimal 8 karakter.',
        ];
    }

    public function authenticate(): mixed
    {
        $this->validate();

        $identity = mb_strtolower(
            trim($this->username)
        );

        $this->ensureNotLocked(
            $identity
        );

        $user = $this->findUsers(
            $identity
        )->first(
            fn (User $candidate): bool =>
                Hash::check(
                    $this->password,
                    (string) $candidate->password
                )
        );

        if (! $user) {
            $this->recordFailedAttempt(
                $identity
            );

            throw ValidationException::withMessages([
                'username' =>
                    'Identitas atau password yang dimasukkan salah.',
            ]);
        }

        if (
            Schema::hasColumn(
                'users',
                'is_active'
            )
            && $user->is_active !== null
            && ! (bool) $user->is_active
        ) {
            throw ValidationException::withMessages([
                'username' =>
                    'Akun sedang dinonaktifkan. Hubungi Admin Fakultas.',
            ]);
        }

        $role = $this->resolveRole(
            $user
        );

        if (
            ! in_array(
                $role,
                [
                    'admin',
                    'mahasiswa',
                    'dosen_pembimbing',
                    'pembimbing_lapangan',
                ],
                true
            )
        ) {
            throw ValidationException::withMessages([
                'username' =>
                    'Role akun belum sesuai dengan sistem SIMMAG.',
            ]);
        }

        $this->clearLoginState(
            $identity
        );

        Auth::guard('web')->login(
            $user,
            $this->remember
        );

        request()
            ->session()
            ->regenerate();

        request()
            ->session()
            ->put(
                'last_activity_at',
                now()->timestamp
            );

        return redirect()->to(
            match ($role) {
                'admin' =>
                    '/dashboard/admin',

                'dosen_pembimbing' =>
                    '/dashboard/dosen-pembimbing',

                'pembimbing_lapangan' =>
                    '/dashboard/pembimbing-lapangan',

                default =>
                    '/dashboard/mahasiswa',
            }
        );
    }

    private function findUsers(
        string $identity
    ): Collection {
        $columns = collect([
            'nim',
            'nidn',
            'nip',
            'identifier',
            'username',
            'email',
        ])
            ->filter(
                fn (string $column): bool =>
                    Schema::hasColumn(
                        'users',
                        $column
                    )
            )
            ->values();

        if ($columns->isEmpty()) {
            throw ValidationException::withMessages([
                'username' =>
                    'Kolom identitas pengguna belum tersedia.',
            ]);
        }

        return User::query()
            ->where(
                function ($query) use (
                    $columns,
                    $identity
                ): void {
                    $query->whereRaw('1 = 0');

                    foreach ($columns as $column) {
                        $query->orWhereRaw(
                            "LOWER(TRIM(CAST(`{$column}` AS CHAR))) = ?",
                            [$identity]
                        );
                    }
                }
            )
            ->orderByDesc('id')
            ->get();
    }

    private function resolveRole(
        User $user
    ): string {
        $role = null;

        if (
            method_exists(
                $user,
                'getRoleNames'
            )
        ) {
            $role = $user
                ->getRoleNames()
                ->first();
        }

        if (! filled($role)) {
            $role = $user->role
                ?? 'mahasiswa';
        }

        $normalized = str((string) $role)
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
                'admin',

            'student' =>
                'mahasiswa',

            'dosen',
            'dospem' =>
                'dosen_pembimbing',

            'pl',
            'field_supervisor',
            'mentor_lapangan' =>
                'pembimbing_lapangan',

            default =>
                $normalized,
        };
    }

    private function ensureNotLocked(
        string $identity
    ): void {
        $lockedUntil = Cache::get(
            $this->lockKey(
                $identity
            )
        );

        if (! $lockedUntil) {
            return;
        }

        $remainingSeconds = max(
            1,
            (int) $lockedUntil
                - now()->timestamp
        );

        $remainingMinutes = max(
            1,
            (int) ceil(
                $remainingSeconds / 60
            )
        );

        throw ValidationException::withMessages([
            'username' =>
                "Akun dikunci sementara. Coba kembali dalam {$remainingMinutes} menit.",
        ]);
    }

    private function recordFailedAttempt(
        string $identity
    ): void {
        $attemptKey = $this->attemptKey(
            $identity
        );

        $attempts = (int) Cache::get(
            $attemptKey,
            0
        );

        $attempts++;

        Cache::put(
            $attemptKey,
            $attempts,
            now()->addMinutes(5)
        );

        if ($attempts < 5) {
            return;
        }

        Cache::put(
            $this->lockKey(
                $identity
            ),
            now()
                ->addMinutes(30)
                ->timestamp,
            now()->addMinutes(30)
        );

        Cache::forget(
            $attemptKey
        );
    }

    private function clearLoginState(
        string $identity
    ): void {
        Cache::forget(
            $this->attemptKey(
                $identity
            )
        );

        Cache::forget(
            $this->lockKey(
                $identity
            )
        );
    }

    private function attemptKey(
        string $identity
    ): string {
        return sprintf(
            'simmag:login:attempt:%s:%s',
            sha1($identity),
            sha1(
                (string) request()->ip()
            )
        );
    }

    private function lockKey(
        string $identity
    ): string {
        return sprintf(
            'simmag:login:lock:%s:%s',
            sha1($identity),
            sha1(
                (string) request()->ip()
            )
        );
    }

    public function render()
    {
        return view(
            'livewire.auth.login'
        );
    }
}