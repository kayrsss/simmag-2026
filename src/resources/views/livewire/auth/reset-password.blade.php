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
                    class="flex h-[104px] w-[104px] items-center justify-center overflow-hidden rounded-[24px] border border-slate-100 bg-white shadow-sm"
                >
                    <img
                        src="{{ asset('images/logo-simmag.png') }}"
                        alt="Logo SIMMAG"
                        class="h-full w-full object-cover"
                    >
                </div>

                <h1
                    class="mt-5 font-display text-2xl font-extrabold gradient-text"
                >
                    Buat Password Baru
                </h1>

                <p
                    class="mx-auto mt-3 max-w-sm text-sm leading-6 text-slate-500"
                >
                    Masukkan password baru untuk akun SIMMAG Anda.
                </p>
            </header>

            <form
                wire:submit="resetPassword"
                class="mt-7 space-y-5"
            >
                <div>
                    <label
                        for="email"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Email
                    </label>

                    <div
                        @class([
                            'form-control flex min-h-[52px] items-center gap-3 rounded-xl border bg-slate-50 px-4',
                            'border-red-400' => $errors->has('email'),
                            'border-slate-200' => ! $errors->has('email'),
                        ])
                    >
                        <span
                            class="material-symbols-rounded text-[21px] text-slate-400"
                        >
                            mail
                        </span>

                        <input
                            wire:model="email"
                            id="email"
                            type="email"
                            class="w-full border-0 bg-transparent p-0 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-0 focus:ring-0"
                            autocomplete="email"
                            readonly
                        >
                    </div>

                    @error('email')
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
                    <label
                        for="new-password"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Password Baru
                    </label>

                    <div
                        @class([
                            'form-control flex min-h-[52px] items-center gap-3 rounded-xl border bg-slate-50 px-4',
                            'border-red-400' => $errors->has('password'),
                            'border-slate-200' => ! $errors->has('password'),
                        ])
                    >
                        <span
                            class="material-symbols-rounded text-[21px] text-slate-400"
                        >
                            lock_reset
                        </span>

                        <input
                            wire:model="password"
                            id="new-password"
                            type="password"
                            class="w-full border-0 bg-transparent p-0 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-0 focus:ring-0"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                        >
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

                <div>
                    <label
                        for="password-confirmation"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Konfirmasi Password
                    </label>

                    <div
                        class="form-control flex min-h-[52px] items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4"
                    >
                        <span
                            class="material-symbols-rounded text-[21px] text-slate-400"
                        >
                            verified_user
                        </span>

                        <input
                            wire:model="password_confirmation"
                            id="password-confirmation"
                            type="password"
                            class="w-full border-0 bg-transparent p-0 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-0 focus:ring-0"
                            placeholder="Ulangi password baru"
                            autocomplete="new-password"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="resetPassword"
                    class="login-button flex min-h-[52px] w-full items-center justify-center gap-2 rounded-xl px-5 text-sm font-extrabold text-white"
                >
                    <span
                        wire:loading.remove
                        wire:target="resetPassword"
                        class="material-symbols-rounded text-[20px]"
                    >
                        save
                    </span>

                    <span
                        wire:loading.remove
                        wire:target="resetPassword"
                    >
                        Simpan Password Baru
                    </span>

                    <span
                        wire:loading
                        wire:target="resetPassword"
                        class="loading-spinner h-[18px] w-[18px] rounded-full border-2 border-white/40 border-t-white"
                    ></span>

                    <span
                        wire:loading
                        wire:target="resetPassword"
                    >
                        Menyimpan...
                    </span>
                </button>
            </form>

            <a
                href="{{ route('login') }}"
                class="mt-5 flex min-h-[48px] w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
            >
                <span
                    class="material-symbols-rounded text-[19px]"
                >
                    arrow_back
                </span>

                Kembali ke Login
            </a>
        </section>
    </main>

    <footer
        class="relative z-10 px-4 pb-7 text-center"
    >
        <p class="text-xs text-slate-500">
            © {{ now()->year }} SIMMAG —
            Sistem Monitoring Magang Mahasiswa
        </p>
    </footer>
</div>