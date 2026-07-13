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
                    Lupa Password
                </h1>

                <p
                    class="mx-auto mt-3 max-w-sm text-sm leading-6 text-slate-500"
                >
                    Masukkan email yang terdaftar pada akun
                    SIMMAG. Sistem akan mengirimkan tautan
                    untuk membuat password baru.
                </p>
            </header>

            @if (session('status'))
                <div
                    class="mt-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700"
                    role="status"
                >
                    <span
                        class="material-symbols-rounded mt-0.5 text-[19px]"
                    >
                        mark_email_read
                    </span>

                    <span>
                        {{ session('status') }}
                    </span>
                </div>
            @endif

            <form
                wire:submit="sendResetLink"
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
                            wire:model.blur="email"
                            id="email"
                            type="email"
                            class="w-full border-0 bg-transparent p-0 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-0 focus:ring-0"
                            placeholder="nama@email.com"
                            autocomplete="email"
                            autofocus
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

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="sendResetLink"
                    class="login-button flex min-h-[52px] w-full items-center justify-center gap-2 rounded-xl px-5 text-sm font-extrabold text-white"
                >
                    <span
                        wire:loading.remove
                        wire:target="sendResetLink"
                        class="material-symbols-rounded text-[20px]"
                    >
                        send
                    </span>

                    <span
                        wire:loading.remove
                        wire:target="sendResetLink"
                    >
                        Kirim Tautan Reset
                    </span>

                    <span
                        wire:loading
                        wire:target="sendResetLink"
                        class="loading-spinner h-[18px] w-[18px] rounded-full border-2 border-white/40 border-t-white"
                    ></span>

                    <span
                        wire:loading
                        wire:target="sendResetLink"
                    >
                        Mengirim...
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

            <div
                class="mt-6 rounded-2xl border border-blue-100 bg-blue-50/70 p-4"
            >
                <div class="flex gap-3">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600"
                    >
                        <span
                            class="material-symbols-rounded text-[20px]"
                        >
                            shield
                        </span>
                    </div>

                    <p
                        class="text-[11px] leading-5 text-slate-500"
                    >
                        Gunakan email yang tercatat pada akun
                        mahasiswa, dosen, Pembimbing Lapangan,
                        atau administrator.
                    </p>
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
    </footer>
</div>