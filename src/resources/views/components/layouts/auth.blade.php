<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        {{ $title ?? 'Login - SIMMAG' }}
    </title>

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
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0,0"
        rel="stylesheet"
    >

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        simmag: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                        },
                    },

                    fontFamily: {
                        sans: [
                            'Inter',
                            'sans-serif',
                        ],

                        display: [
                            'Plus Jakarta Sans',
                            'sans-serif',
                        ],
                    },

                    boxShadow: {
                        login:
                            '0 24px 70px rgba(15, 23, 42, 0.10)',
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

            background:
                radial-gradient(
                    circle at 15% 10%,
                    rgba(37, 99, 235, 0.10),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 90% 85%,
                    rgba(124, 58, 237, 0.10),
                    transparent 32%
                ),
                #f8fafc;

            color:
                #172033;

            font-family:
                Inter,
                sans-serif;

            -webkit-font-smoothing:
                antialiased;
        }

        [wire\:cloak] {
            display:
                none !important;
        }

        .material-symbols-rounded {
            font-variation-settings:
                "FILL" 0,
                "wght" 400,
                "GRAD" 0,
                "opsz" 24;
        }

        .gradient-text {
            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #7c3aed
                );

            background-clip:
                text;

            -webkit-background-clip:
                text;

            color:
                transparent;

            -webkit-text-fill-color:
                transparent;
        }

        .login-decoration {
            position:
                fixed;

            border-radius:
                9999px;

            filter:
                blur(90px);

            pointer-events:
                none;
        }

        .login-decoration-primary {
            top:
                -160px;

            right:
                -160px;

            width:
                380px;

            height:
                380px;

            background:
                rgba(37, 99, 235, 0.10);
        }

        .login-decoration-secondary {
            bottom:
                -190px;

            left:
                -180px;

            width:
                420px;

            height:
                420px;

            background:
                rgba(124, 58, 237, 0.10);
        }

        .form-control {
            transition:
                border-color 160ms ease,
                box-shadow 160ms ease,
                background-color 160ms ease;
        }

        .form-control:focus-within {
            border-color:
                #2563eb;

            box-shadow:
                0 0 0 4px rgba(37, 99, 235, 0.10);

            background:
                #ffffff;
        }

        .login-button {
            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #7c3aed
                );

            transition:
                transform 180ms ease,
                opacity 180ms ease,
                box-shadow 180ms ease;
        }

        .login-button:hover {
            transform:
                translateY(-1px);

            box-shadow:
                0 14px 30px rgba(37, 99, 235, 0.22);
        }

        .login-button:active {
            transform:
                translateY(0);
        }

        .login-button:disabled {
            cursor:
                wait;

            opacity:
                0.72;
        }

        @keyframes spinner {
            to {
                transform:
                    rotate(360deg);
            }
        }

        .loading-spinner {
            animation:
                spinner 700ms linear infinite;
        }
    </style>

    @livewireStyles
</head>

<body>
    <div
        class="login-decoration login-decoration-primary"
        aria-hidden="true"
    ></div>

    <div
        class="login-decoration login-decoration-secondary"
        aria-hidden="true"
    ></div>

    {{ $slot }}

    @livewireScripts
</body>
</html>