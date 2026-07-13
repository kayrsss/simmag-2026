<!DOCTYPE html>
<html lang="id">
<head>
    @php
        $logoPath = public_path('images/logo-simmag.png');

        $logoVersion = file_exists($logoPath)
            ? filemtime($logoPath)
            : now()->timestamp;

        $authenticatedUser = auth()->user();

        $rawRole = $authenticatedUser
            && method_exists($authenticatedUser, 'getRoleNames')
                ? $authenticatedUser->getRoleNames()->first()
                : $authenticatedUser?->role;

        $normalizedRole = str(
            (string) ($rawRole ?: 'mahasiswa')
        )
            ->lower()
            ->replace([
                ' ',
                '-',
            ], '_')
            ->toString();

        $normalizedRole = match ($normalizedRole) {
            'administrator',
            'admin_fakultas',
            'super_admin' => 'admin',

            'dosen',
            'dospem',
            'lecturer' => 'dosen_pembimbing',

            'mentor_lapangan',
            'field_supervisor',
            'pl' => 'pembimbing_lapangan',

            default => $normalizedRole,
        };

        $roleLabel = match ($normalizedRole) {
            'admin' => 'Administrator',

            'dosen_pembimbing' =>
                'Dosen Pembimbing',

            'pembimbing_lapangan' =>
                'Pembimbing Lapangan',

            default => 'Mahasiswa',
        };

        $dashboardRoute = match ($normalizedRole) {
            'admin' =>
                'dashboard.admin',

            'dosen_pembimbing' =>
                'dashboard.dosen',

            'pembimbing_lapangan' =>
                'dashboard.pembimbing-lapangan',

            default =>
                'dashboard.mahasiswa',
        };

        $userName = $authenticatedUser?->name
            ?: 'Pengguna SIMMAG';

        $userInitials = collect(
            explode(' ', trim($userName))
        )
            ->filter()
            ->take(2)
            ->map(
                fn (string $word): string =>
                    mb_strtoupper(
                        mb_substr($word, 0, 1)
                    )
            )
            ->implode('');

        $userInitials = $userInitials !== ''
            ? $userInitials
            : 'SM';

        /*
        |--------------------------------------------------------------------------
        | Navigation Utama
        |--------------------------------------------------------------------------
        */

        $navigationSections = [
            [
                'heading' => 'Menu Utama',

                'items' => [
                    [
                        'label' => 'Beranda',
                        'icon' => 'home',
                        'route' => $dashboardRoute,
                        'active' => [
                            $dashboardRoute,
                        ],
                    ],
                ],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Navigation Admin
        |--------------------------------------------------------------------------
        */

        if ($normalizedRole === 'admin') {
            $navigationSections[] = [
                'heading' => 'Administrasi',

                'items' => [
                    [
                        'label' => 'Panel Admin',
                        'icon' => 'admin_panel_settings',
                        'url' => url('/admin'),
                        'active_url' => 'admin',
                    ],

                    [
                        'label' => 'Data Pengguna',
                        'icon' => 'manage_accounts',
                        'route' =>
                            'filament.admin.resources.users.index',
                        'active' => [
                            'filament.admin.resources.users.*',
                        ],
                    ],

                    [
                        'label' => 'Periode Magang',
                        'icon' => 'date_range',
                        'route' =>
                            'filament.admin.resources.periods.index',
                        'active' => [
                            'filament.admin.resources.periods.*',
                        ],
                    ],

                    [
                        'label' => 'Data Magang',
                        'icon' => 'work',
                        'route' =>
                            'filament.admin.resources.internships.index',
                        'active' => [
                            'filament.admin.resources.internships.*',
                        ],
                    ],

                    [
                        'label' => 'Data Instansi',
                        'icon' => 'apartment',
                        'route' =>
                            'filament.admin.resources.company-profiles.index',
                        'active' => [
                            'filament.admin.resources.company-profiles.*',
                        ],
                    ],

                    [
                        'label' => 'Program Studi',
                        'icon' => 'school',
                        'route' =>
                            'filament.admin.resources.program-studies.index',
                        'active' => [
                            'filament.admin.resources.program-studies.*',
                        ],
                    ],
                ],
            ];

            $navigationSections[] = [
                'heading' => 'Monitoring Sistem',

                'items' => [
                    [
                        'label' => 'Pengumuman',
                        'icon' => 'campaign',
                        'route' =>
                            'announcements.index',
                        'active' => [
                            'announcements.index',
                        ],
                    ],

                    [
                        'label' => 'Arsip Digital',
                        'icon' => 'inventory_2',
                        'route' =>
                            'filament.admin.resources.digital-archives.index',
                        'active' => [
                            'filament.admin.resources.digital-archives.*',
                        ],
                    ],

                    [
                        'label' => 'Audit Trail',
                        'icon' => 'history',
                        'route' =>
                            'filament.admin.resources.audit-trails.index',
                        'active' => [
                            'filament.admin.resources.audit-trails.*',
                        ],
                    ],

                    [
                        'label' => 'Log Aktivitas',
                        'icon' => 'receipt_long',
                        'route' =>
                            'filament.admin.resources.activity-logs.index',
                        'active' => [
                            'filament.admin.resources.activity-logs.*',
                        ],
                    ],

                    [
                        'label' => 'Sinkronisasi SIAKAD',
                        'icon' => 'sync',
                        'route' =>
                            'filament.admin.resources.siakad-sync-logs.index',
                        'active' => [
                            'filament.admin.resources.siakad-sync-logs.*',
                        ],
                    ],
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Navigation Mahasiswa
        |--------------------------------------------------------------------------
        */

        if ($normalizedRole === 'mahasiswa') {
            $navigationSections[] = [
                'heading' => 'Aktivitas Magang',

                'items' => [
                    [
                        'label' => 'Kerangka Acuan',
                        'icon' => 'description',
                        'route' =>
                            'student.frameworks.index',
                        'active' => [
                            'student.frameworks.*',
                        ],
                    ],

                    [
                        'label' => 'Logbook',
                        'icon' => 'edit_note',
                        'route' =>
                            'student.logbooks.index',
                        'active' => [
                            'student.logbooks.*',
                        ],
                    ],

                    [
                        'label' => 'Bimbingan',
                        'icon' => 'forum',
                        'route' =>
                            'student.consultations.index',
                        'active' => [
                            'student.consultations.*',
                        ],
                    ],

                    [
                        'label' => 'Laporan Akhir',
                        'icon' => 'draft',
                        'route' =>
                            'student.final-reports.index',
                        'active' => [
                            'student.final-reports.*',
                        ],
                    ],

                    [
                        'label' => 'Hasil Penilaian',
                        'icon' => 'workspace_premium',
                        'route' =>
                            'student.assessments.index',
                        'active' => [
                            'student.assessments.*',
                        ],
                    ],

                    [
                        'label' => 'Pengumuman',
                        'icon' => 'campaign',
                        'route' =>
                            'announcements.index',
                        'active' => [
                            'announcements.index',
                        ],
                    ],
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Navigation Dosen Pembimbing
        |--------------------------------------------------------------------------
        */

        if (
            $normalizedRole ===
            'dosen_pembimbing'
        ) {
            $navigationSections[] = [
                'heading' => 'Mahasiswa Bimbingan',

                'items' => [
                    [
                        'label' => 'Daftar Mahasiswa',
                        'icon' => 'groups',
                        'route' =>
                            'lecturer.students.index',
                        'active' => [
                            'lecturer.students.*',
                        ],
                    ],

                    [
                        'label' => 'Kerangka Acuan',
                        'icon' => 'description',
                        'route' =>
                            'lecturer.frameworks.index',
                        'active' => [
                            'lecturer.frameworks.*',
                        ],
                    ],

                    [
                        'label' => 'Monitoring Logbook',
                        'icon' => 'edit_note',
                        'route' =>
                            'lecturer.logbooks.index',
                        'active' => [
                            'lecturer.logbooks.*',
                        ],
                    ],

                    [
                        'label' => 'Bimbingan',
                        'icon' => 'forum',
                        'route' =>
                            'lecturer.consultations.index',
                        'active' => [
                            'lecturer.consultations.*',
                        ],
                    ],

                    [
                        'label' => 'Laporan Akhir',
                        'icon' => 'draft',
                        'route' =>
                            'lecturer.final-reports.index',
                        'active' => [
                            'lecturer.final-reports.*',
                        ],
                    ],

                    [
                        'label' => 'Penilaian Akademik',
                        'icon' => 'grading',
                        'route' =>
                            'lecturer.assessments.index',
                        'active' => [
                            'lecturer.assessments.*',
                        ],
                    ],

                    [
                        'label' => 'Pengumuman',
                        'icon' => 'campaign',
                        'route' =>
                            'announcements.index',
                        'active' => [
                            'announcements.index',
                        ],
                    ],
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Navigation Pembimbing Lapangan
        |--------------------------------------------------------------------------
        */

        if (
            $normalizedRole ===
            'pembimbing_lapangan'
        ) {
            $navigationSections[] = [
                'heading' => 'Mahasiswa Magang',

                'items' => [
                    [
                        'label' => 'Daftar Mahasiswa',
                        'icon' => 'groups',
                        'route' =>
                            'field-supervisor.students.index',
                        'active' => [
                            'field-supervisor.students.*',
                        ],
                    ],

                    [
                        'label' => 'Review Kerangka Acuan',
                        'icon' => 'description',
                        'route' =>
                            'field-supervisor.frameworks.index',
                        'active' => [
                            'field-supervisor.frameworks.*',
                        ],
                    ],

                    [
                        'label' => 'Validasi Logbook',
                        'icon' => 'fact_check',
                        'route' =>
                            'field-supervisor.logbooks.index',
                        'active' => [
                            'field-supervisor.logbooks.*',
                        ],
                    ],

                    [
                        'label' => 'Penilaian Lapangan',
                        'icon' => 'grading',
                        'route' =>
                            'field-supervisor.assessments.index',
                        'active' => [
                            'field-supervisor.assessments.*',
                        ],
                    ],

                    [
                        'label' => 'Pengumuman',
                        'icon' => 'campaign',
                        'route' =>
                            'announcements.index',
                        'active' => [
                            'announcements.index',
                        ],
                    ],
                ],
            ];
        }
    @endphp

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
        {{ $title ?? 'SIMMAG' }}
    </title>

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo-simmag.png') }}?v={{ $logoVersion }}"
    >

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
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0,0"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    <style>
        :root {
            --simmag-primary: #4f46e5;
            --simmag-primary-dark: #4338ca;
            --simmag-primary-soft: #eef2ff;
            --simmag-blue: #2563eb;
            --simmag-violet: #7c3aed;
            --simmag-success: #16a34a;
            --simmag-warning: #d97706;
            --simmag-danger: #dc2626;
            --simmag-text: #111827;
            --simmag-muted: #64748b;
            --simmag-border: #e5e7eb;
            --simmag-background: #f8fafc;
            --simmag-card: #ffffff;
            --simmag-sidebar-width: 318px;
            --simmag-topbar-height: 76px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(99, 102, 241, 0.035),
                    transparent 34%
                ),
                var(--simmag-background);
            color: var(--simmag-text);
            font-family: Inter, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        button,
        input,
        textarea,
        select {
            font: inherit;
        }

        button,
        a {
            -webkit-tap-highlight-color: transparent;
        }

        button:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        [x-cloak],
        [wire\:cloak],
        [hidden] {
            display: none !important;
        }

        .material-symbols-rounded {
            font-size: 21px;
            line-height: 1;
            font-variation-settings:
                "FILL" 0,
                "wght" 400,
                "GRAD" 0,
                "opsz" 24;
        }

        .simmag-shell {
            min-height: 100vh;
        }

        .simmag-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 60;
            display: flex;
            width: var(--simmag-sidebar-width);
            max-width: calc(100vw - 44px);
            flex-direction: column;
            border-right: 1px solid var(--simmag-border);
            background: rgba(255, 255, 255, 0.99);
            transform: translateX(-100%);
            transition: transform 220ms ease;
        }

        .simmag-sidebar.is-open {
            transform: translateX(0);
        }

        .simmag-sidebar-overlay {
            position: fixed;
            inset: 0;
            z-index: 55;
            display: none;
            background: rgba(15, 23, 42, 0.44);
            backdrop-filter: blur(3px);
        }

        .simmag-sidebar-overlay.is-visible {
            display: block;
        }

        .simmag-brand {
            display: flex;
            min-height: 116px;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 22px;
            border-bottom: 1px solid #f1f5f9;
        }

        .simmag-brand-link {
            display: flex;
            min-width: 0;
            flex: 1;
            align-items: center;
            gap: 14px;
        }

        .simmag-brand-logo {
            display: flex;
            width: 62px;
            height: 62px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 16px;
            background: #ffffff;
        }

        .simmag-brand-logo-image {
            display: block;
            width: 128%;
            height: 128%;
            max-width: none;
            flex-shrink: 0;
            object-fit: contain;
            object-position: center;
        }

        .simmag-brand-text {
            min-width: 0;
        }

        .simmag-brand-name {
            margin: 0;
            color: #111827;
            font-family:
                "Plus Jakarta Sans",
                sans-serif;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }

        .simmag-brand-description {
            margin: 5px 0 0;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.5;
        }

        .simmag-icon-button {
            display: inline-flex;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--simmag-border);
            border-radius: 12px;
            background: #ffffff;
            color: #64748b;
            cursor: pointer;
            transition:
                border-color 160ms ease,
                background 160ms ease,
                color 160ms ease,
                transform 160ms ease;
        }

        .simmag-icon-button:hover {
            border-color: #c7d2fe;
            background: #eef2ff;
            color: var(--simmag-primary);
            transform: translateY(-1px);
        }

        .simmag-sidebar-close {
            display: inline-flex;
        }

        .simmag-sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 22px 18px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .simmag-nav-section {
            margin-bottom: 25px;
        }

        .simmag-nav-section:last-child {
            margin-bottom: 0;
        }

        .simmag-nav-heading {
            margin: 0 0 10px;
            padding: 0 14px;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .simmag-nav-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .simmag-nav-item {
            display: flex;
            min-height: 50px;
            align-items: center;
            gap: 13px;
            padding: 11px 14px;
            border-radius: 13px;
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            transition:
                background 160ms ease,
                color 160ms ease,
                transform 160ms ease;
        }

        .simmag-nav-item:hover {
            background: #f8fafc;
            color: #334155;
            transform: translateX(2px);
        }

        .simmag-nav-item.is-active {
            background: linear-gradient(
                135deg,
                #eef2ff,
                #f5f3ff
            );
            color: var(--simmag-primary);
        }

        .simmag-nav-item.is-active
        .material-symbols-rounded {
            color: var(--simmag-primary);
            font-variation-settings:
                "FILL" 1,
                "wght" 500,
                "GRAD" 0,
                "opsz" 24;
        }

        .simmag-sidebar-footer {
            border-top: 1px solid #f1f5f9;
            padding: 14px;
        }

        .simmag-user-card {
            position: relative;
        }

        .simmag-user-trigger {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 11px;
            padding: 10px;
            border: 0;
            border-radius: 14px;
            background: transparent;
            color: inherit;
            text-align: left;
            cursor: pointer;
            transition: background 160ms ease;
        }

        .simmag-user-trigger:hover {
            background: #f8fafc;
        }

        .simmag-avatar {
            display: flex;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(
                135deg,
                #2563eb,
                #7c3aed
            );
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
        }

        .simmag-user-information {
            min-width: 0;
            flex: 1;
        }

        .simmag-user-name {
            display: block;
            overflow: hidden;
            margin: 0;
            color: #1e293b;
            font-size: 12px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .simmag-user-role {
            display: block;
            overflow: hidden;
            margin: 3px 0 0;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 500;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .simmag-user-chevron {
            transition: transform 180ms ease;
        }

        .simmag-user-trigger.is-open
        .simmag-user-chevron {
            transform: rotate(180deg);
        }

        .simmag-sidebar-dropdown {
            position: absolute;
            right: 0;
            bottom: calc(100% + 8px);
            left: 0;
            display: none;
            overflow: hidden;
            border: 1px solid var(--simmag-border);
            border-radius: 14px;
            background: #ffffff;
            box-shadow:
                0 18px 50px rgba(15, 23, 42, 0.14);
        }

        .simmag-sidebar-dropdown.is-open {
            display: block;
        }

        .simmag-dropdown-item {
            display: flex;
            width: 100%;
            min-height: 44px;
            align-items: center;
            gap: 10px;
            padding: 11px 13px;
            border: 0;
            background: #ffffff;
            color: #475569;
            font-size: 11px;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
        }

        .simmag-dropdown-item:hover {
            background: #f8fafc;
        }

        .simmag-dropdown-item.is-danger {
            color: #dc2626;
        }

        .simmag-main {
            min-height: 100vh;
        }

        .simmag-topbar {
            position: sticky;
            top: 0;
            z-index: 40;
            display: flex;
            min-height: var(--simmag-topbar-height);
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid var(--simmag-border);
            background: rgba(255, 255, 255, 0.94);
            padding: 0 18px;
            backdrop-filter: blur(16px);
        }

        .simmag-topbar-left,
        .simmag-topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .simmag-topbar-title {
            display: none;
            margin: 0;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .simmag-date-card {
            display: none;
            min-height: 44px;
            align-items: center;
            gap: 10px;
            padding: 7px 13px;
            border: 1px solid var(--simmag-border);
            border-radius: 12px;
            background: #ffffff;
        }

        .simmag-date-card strong {
            display: block;
            color: #334155;
            font-size: 10px;
        }

        .simmag-date-card span {
            display: block;
            margin-top: 2px;
            color: #94a3b8;
            font-size: 8px;
        }

        .simmag-notification {
            position: relative;
        }

        .simmag-notification-dot {
            position: absolute;
            top: 9px;
            right: 9px;
            width: 7px;
            height: 7px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            background: #ef4444;
        }

        .simmag-content {
            width: 100%;
            max-width: 1480px;
            margin: 0 auto;
            padding: 28px 18px 50px;
        }

        .simmag-flash {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            margin-bottom: 18px;
            padding: 13px 15px;
            border: 1px solid;
            border-radius: 13px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.65;
        }

        .simmag-flash.is-success {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #15803d;
        }

        .simmag-flash.is-error {
            border-color: #fecaca;
            background: #fef2f2;
            color: #dc2626;
        }

        .simmag-card {
            overflow: hidden;
            border: 1px solid var(--simmag-border);
            border-radius: 20px;
            background: var(--simmag-card);
            box-shadow:
                0 10px 35px rgba(15, 23, 42, 0.035);
        }

        .simmag-card-header {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .simmag-card-title {
            margin: 0;
            color: #0f172a;
            font-family:
                "Plus Jakarta Sans",
                sans-serif;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .simmag-card-description {
            margin: 5px 0 0;
            color: #94a3b8;
            font-size: 10px;
            line-height: 1.7;
        }

        .simmag-primary-button,
        .simmag-secondary-button {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 12px;
            padding: 10px 15px;
            font-size: 10px;
            font-weight: 700;
            cursor: pointer;
            transition:
                transform 160ms ease,
                border-color 160ms ease,
                background 160ms ease,
                color 160ms ease,
                box-shadow 160ms ease;
        }

        .simmag-primary-button {
            border: 0;
            background: linear-gradient(
                135deg,
                #2563eb,
                #7c3aed
            );
            color: #ffffff;
            box-shadow:
                0 10px 25px rgba(79, 70, 229, 0.18);
        }

        .simmag-primary-button:hover {
            transform: translateY(-1px);
            box-shadow:
                0 14px 30px rgba(79, 70, 229, 0.25);
        }

        .simmag-secondary-button {
            border: 1px solid var(--simmag-border);
            background: #ffffff;
            color: #475569;
        }

        .simmag-secondary-button:hover {
            border-color: #c7d2fe;
            background: #eef2ff;
            color: var(--simmag-primary);
            transform: translateY(-1px);
        }

        .simmag-status {
            display: inline-flex;
            min-height: 27px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: 0.015em;
        }

        .simmag-status.is-primary {
            background: #eef2ff;
            color: #4f46e5;
        }

        .simmag-status.is-success {
            background: #ecfdf5;
            color: #15803d;
        }

        .simmag-status.is-warning {
            background: #fff7ed;
            color: #c2410c;
        }

        .simmag-status.is-danger {
            background: #fef2f2;
            color: #dc2626;
        }

        .simmag-status.is-neutral {
            background: #f1f5f9;
            color: #64748b;
        }

        .simmag-empty-state {
            display: flex;
            min-height: 260px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
            text-align: center;
        }

        .simmag-empty-icon {
            display: flex;
            width: 62px;
            height: 62px;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: #eef2ff;
            color: #4f46e5;
        }

        .simmag-empty-icon
        .material-symbols-rounded {
            font-size: 29px;
        }

        .simmag-empty-title {
            margin: 16px 0 0;
            color: #1e293b;
            font-size: 13px;
            font-weight: 800;
        }

        .simmag-empty-description {
            max-width: 420px;
            margin: 7px 0 0;
            color: #94a3b8;
            font-size: 10px;
            line-height: 1.75;
        }

        @media (min-width: 640px) {
            .simmag-topbar {
                padding-right: 24px;
                padding-left: 24px;
            }

            .simmag-topbar-title {
                display: block;
            }

            .simmag-date-card {
                display: flex;
            }

            .simmag-content {
                padding: 32px 26px 54px;
            }

            .simmag-card-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        @media (min-width: 1100px) {
            .simmag-sidebar {
                transform: translateX(0);
            }

            .simmag-sidebar-overlay {
                display: none !important;
            }

            .simmag-sidebar-close,
            .simmag-mobile-menu {
                display: none;
            }

            .simmag-main {
                margin-left:
                    var(--simmag-sidebar-width);
            }

            .simmag-content {
                padding: 34px 38px 60px;
            }
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>

<body>
    <div class="simmag-shell">
        <div
            id="simmag-sidebar-overlay"
            class="simmag-sidebar-overlay"
        ></div>

        <aside
            id="simmag-sidebar"
            class="simmag-sidebar"
        >
            <div class="simmag-brand">
                <a
                    href="{{ \Illuminate\Support\Facades\Route::has($dashboardRoute)
                        ? route($dashboardRoute)
                        : url('/dashboard') }}"
                    class="simmag-brand-link"
                    aria-label="Beranda SIMMAG"
                >
                    <div class="simmag-brand-logo">
                        <img
                            src="{{ asset('images/logo-simmag.png') }}?v={{ $logoVersion }}"
                            alt="Logo SIMMAG"
                            class="simmag-brand-logo-image"
                        >
                    </div>

                    <div class="simmag-brand-text">
                        <p class="simmag-brand-name">
                            SIMMAG
                        </p>

                        <p class="simmag-brand-description">
                            Sistem Monitoring Magang
                        </p>
                    </div>
                </a>

                <button
                    id="simmag-sidebar-close"
                    type="button"
                    class="simmag-icon-button simmag-sidebar-close"
                    aria-label="Tutup menu"
                >
                    <span class="material-symbols-rounded">
                        close
                    </span>
                </button>
            </div>

            <nav class="simmag-sidebar-content">
                @foreach (
                    $navigationSections
                    as $navigationSection
                )
                    @php
                        $visibleItems = collect(
                            $navigationSection['items']
                        )->filter(
                            function (array $item): bool {
                                if (isset($item['url'])) {
                                    return true;
                                }

                                return isset($item['route'])
                                    && \Illuminate\Support\Facades\Route::has(
                                        $item['route']
                                    );
                            }
                        );
                    @endphp

                    @if ($visibleItems->isNotEmpty())
                        <section class="simmag-nav-section">
                            <p class="simmag-nav-heading">
                                {{ $navigationSection['heading'] }}
                            </p>

                            <div class="simmag-nav-list">
                                @foreach (
                                    $visibleItems
                                    as $navigationItem
                                )
                                    @php
                                        $navigationUrl =
                                            $navigationItem['url']
                                            ?? route(
                                                $navigationItem['route']
                                            );

                                        $navigationActive =
                                            isset(
                                                $navigationItem['active_url']
                                            )
                                                ? request()->is(
                                                    $navigationItem['active_url']
                                                )
                                                : collect(
                                                    $navigationItem['active']
                                                    ?? []
                                                )->contains(
                                                    fn (
                                                        string $pattern
                                                    ): bool =>
                                                        request()->routeIs(
                                                            $pattern
                                                        )
                                                );
                                    @endphp

                                    <a
                                        href="{{ $navigationUrl }}"
                                        @class([
                                            'simmag-nav-item',
                                            'is-active' =>
                                                $navigationActive,
                                        ])
                                    >
                                        <span class="material-symbols-rounded">
                                            {{ $navigationItem['icon'] }}
                                        </span>

                                        <span>
                                            {{ $navigationItem['label'] }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach
            </nav>

            <div class="simmag-sidebar-footer">
                <div class="simmag-user-card">
                    <div
                        id="simmag-user-dropdown"
                        class="simmag-sidebar-dropdown"
                    >
                        @if ($normalizedRole === 'admin')
                            <a
                                href="{{ url('/admin') }}"
                                class="simmag-dropdown-item"
                            >
                                <span class="material-symbols-rounded">
                                    admin_panel_settings
                                </span>

                                Panel Admin
                            </a>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="simmag-dropdown-item is-danger"
                            >
                                <span class="material-symbols-rounded">
                                    logout
                                </span>

                                Keluar
                            </button>
                        </form>
                    </div>

                    <button
                        id="simmag-user-trigger"
                        type="button"
                        class="simmag-user-trigger"
                        aria-expanded="false"
                    >
                        <span class="simmag-avatar">
                            {{ $userInitials }}
                        </span>

                        <span class="simmag-user-information">
                            <span class="simmag-user-name">
                                {{ $userName }}
                            </span>

                            <span class="simmag-user-role">
                                {{ $roleLabel }}
                            </span>
                        </span>

                        <span
                            class="material-symbols-rounded simmag-user-chevron"
                        >
                            unfold_more
                        </span>
                    </button>
                </div>
            </div>
        </aside>

        <div class="simmag-main">
            <header class="simmag-topbar">
                <div class="simmag-topbar-left">
                    <button
                        id="simmag-mobile-menu"
                        type="button"
                        class="simmag-icon-button simmag-mobile-menu"
                        aria-label="Buka menu"
                    >
                        <span class="material-symbols-rounded">
                            menu
                        </span>
                    </button>

                    <p class="simmag-topbar-title">
                        {{ $title ?? 'Dashboard SIMMAG' }}
                    </p>
                </div>

                <div class="simmag-topbar-right">
                    @if (
                        \Illuminate\Support\Facades\Route::has(
                            'announcements.index'
                        )
                    )
                        <a
                            href="{{ route('announcements.index') }}"
                            class="simmag-icon-button simmag-notification"
                            aria-label="Buka pengumuman"
                        >
                            <span class="material-symbols-rounded">
                                notifications
                            </span>

                            <span
                                class="simmag-notification-dot"
                            ></span>
                        </a>
                    @endif

                    <div class="simmag-date-card">
                        <span class="material-symbols-rounded">
                            calendar_today
                        </span>

                        <div>
                            <strong>
                                {{ now()->translatedFormat('d F Y') }}
                            </strong>

                            <span>
                                {{ now()->translatedFormat('l, H.i') }}
                                WIB
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="simmag-content">
                @if (session('success'))
                    <div class="simmag-flash is-success">
                        <span class="material-symbols-rounded">
                            check_circle
                        </span>

                        <span>
                            {{ session('success') }}
                        </span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="simmag-flash is-error">
                        <span class="material-symbols-rounded">
                            error
                        </span>

                        <span>
                            {{ session('error') }}
                        </span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
        function initializeSimmagLayout() {
            const sidebar =
                document.getElementById(
                    'simmag-sidebar'
                );

            const overlay =
                document.getElementById(
                    'simmag-sidebar-overlay'
                );

            const openButton =
                document.getElementById(
                    'simmag-mobile-menu'
                );

            const closeButton =
                document.getElementById(
                    'simmag-sidebar-close'
                );

            const userTrigger =
                document.getElementById(
                    'simmag-user-trigger'
                );

            const userDropdown =
                document.getElementById(
                    'simmag-user-dropdown'
                );

            const openSidebar = function () {
                sidebar?.classList.add(
                    'is-open'
                );

                overlay?.classList.add(
                    'is-visible'
                );

                document.body.style.overflow =
                    'hidden';
            };

            const closeSidebar = function () {
                if (window.innerWidth >= 1100) {
                    return;
                }

                sidebar?.classList.remove(
                    'is-open'
                );

                overlay?.classList.remove(
                    'is-visible'
                );

                document.body.style.overflow =
                    '';
            };

            if (
                openButton
                && openButton.dataset.initialized
                    !== 'true'
            ) {
                openButton.dataset.initialized =
                    'true';

                openButton.addEventListener(
                    'click',
                    openSidebar
                );
            }

            if (
                closeButton
                && closeButton.dataset.initialized
                    !== 'true'
            ) {
                closeButton.dataset.initialized =
                    'true';

                closeButton.addEventListener(
                    'click',
                    closeSidebar
                );
            }

            if (
                overlay
                && overlay.dataset.initialized
                    !== 'true'
            ) {
                overlay.dataset.initialized =
                    'true';

                overlay.addEventListener(
                    'click',
                    closeSidebar
                );
            }

            if (
                userTrigger
                && userDropdown
                && userTrigger.dataset.initialized
                    !== 'true'
            ) {
                userTrigger.dataset.initialized =
                    'true';

                userTrigger.addEventListener(
                    'click',
                    function (event) {
                        event.stopPropagation();

                        const dropdownIsOpen =
                            userDropdown.classList.toggle(
                                'is-open'
                            );

                        userTrigger.classList.toggle(
                            'is-open',
                            dropdownIsOpen
                        );

                        userTrigger.setAttribute(
                            'aria-expanded',
                            dropdownIsOpen
                                ? 'true'
                                : 'false'
                        );
                    }
                );

                document.addEventListener(
                    'click',
                    function (event) {
                        if (
                            userDropdown.contains(
                                event.target
                            )
                            || userTrigger.contains(
                                event.target
                            )
                        ) {
                            return;
                        }

                        userDropdown.classList.remove(
                            'is-open'
                        );

                        userTrigger.classList.remove(
                            'is-open'
                        );

                        userTrigger.setAttribute(
                            'aria-expanded',
                            'false'
                        );
                    }
                );
            }

            window.addEventListener(
                'resize',
                function () {
                    if (window.innerWidth >= 1100) {
                        overlay?.classList.remove(
                            'is-visible'
                        );

                        document.body.style.overflow =
                            '';
                    }
                }
            );
        }

        document.addEventListener(
            'DOMContentLoaded',
            initializeSimmagLayout
        );

        document.addEventListener(
            'livewire:navigated',
            initializeSimmagLayout
        );
    </script>
</body>
</html>