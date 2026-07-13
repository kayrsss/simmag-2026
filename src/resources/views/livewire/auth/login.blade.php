@php
    $logoPath = public_path(
        'images/logo-simmag.png'
    );

    $logoVersion = file_exists($logoPath)
        ? filemtime($logoPath)
        : now()->timestamp;
@endphp

<div
    class="relative z-10 flex min-h-screen min-h-[100dvh] flex-col"
>
    <main
        class="flex flex-1 items-center justify-center px-4 py-8 sm:px-6"
    >
        <section
            class="w-full max-w-[430px] rounded-[28px] border border-slate-200/90 bg-white/95 p-6 shadow-login backdrop-blur-xl sm:p-8"
        >
            <header
                class="flex flex-col items-center text-center"
            >
                <div
                    class="flex h-[132px] w-[132px] items-center justify-center overflow-hidden rounded-[28px] border border-slate-100 bg-white shadow-sm"
                >
                    <img
                        src="{{ asset('images/logo-simmag.png') }}?v={{ $logoVersion }}"
                        alt="Logo SIMMAG"
                        class="block h-[128%] w-[128%] max-w-none shrink-0 object-contain object-center"
                    >
                </div>

                <h1
                    class="mt-5 font-display text-3xl font-extrabold gradient-text"
                >
                    SIMMAG
                </h1>

                <span
                    class="mt-3 inline-flex rounded-full bg-blue-50 px-4 py-1.5 text-[10px] font-extrabold uppercase tracking-[0.16em] text-blue-700"
                >
                    Sistem Monitoring Magang
                </span>
            </header>

            <div class="mt-8 text-center">
                <h2
                    class="font-display text-2xl font-bold leading-tight text-slate-900"
                >
                    Selamat Datang
                </h2>

                <p
                    class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500"
                >
                    Masuk menggunakan akun akademik Anda
                    untuk melanjutkan ke dashboard SIMMAG.
                </p>
            </div>

            @if (session('status'))
                <div
                    class="mt-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700"
                    role="status"
                >
                    <span
                        class="material-symbols-rounded mt-0.5 text-[19px]"
                    >
                        check_circle
                    </span>

                    <span>
                        {{ session('status') }}
                    </span>
                </div>
            @endif

            <form
                wire:submit="authenticate"
                class="mt-7 space-y-5"
            >
                <div>
                    <label
                        for="username"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Username
                    </label>

                    <div
                        @class([
                            'form-control flex min-h-[52px] items-center gap-3 rounded-xl border bg-slate-50 px-4',
                            'border-red-400' => $errors->has('username'),
                            'border-slate-200' => ! $errors->has('username'),
                        ])
                    >
                        <span
                            class="material-symbols-rounded text-[21px] text-slate-400"
                            aria-hidden="true"
                        >
                            person
                        </span>

                        <input
                            wire:model.blur="username"
                            id="username"
                            type="text"
                            class="w-full border-0 bg-transparent p-0 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-0 focus:ring-0"
                            placeholder="NIM / NIP / Username / Email"
                            autocomplete="username"
                            autofocus
                        >
                    </div>

                    @error('username')
                        <p
                            class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-red-600"
                        >
                            <span
                                class="material-symbols-rounded text-[16px]"
                            >
                                error
                            </span>

                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <div
                        class="mb-2 flex items-center justify-between gap-3"
                    >
                        <label
                            for="password"
                            class="block text-sm font-bold text-slate-700"
                        >
                            Password
                        </label>

                        @if (
                            Route::has(
                                'password.request'
                            )
                        )
                            <a
                                href="{{ route('password.request') }}"
                                class="text-xs font-bold text-blue-600 transition hover:text-blue-700 hover:underline"
                            >
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <div
                        @class([
                            'form-control flex min-h-[52px] items-center gap-3 rounded-xl border bg-slate-50 px-4',
                            'border-red-400' => $errors->has('password'),
                            'border-slate-200' => ! $errors->has('password'),
                        ])
                    >
                        <span
                            class="material-symbols-rounded text-[21px] text-slate-400"
                            aria-hidden="true"
                        >
                            lock
                        </span>

                        <input
                            wire:model="password"
                            id="password"
                            type="password"
                            class="w-full border-0 bg-transparent p-0 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-0 focus:ring-0"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                        >

                        <button
                            id="password-toggle"
                            type="button"
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-200 hover:text-slate-700"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            <span
                                id="password-toggle-icon"
                                class="material-symbols-rounded text-[20px]"
                            >
                                visibility
                            </span>
                        </button>
                    </div>

                    @error('password')
                        <p
                            class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-red-600"
                        >
                            <span
                                class="material-symbols-rounded text-[16px]"
                            >
                                error
                            </span>

                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div
                    class="flex items-center justify-between gap-4"
                >
                    <label
                        class="flex cursor-pointer items-center gap-2 text-sm text-slate-500"
                    >
                        <input
                            wire:model="remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        >

                        <span>
                            Ingat saya
                        </span>
                    </label>
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="authenticate"
                    class="login-button flex min-h-[52px] w-full items-center justify-center gap-2 rounded-xl px-5 text-sm font-extrabold text-white"
                >
                    <span
                        wire:loading.remove
                        wire:target="authenticate"
                    >
                        Masuk
                    </span>

                    <span
                        wire:loading.remove
                        wire:target="authenticate"
                        class="material-symbols-rounded text-[20px]"
                    >
                        login
                    </span>

                    <span
                        wire:loading
                        wire:target="authenticate"
                        class="loading-spinner h-[18px] w-[18px] rounded-full border-2 border-white/40 border-t-white"
                    ></span>

                    <span
                        wire:loading
                        wire:target="authenticate"
                    >
                        Memproses...
                    </span>
                </button>
            </form>

            <div
                class="relative my-6 flex items-center justify-center"
            >
                <div
                    class="absolute inset-x-0 h-px bg-slate-200"
                ></div>

                <span
                    class="relative bg-white px-3 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400"
                >
                    Akses Akademik
                </span>
            </div>

            <div
                class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4"
            >
                <div class="flex gap-3">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600"
                    >
                        <span
                            class="material-symbols-rounded text-[20px]"
                        >
                            info
                        </span>
                    </div>

                    <div>
                        <p
                            class="text-xs font-bold text-slate-800"
                        >
                            Gunakan akun SIMMAG Anda
                        </p>

                        <p
                            class="mt-1 text-[11px] leading-5 text-slate-500"
                        >
                            Mahasiswa menggunakan NIM,
                            dosen menggunakan NIP, dan admin
                            menggunakan username sistem.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer
        class="relative z-10 px-4 pb-7 text-center"
    >
        <p class="text-xs text-slate-500">
            © {{ now()->year }} SIMMAG —
            Sistem Monitoring Magang Mahasiswa
        </p>

        <p
            class="mt-1 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400"
        >
            Fakultas Ilmu Komputer
        </p>
    </footer>

    <script>
        function initializeSimmagPasswordToggle() {
            const passwordInput =
                document.getElementById(
                    'password'
                );

            const passwordToggle =
                document.getElementById(
                    'password-toggle'
                );

            const passwordToggleIcon =
                document.getElementById(
                    'password-toggle-icon'
                );

            if (
                ! passwordInput
                || ! passwordToggle
                || ! passwordToggleIcon
                || passwordToggle.dataset.initialized
                    === 'true'
            ) {
                return;
            }

            passwordToggle.dataset.initialized =
                'true';

            passwordToggle.addEventListener(
                'click',
                function () {
                    const passwordIsHidden =
                        passwordInput.type ===
                        'password';

                    passwordInput.type =
                        passwordIsHidden
                            ? 'text'
                            : 'password';

                    passwordToggleIcon.textContent =
                        passwordIsHidden
                            ? 'visibility_off'
                            : 'visibility';

                    passwordToggle.setAttribute(
                        'aria-pressed',
                        passwordIsHidden
                            ? 'true'
                            : 'false'
                    );

                    passwordToggle.setAttribute(
                        'aria-label',
                        passwordIsHidden
                            ? 'Sembunyikan password'
                            : 'Tampilkan password'
                    );

                    passwordInput.focus({
                        preventScroll: true,
                    });
                }
            );
        }

        document.addEventListener(
            'DOMContentLoaded',
            initializeSimmagPasswordToggle
        );

        document.addEventListener(
            'livewire:navigated',
            initializeSimmagPasswordToggle
        );
    </script>
</div>