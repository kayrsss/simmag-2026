<!DOCTYPE html>
<html
    class="light"
    lang="id"
>
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Login - SIMMAG</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet"
    >

    <script>
        tailwind.config = {
            darkMode: 'class',

            theme: {
                extend: {
                    colors: {
                        'on-background': '#191c1e',
                        'primary-container': '#2563eb',
                        'on-error-container': '#93000a',
                        'on-primary-container': '#eeefff',
                        'surface-container-high': '#e6e8ea',
                        'primary-fixed': '#dbe1ff',
                        'on-secondary': '#ffffff',
                        'tertiary-container': '#bc4800',
                        'secondary-fixed-dim': '#d2bbff',
                        'background': '#f7f9fb',
                        'secondary': '#712ae2',
                        'on-surface-variant': '#434655',
                        'surface-container-highest': '#e0e3e5',
                        'surface-tint': '#0053db',
                        'outline': '#737686',
                        'secondary-container': '#8a4cfc',
                        'on-error': '#ffffff',
                        'surface-container-low': '#f2f4f6',
                        'surface-container-lowest': '#ffffff',
                        'secondary-fixed': '#eaddff',
                        'inverse-primary': '#b4c5ff',
                        'primary-fixed-dim': '#b4c5ff',
                        'on-tertiary': '#ffffff',
                        'tertiary': '#943700',
                        'tertiary-fixed-dim': '#ffb596',
                        'primary': '#004ac6',
                        'outline-variant': '#c3c6d7',
                        'error': '#ba1a1a',
                        'surface-dim': '#d8dadc',
                        'error-container': '#ffdad6',
                        'inverse-surface': '#2d3133',
                        'on-primary-fixed-variant': '#003ea8',
                        'surface-bright': '#f7f9fb',
                        'surface-container': '#eceef0',
                        'on-tertiary-fixed': '#360f00',
                        'on-secondary-container': '#fffbff',
                        'inverse-on-surface': '#eff1f3',
                        'tertiary-fixed': '#ffdbcd',
                        'on-secondary-fixed': '#25005a',
                        'surface-variant': '#e0e3e5',
                        'on-tertiary-container': '#ffede6',
                        'on-primary': '#ffffff',
                        'on-primary-fixed': '#00174b',
                        'on-secondary-fixed-variant': '#5a00c6',
                        'on-tertiary-fixed-variant': '#7d2d00',
                        'surface': '#f7f9fb',
                        'on-surface': '#191c1e',
                    },

                    borderRadius: {
                        DEFAULT: '0.25rem',
                        lg: '0.5rem',
                        xl: '0.75rem',
                        full: '9999px',
                    },

                    spacing: {
                        'container-max': '1280px',
                        base: '4px',
                        '2xl': '64px',
                        sm: '8px',
                        md: '16px',
                        lg: '24px',
                        xl: '40px',
                        xs: '4px',
                        gutter: '24px',
                    },

                    fontFamily: {
                        'headline-lg': ['Plus Jakarta Sans'],
                        'label-md': ['Inter'],
                        'body-sm': ['Inter'],
                        'headline-md': ['Plus Jakarta Sans'],
                        'label-caps': ['Inter'],
                        'headline-lg-mobile': ['Plus Jakarta Sans'],
                        'body-md': ['Inter'],
                        'display-lg': ['Plus Jakarta Sans'],
                        'body-lg': ['Inter'],
                    },

                    fontSize: {
                        'headline-lg': [
                            '32px',
                            {
                                lineHeight: '1.25',
                                fontWeight: '600',
                            },
                        ],

                        'label-md': [
                            '14px',
                            {
                                lineHeight: '1',
                                letterSpacing: '0.01em',
                                fontWeight: '500',
                            },
                        ],

                        'body-sm': [
                            '14px',
                            {
                                lineHeight: '1.5',
                                fontWeight: '400',
                            },
                        ],

                        'headline-md': [
                            '24px',
                            {
                                lineHeight: '1.3',
                                fontWeight: '600',
                            },
                        ],

                        'label-caps': [
                            '12px',
                            {
                                lineHeight: '1',
                                letterSpacing: '0.05em',
                                fontWeight: '600',
                            },
                        ],

                        'headline-lg-mobile': [
                            '24px',
                            {
                                lineHeight: '1.3',
                                fontWeight: '600',
                            },
                        ],

                        'body-md': [
                            '16px',
                            {
                                lineHeight: '1.5',
                                fontWeight: '400',
                            },
                        ],

                        'display-lg': [
                            '48px',
                            {
                                lineHeight: '1.2',
                                letterSpacing: '-0.02em',
                                fontWeight: '700',
                            },
                        ],

                        'body-lg': [
                            '18px',
                            {
                                lineHeight: '1.6',
                                fontWeight: '400',
                            },
                        ],
                    },
                },
            },
        };
    </script>

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;

            background-color: #f7f9fb;
            color: #191c1e;

            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            -webkit-font-smoothing: antialiased;
        }

        .material-symbols-outlined {
            font-variation-settings:
                "FILL" 0,
                "wght" 400,
                "GRAD" 0,
                "opsz" 24;
        }

        .bg-blue-purple-gradient {
            background:
                linear-gradient(
                    135deg,
                    #2563eb 0%,
                    #712ae2 100%
                );
        }

        .text-gradient {
            background:
                linear-gradient(
                    135deg,
                    #004ac6 0%,
                    #712ae2 100%
                );

            background-clip: text;
            -webkit-background-clip: text;

            color: transparent;
            -webkit-text-fill-color: transparent;
        }

        .focus-glow:focus {
            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.14);
        }

        .login-decoration {
            position: fixed;

            border-radius: 9999px;

            filter: blur(90px);

            pointer-events: none;
        }

        .login-decoration-primary {
            top: -180px;
            right: -170px;

            width: 390px;
            height: 390px;

            background: rgba(0, 74, 198, 0.08);
        }

        .login-decoration-secondary {
            bottom: -190px;
            left: -180px;

            width: 420px;
            height: 420px;

            background: rgba(113, 42, 226, 0.08);
        }

        .login-card {
            transition:
                box-shadow 200ms ease,
                transform 200ms ease;
        }

        .login-card:hover {
            box-shadow:
                0 20px 50px rgba(15, 23, 42, 0.08);

            transform: translateY(-1px);
        }

        .form-input {
            transition:
                border-color 160ms ease,
                box-shadow 160ms ease,
                background-color 160ms ease;
        }

        .form-input.is-invalid {
            border-color: #ba1a1a;
        }

        .field-error {
            display: block;

            margin-top: 6px;
            margin-left: 4px;

            color: #ba1a1a;

            font-size: 13px;
            line-height: 1.5;
        }

        .status-message {
            margin-bottom: 16px;
            padding: 12px 14px;

            border: 1px solid rgba(22, 163, 74, 0.24);
            border-radius: 8px;

            background: rgba(22, 163, 74, 0.08);
            color: #166534;

            font-size: 14px;
            line-height: 1.5;
        }

        .password-toggle:focus-visible,
        .footer-link:focus-visible,
        .forgot-link:focus-visible {
            outline:
                3px solid rgba(37, 99, 235, 0.18);

            outline-offset: 3px;
        }

        .submit-button:disabled {
            cursor: wait;

            opacity: 0.75;
        }

        .button-spinner {
            display: none;

            width: 18px;
            height: 18px;

            border:
                2px solid rgba(255, 255, 255, 0.35);

            border-top-color: #ffffff;
            border-radius: 9999px;

            animation:
                spin 700ms linear infinite;
        }

        .submit-button.is-loading .button-spinner {
            display: inline-block;
        }

        .submit-button.is-loading .button-icon {
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body
    class="bg-background font-body-md text-on-surface flex flex-col min-h-screen relative overflow-x-hidden"
>
    <div
        class="login-decoration login-decoration-primary"
        aria-hidden="true"
    ></div>

    <div
        class="login-decoration login-decoration-secondary"
        aria-hidden="true"
    ></div>

    <main
        class="flex-grow flex items-center justify-center p-md z-10"
    >
        <section
            class="login-card w-full max-w-[400px] bg-surface-container-lowest border border-outline-variant/50 rounded-xl shadow-sm p-lg md:p-xl"
            aria-labelledby="login-heading"
        >
            <header class="flex flex-col items-center mb-xl">
                <div class="w-16 h-16 mb-md drop-shadow-sm">
                    <img
                        class="w-full h-full object-contain"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCil7lc_BBgbvrW63-AW8dxzZI70WgkxvLcy_SzN2_-Jnnv5gdjN8U3CjmV0SI23HdSt5huJpKxYHktWJ0tWZy5XMsQyfhy70NWy7Xz8_sYCf54KL5ypTp4AeGBTjZKJuZel7EDZR-HcVMmQy2bBFmrMg0a4UNE7lfS-AdRVCvrYmuySWjWfEn9HdVO_-WBYsExFMOLfHZP1eTDvEZ-XL9GAcgSjj4nv_rhFrcoQzghMUeRgLxYQ83pPgHWP4mlEkXGjRxfylGu"
                        alt="Logo SIMMAG"
                    >
                </div>

                <h1
                    class="font-headline-lg-mobile text-headline-lg-mobile text-gradient tracking-tight"
                >
                    SIMMAG
                </h1>
            </header>

            <div class="text-center mb-xl">
                <h2
                    id="login-heading"
                    class="font-headline-md text-headline-md text-on-surface mb-xs"
                >
                    Selamat Datang di SIMMAG
                </h2>

                <p
                    class="font-body-sm text-body-sm text-on-surface-variant px-4"
                >
                    Silakan masuk menggunakan kredensial Anda
                    untuk melanjutkan.
                </p>
            </div>

            @if (session('status'))
                <div
                    class="status-message"
                    role="status"
                >
                    {{ session('status') }}
                </div>
            @endif

            <form
                id="login-form"
                class="space-y-md"
                method="POST"
                action="{{ route('login') }}"
            >
                @csrf

                <div class="space-y-xs">
                    <label
                        class="font-label-md text-label-md text-on-surface-variant ml-1"
                        for="username"
                    >
                        Username (NIM/NIP/ID)
                    </label>

                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]"
                            aria-hidden="true"
                        >
                            person
                        </span>

                        <input
                            id="username"
                            class="form-input w-full pl-10 pr-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md focus:border-primary focus:ring-0 focus-glow outline-none @error('username') is-invalid @enderror"
                            name="username"
                            type="text"
                            value="{{ old('username') }}"
                            placeholder="Contoh: 2024101010"
                            autocomplete="username"
                            autofocus
                            required
                        >
                    </div>

                    @error('username')
                        <span
                            class="field-error"
                            role="alert"
                        >
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="space-y-xs">
                    <div
                        class="flex justify-between items-center px-1"
                    >
                        <label
                            class="font-label-md text-label-md text-on-surface-variant"
                            for="password"
                        >
                            Password
                        </label>

                        @if (Route::has('password.request'))
                            <a
                                class="forgot-link font-label-md text-label-md text-primary hover:underline transition-all"
                                href="{{ route('password.request') }}"
                            >
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]"
                            aria-hidden="true"
                        >
                            lock
                        </span>

                        <input
                            id="password"
                            class="form-input w-full pl-10 pr-10 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg font-body-md text-body-md focus:border-primary focus:ring-0 focus-glow outline-none @error('password') is-invalid @enderror"
                            name="password"
                            type="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            id="password-toggle"
                            class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors"
                            type="button"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            <span
                                id="password-icon"
                                class="material-symbols-outlined text-[20px]"
                                aria-hidden="true"
                            >
                                visibility
                            </span>
                        </button>
                    </div>

                    @error('password')
                        <span
                            class="field-error"
                            role="alert"
                        >
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="flex items-center gap-xs px-1">
                    <input
                        id="remember"
                        class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary-container"
                        name="remember"
                        type="checkbox"
                        value="1"
                        @checked(old('remember'))
                    >

                    <label
                        class="font-body-sm text-body-sm text-on-surface-variant cursor-pointer"
                        for="remember"
                    >
                        Ingat saya
                    </label>
                </div>

                <div class="pt-sm">
                    <button
                        id="login-button"
                        class="submit-button w-full py-3.5 bg-blue-purple-gradient text-on-primary font-label-md text-body-md rounded-lg shadow-sm hover:opacity-90 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-sm"
                        type="submit"
                    >
                        <span
                            class="button-spinner"
                            aria-hidden="true"
                        ></span>

                        <span id="login-button-text">
                            Masuk
                        </span>

                        <span
                            class="button-icon material-symbols-outlined text-[18px]"
                            aria-hidden="true"
                        >
                            login
                        </span>
                    </button>
                </div>
            </form>
        </section>
    </main>

    <footer
        class="w-full max-w-container-max mx-auto px-lg py-xl text-center z-10"
    >
        <div class="flex flex-col gap-xs">
            <p
                class="font-body-sm text-body-sm text-on-surface-variant"
            >
                © {{ now()->year }} SIMMAG -
                Sistem Monitoring Magang Mahasiswa
            </p>

            <p
                class="font-label-caps text-label-caps text-outline font-bold tracking-wider"
            >
                FAKULTAS ILMU KOMPUTER
            </p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm =
                document.getElementById('login-form');

            const loginButton =
                document.getElementById('login-button');

            const loginButtonText =
                document.getElementById('login-button-text');

            const passwordInput =
                document.getElementById('password');

            const passwordToggle =
                document.getElementById('password-toggle');

            const passwordIcon =
                document.getElementById('password-icon');

            passwordToggle.addEventListener(
                'click',
                function () {
                    const passwordIsHidden =
                        passwordInput.type === 'password';

                    passwordInput.type =
                        passwordIsHidden
                            ? 'text'
                            : 'password';

                    passwordIcon.textContent =
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

            loginForm.addEventListener(
                'submit',
                function () {
                    loginButton.disabled = true;

                    loginButton.classList.add(
                        'is-loading'
                    );

                    loginButtonText.textContent =
                        'Memproses...';
                }
            );
        });
    </script>
</body>
</html>