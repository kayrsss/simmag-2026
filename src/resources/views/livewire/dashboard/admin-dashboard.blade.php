@php
    $toneStyle = [
        'blue' => [
            'background' => '#eff6ff',
            'color' => '#2563eb',
        ],

        'violet' => [
            'background' => '#f5f3ff',
            'color' => '#7c3aed',
        ],

        'cyan' => [
            'background' => '#ecfeff',
            'color' => '#0891b2',
        ],

        'green' => [
            'background' => '#ecfdf5',
            'color' => '#16a34a',
        ],

        'orange' => [
            'background' => '#fff7ed',
            'color' => '#ea580c',
        ],

        'amber' => [
            'background' => '#fffbeb',
            'color' => '#d97706',
        ],

        'red' => [
            'background' => '#fef2f2',
            'color' => '#dc2626',
        ],
    ];
@endphp

<style>
    .admin-dashboard {
        display: grid;
        gap: 22px;
    }

    .admin-heading {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .admin-heading-label {
        margin: 0;
        color: #2563eb;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .admin-heading-title {
        margin: 7px 0 0;
        color: #0f172a;
        font-family:
            "Plus Jakarta Sans",
            sans-serif;
        font-size: clamp(26px, 4vw, 36px);
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .admin-heading-description {
        max-width: 760px;
        margin: 10px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.75;
    }

    .admin-heading-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .admin-banner {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background:
            linear-gradient(
                135deg,
                #0f172a,
                #172554 52%,
                #4c1d95
            );
        padding: 28px 24px;
        color: #ffffff;
        box-shadow:
            0 22px 55px
            rgba(30, 41, 59, 0.2);
    }

    .admin-banner::before {
        position: absolute;
        top: -110px;
        right: -80px;
        width: 290px;
        height: 290px;
        border: 58px solid
            rgba(255, 255, 255, 0.045);
        border-radius: 50%;
        content: "";
    }

    .admin-banner-content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .admin-system-status {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 8px;
        padding: 7px 11px;
        border: 1px solid
            rgba(255, 255, 255, 0.15);
        border-radius: 999px;
        background:
            rgba(255, 255, 255, 0.09);
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .admin-system-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #4ade80;
        box-shadow:
            0 0 0 4px
            rgba(74, 222, 128, 0.15);
    }

    .admin-banner-title {
        margin: 17px 0 0;
        font-family:
            "Plus Jakarta Sans",
            sans-serif;
        font-size: clamp(22px, 4vw, 31px);
        font-weight: 800;
        letter-spacing: -0.035em;
    }

    .admin-banner-description {
        max-width: 700px;
        margin: 10px 0 0;
        color: #dbeafe;
        font-size: 11px;
        line-height: 1.75;
    }

    .admin-banner-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin-top: 18px;
    }

    .admin-banner-meta-item {
        display: inline-flex;
        min-height: 37px;
        align-items: center;
        gap: 8px;
        padding: 8px 11px;
        border-radius: 11px;
        background:
            rgba(255, 255, 255, 0.09);
        color: #ffffff;
        font-size: 9px;
        font-weight: 700;
    }

    .admin-banner-icon {
        display: none;
        width: 116px;
        height: 116px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border: 1px solid
            rgba(255, 255, 255, 0.14);
        border-radius: 30px;
        background:
            rgba(255, 255, 255, 0.08);
        transform: rotate(5deg);
    }

    .admin-banner-icon
    .material-symbols-rounded {
        font-size: 58px;
        transform: rotate(-5deg);
    }

    .admin-statistics {
        display: grid;
        grid-template-columns: 1fr;
        gap: 13px;
    }

    .admin-statistic-card {
        display: flex;
        min-height: 124px;
        align-items: center;
        gap: 15px;
        padding: 17px;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        box-shadow:
            0 8px 28px
            rgba(15, 23, 42, 0.035);
        transition:
            border-color 160ms ease,
            transform 160ms ease,
            box-shadow 160ms ease;
    }

    a.admin-statistic-card:hover {
        border-color: #c7d2fe;
        transform: translateY(-2px);
        box-shadow:
            0 14px 34px
            rgba(15, 23, 42, 0.07);
    }

    .admin-statistic-icon {
        display: flex;
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
    }

    .admin-statistic-label {
        margin: 0;
        color: #64748b;
        font-size: 10px;
        font-weight: 700;
    }

    .admin-statistic-value {
        margin: 6px 0 0;
        color: #0f172a;
        font-family:
            "Plus Jakarta Sans",
            sans-serif;
        font-size: 24px;
        font-weight: 800;
    }

    .admin-statistic-description {
        margin: 5px 0 0;
        color: #94a3b8;
        font-size: 8px;
        line-height: 1.5;
    }

    .admin-section {
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 21px;
        background: #ffffff;
        box-shadow:
            0 10px 35px
            rgba(15, 23, 42, 0.035);
    }

    .admin-section-header {
        display: flex;
        flex-direction: column;
        gap: 13px;
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .admin-section-label {
        margin: 0;
        color: #4f46e5;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.11em;
        text-transform: uppercase;
    }

    .admin-section-title {
        margin: 6px 0 0;
        color: #0f172a;
        font-family:
            "Plus Jakarta Sans",
            sans-serif;
        font-size: 17px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .admin-section-description {
        margin: 6px 0 0;
        color: #94a3b8;
        font-size: 9px;
        line-height: 1.65;
    }

    .admin-quick-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 11px;
        padding: 18px;
    }

    .admin-quick-action {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 15px;
        background: #ffffff;
        transition:
            border-color 160ms ease,
            background 160ms ease,
            transform 160ms ease;
    }

    .admin-quick-action:hover {
        border-color: #c7d2fe;
        background: #f8fafc;
        transform: translateY(-1px);
    }

    .admin-quick-icon {
        display: flex;
        width: 43px;
        height: 43px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
    }

    .admin-quick-content {
        min-width: 0;
        flex: 1;
    }

    .admin-quick-title {
        margin: 0;
        color: #334155;
        font-size: 10px;
        font-weight: 800;
    }

    .admin-quick-description {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 8px;
        line-height: 1.5;
    }

    .admin-table-wrapper {
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        min-width: 1200px;
        border-collapse: collapse;
    }

    .admin-table th {
        padding: 12px 14px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #64748b;
        font-size: 8px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
    }

    .admin-table td {
        padding: 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
        font-size: 9px;
        vertical-align: middle;
    }

    .admin-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .admin-student-cell {
        display: flex;
        min-width: 215px;
        align-items: center;
        gap: 10px;
    }

    .admin-avatar {
        display: flex;
        width: 39px;
        height: 39px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background:
            linear-gradient(
                135deg,
                #2563eb,
                #7c3aed
            );
        color: #ffffff;
        font-size: 9px;
        font-weight: 800;
    }

    .admin-student-name {
        margin: 0;
        color: #1e293b;
        font-size: 10px;
        font-weight: 800;
    }

    .admin-student-meta {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 8px;
    }

    .admin-status {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        border-radius: 999px;
        padding: 5px 9px;
        font-size: 8px;
        font-weight: 800;
        white-space: nowrap;
    }

    .admin-status.is-primary {
        background: #eef2ff;
        color: #4f46e5;
    }

    .admin-status.is-success {
        background: #ecfdf5;
        color: #15803d;
    }

    .admin-status.is-warning {
        background: #fff7ed;
        color: #c2410c;
    }

    .admin-status.is-danger {
        background: #fef2f2;
        color: #dc2626;
    }

    .admin-status.is-neutral {
        background: #f1f5f9;
        color: #64748b;
    }

    .admin-row-actions {
        display: flex;
        justify-content: flex-end;
        gap: 7px;
    }

    .admin-icon-link {
        display: inline-flex;
        width: 36px;
        height: 36px;
        align-items: center;
        justify-content: center;
        border: 1px solid #e5e7eb;
        border-radius: 11px;
        background: #ffffff;
        color: #64748b;
        transition:
            border-color 160ms ease,
            background 160ms ease,
            color 160ms ease;
    }

    .admin-icon-link:hover {
        border-color: #c7d2fe;
        background: #eef2ff;
        color: #4f46e5;
    }

    .admin-bottom-grid {
        display: grid;
        gap: 18px;
    }

    .admin-list {
        display: grid;
        padding: 6px 18px 18px;
    }

    .admin-list-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .admin-list-item:last-child {
        border-bottom: 0;
    }

    .admin-list-avatar {
        display: flex;
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 9px;
        font-weight: 800;
    }

    .admin-list-content {
        min-width: 0;
        flex: 1;
    }

    .admin-list-title {
        margin: 0;
        color: #1e293b;
        font-size: 10px;
        font-weight: 800;
        line-height: 1.55;
    }

    .admin-list-meta {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 8px;
        line-height: 1.55;
    }

    .admin-list-description {
        margin: 7px 0 0;
        color: #64748b;
        font-size: 9px;
        line-height: 1.65;
    }

    .admin-empty {
        display: flex;
        min-height: 190px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 25px;
        text-align: center;
    }

    .admin-empty-icon {
        display: flex;
        width: 53px;
        height: 53px;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: #eef2ff;
        color: #4f46e5;
    }

    .admin-empty-title {
        margin: 13px 0 0;
        color: #334155;
        font-size: 11px;
        font-weight: 800;
    }

    .admin-empty-description {
        max-width: 360px;
        margin: 6px 0 0;
        color: #94a3b8;
        font-size: 9px;
        line-height: 1.65;
    }

    .admin-section-footer {
        padding: 14px 18px;
        border-top: 1px solid #f1f5f9;
        background: #fcfcfd;
    }

    @media (min-width: 560px) {
        .admin-statistics {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .admin-quick-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 760px) {
        .admin-heading {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }

        .admin-section-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .admin-banner-content {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .admin-banner-icon {
            display: flex;
        }
    }

    @media (min-width: 1050px) {
        .admin-statistics {
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
        }

        .admin-quick-grid {
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
        }

        .admin-bottom-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1380px) {
        .admin-statistics {
            grid-template-columns:
                repeat(6, minmax(0, 1fr));
        }
    }
</style>

<div class="admin-dashboard">
    <section class="admin-heading">
        <div>
            <p class="admin-heading-label">
                Monitoring Fakultas
            </p>

            <h1 class="admin-heading-title">
                Dashboard Admin Fakultas
            </h1>

            <p class="admin-heading-description">
                Kelola pengguna, periode, data magang,
                instansi, pengumuman, arsip digital,
                Audit Trail, dan sinkronisasi SIAKAD.
            </p>
        </div>

        <div class="admin-heading-actions">
            <a
                href="{{ $links['panel'] }}"
                class="simmag-secondary-button"
            >
                <span class="material-symbols-rounded">
                    admin_panel_settings
                </span>

                Buka Panel Filament
            </a>

            @if ($links['syncLogs'])
                <a
                    href="{{ $links['syncLogs'] }}"
                    class="simmag-primary-button"
                >
                    <span class="material-symbols-rounded">
                        sync
                    </span>

                    Sinkronisasi SIAKAD
                </a>
            @endif
        </div>
    </section>

    <section class="admin-banner">
        <div class="admin-banner-content">
            <div>
                <div class="admin-system-status">
                    <span class="admin-system-dot"></span>

                    Sistem Berjalan Normal
                </div>

                <h2 class="admin-banner-title">
                    Selamat datang, {{ $adminName }}
                </h2>

                <p class="admin-banner-description">
                    Seluruh informasi pada dashboard ini
                    berasal dari database SIMMAG.
                    Gunakan Panel Filament untuk mengelola
                    data utama dan penugasan magang.
                </p>

                <div class="admin-banner-meta">
                    <span class="admin-banner-meta-item">
                        <span class="material-symbols-rounded">
                            badge
                        </span>

                        {{ $adminIdentifier }}
                    </span>

                    <span class="admin-banner-meta-item">
                        <span class="material-symbols-rounded">
                            calendar_month
                        </span>

                        {{ $period['name'] }}
                    </span>

                    <span class="admin-banner-meta-item">
                        <span class="material-symbols-rounded">
                            info
                        </span>

                        {{ $period['description'] }}
                    </span>
                </div>
            </div>

            <div class="admin-banner-icon">
                <span class="material-symbols-rounded">
                    admin_panel_settings
                </span>
            </div>
        </div>
    </section>

    <section class="admin-statistics">
        @foreach ($statistics as $statistic)
            @php
                $statisticStyle =
                    $toneStyle[
                        $statistic['tone']
                    ]
                    ?? $toneStyle['blue'];
            @endphp

            @if ($statistic['url'])
                <a
                    href="{{ $statistic['url'] }}"
                    class="admin-statistic-card"
                >
                    <div
                        class="admin-statistic-icon"
                        style="
                            background:
                                {{ $statisticStyle['background'] }};
                            color:
                                {{ $statisticStyle['color'] }};
                        "
                    >
                        <span class="material-symbols-rounded">
                            {{ $statistic['icon'] }}
                        </span>
                    </div>

                    <div>
                        <p class="admin-statistic-label">
                            {{ $statistic['label'] }}
                        </p>

                        <p class="admin-statistic-value">
                            {{ number_format(
                                (int) $statistic['value'],
                                0,
                                ',',
                                '.'
                            ) }}
                        </p>

                        <p class="admin-statistic-description">
                            {{ $statistic['description'] }}
                        </p>
                    </div>
                </a>
            @else
                <article class="admin-statistic-card">
                    <div
                        class="admin-statistic-icon"
                        style="
                            background:
                                {{ $statisticStyle['background'] }};
                            color:
                                {{ $statisticStyle['color'] }};
                        "
                    >
                        <span class="material-symbols-rounded">
                            {{ $statistic['icon'] }}
                        </span>
                    </div>

                    <div>
                        <p class="admin-statistic-label">
                            {{ $statistic['label'] }}
                        </p>

                        <p class="admin-statistic-value">
                            {{ number_format(
                                (int) $statistic['value'],
                                0,
                                ',',
                                '.'
                            ) }}
                        </p>

                        <p class="admin-statistic-description">
                            {{ $statistic['description'] }}
                        </p>
                    </div>
                </article>
            @endif
        @endforeach
    </section>

    <section class="admin-section">
        <div class="admin-section-header">
            <div>
                <p class="admin-section-label">
                    Akses Cepat
                </p>

                <h2 class="admin-section-title">
                    Pengelolaan SIMMAG
                </h2>

                <p class="admin-section-description">
                    Seluruh menu di bawah terhubung
                    langsung ke resource Panel Filament.
                </p>
            </div>
        </div>

        <div class="admin-quick-grid">
            @foreach ($quickActions as $action)
                @php
                    $actionStyle =
                        $toneStyle[
                            $action['tone']
                        ]
                        ?? $toneStyle['blue'];
                @endphp

                <a
                    href="{{ $action['url'] }}"
                    class="admin-quick-action"
                >
                    <div
                        class="admin-quick-icon"
                        style="
                            background:
                                {{ $actionStyle['background'] }};
                            color:
                                {{ $actionStyle['color'] }};
                        "
                    >
                        <span class="material-symbols-rounded">
                            {{ $action['icon'] }}
                        </span>
                    </div>

                    <div class="admin-quick-content">
                        <p class="admin-quick-title">
                            {{ $action['title'] }}
                        </p>

                        <p class="admin-quick-description">
                            {{ $action['description'] }}
                        </p>
                    </div>

                    <span class="material-symbols-rounded">
                        chevron_right
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="admin-section">
        <div class="admin-section-header">
            <div>
                <p class="admin-section-label">
                    Monitoring Alur Magang
                </p>

                <h2 class="admin-section-title">
                    Data Magang Terbaru
                </h2>

                <p class="admin-section-description">
                    Admin hanya memonitor proses.
                    Persetujuan tetap dilakukan oleh
                    Pembimbing Lapangan dan Dosen Pembimbing.
                </p>
            </div>

            @if ($links['internships'])
                <a
                    href="{{ $links['internships'] }}"
                    class="simmag-secondary-button"
                >
                    Lihat Semua Data
                </a>
            @endif
        </div>

        @if ($monitoringRows->isNotEmpty())
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Instansi</th>
                            <th>Dosen</th>
                            <th>Pembimbing Lapangan</th>
                            <th>Kerangka Acuan</th>
                            <th>Logbook</th>
                            <th>Status Magang</th>
                            <th style="text-align:right;">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($monitoringRows as $internship)
                            <tr>
                                <td>
                                    <div class="admin-student-cell">
                                        <div class="admin-avatar">
                                            {{ $internship['initials'] }}
                                        </div>

                                        <div>
                                            <p class="admin-student-name">
                                                {{ $internship['student'] }}
                                            </p>

                                            <p class="admin-student-meta">
                                                {{ $internship['identifier'] }}

                                                ·

                                                {{ $internship['program_study'] }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    {{ $internship['company'] }}
                                </td>

                                <td>
                                    {{ $internship['lecturer'] }}
                                </td>

                                <td>
                                    {{ $internship['field_supervisor'] }}
                                </td>

                                <td>
                                    <span
                                        class="admin-status is-{{ $internship['framework_tone'] }}"
                                    >
                                        {{ $internship['framework_status'] }}
                                    </span>
                                </td>

                                <td>
                                    <span
                                        class="admin-status is-{{ $internship['logbook_tone'] }}"
                                    >
                                        {{ $internship['logbook_status'] }}
                                    </span>
                                </td>

                                <td>
                                    <span
                                        class="admin-status is-{{ $internship['internship_tone'] }}"
                                    >
                                        {{ $internship['internship_status'] }}
                                    </span>
                                </td>

                                <td>
                                    <div class="admin-row-actions">
                                        @if ($internship['detail_url'])
                                            <a
                                                href="{{ $internship['detail_url'] }}"
                                                class="admin-icon-link"
                                                aria-label="Lihat detail magang"
                                                title="Lihat detail"
                                            >
                                                <span class="material-symbols-rounded">
                                                    visibility
                                                </span>
                                            </a>
                                        @endif

                                        @if ($internship['audit_url'])
                                            <a
                                                href="{{ $internship['audit_url'] }}"
                                                class="admin-icon-link"
                                                aria-label="Lihat Audit Trail"
                                                title="Lihat Audit Trail"
                                            >
                                                <span class="material-symbols-rounded">
                                                    history
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="admin-empty">
                <div class="admin-empty-icon">
                    <span class="material-symbols-rounded">
                        work_off
                    </span>
                </div>

                <p class="admin-empty-title">
                    Data magang belum tersedia
                </p>

                <p class="admin-empty-description">
                    Tambahkan data magang melalui
                    Panel Filament agar monitoring
                    tampil pada dashboard.
                </p>
            </div>
        @endif
    </section>

    <section class="admin-bottom-grid">
        <article class="admin-section">
            <div class="admin-section-header">
                <div>
                    <p class="admin-section-label">
                        Audit Trail
                    </p>

                    <h2 class="admin-section-title">
                        Aktivitas Terbaru
                    </h2>
                </div>

                @if ($links['auditTrails'])
                    <a
                        href="{{ $links['auditTrails'] }}"
                        class="simmag-secondary-button"
                    >
                        Lihat Semua
                    </a>
                @endif
            </div>

            @if ($auditRows->isNotEmpty())
                <div class="admin-list">
                    @foreach ($auditRows as $audit)
                        <div class="admin-list-item">
                            <div class="admin-list-avatar">
                                {{ $audit['initials'] }}
                            </div>

                            <div class="admin-list-content">
                                <p class="admin-list-title">
                                    {{ $audit['actor'] }}
                                </p>

                                <p class="admin-list-meta">
                                    {{ $audit['role'] }}

                                    ·

                                    {{ $audit['time'] }}
                                </p>

                                <p class="admin-list-description">
                                    {{ $audit['activity'] }}
                                </p>

                                <p class="admin-list-meta">
                                    IP:
                                    {{ $audit['ip'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="admin-empty">
                    <div class="admin-empty-icon">
                        <span class="material-symbols-rounded">
                            history
                        </span>
                    </div>

                    <p class="admin-empty-title">
                        Belum ada Audit Trail
                    </p>

                    <p class="admin-empty-description">
                        Aktivitas persetujuan dan perubahan
                        data akan tampil di bagian ini.
                    </p>
                </div>
            @endif
        </article>

        <article class="admin-section">
            <div class="admin-section-header">
                <div>
                    <p class="admin-section-label">
                        Pengumuman
                    </p>

                    <h2 class="admin-section-title">
                        Publikasi Terbaru
                    </h2>
                </div>

                <div class="admin-heading-actions">
                    @if ($links['announcementCreate'])
                        <a
                            href="{{ $links['announcementCreate'] }}"
                            class="simmag-primary-button"
                        >
                            <span class="material-symbols-rounded">
                                add
                            </span>

                            Tambah
                        </a>
                    @endif

                    @if ($links['announcements'])
                        <a
                            href="{{ $links['announcements'] }}"
                            class="simmag-secondary-button"
                        >
                            Kelola
                        </a>
                    @endif
                </div>
            </div>

            @if ($announcementRows->isNotEmpty())
                <div class="admin-list">
                    @foreach ($announcementRows as $announcement)
                        <div class="admin-list-item">
                            <div class="admin-list-avatar">
                                <span class="material-symbols-rounded">
                                    campaign
                                </span>
                            </div>

                            <div class="admin-list-content">
                                <p class="admin-list-title">
                                    {{ $announcement['title'] }}
                                </p>

                                <p class="admin-list-meta">
                                    {{ $announcement['audience'] }}

                                    ·

                                    {{ $announcement['published_at'] }}
                                </p>
                            </div>

                            <span
                                class="admin-status is-{{ $announcement['tone'] }}"
                            >
                                {{ $announcement['status'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="admin-empty">
                    <div class="admin-empty-icon">
                        <span class="material-symbols-rounded">
                            campaign
                        </span>
                    </div>

                    <p class="admin-empty-title">
                        Belum ada pengumuman
                    </p>

                    <p class="admin-empty-description">
                        Tambahkan pengumuman melalui
                        Panel Filament.
                    </p>
                </div>
            @endif
        </article>
    </section>

    <section class="admin-section">
        <div class="admin-section-header">
            <div>
                <p class="admin-section-label">
                    Integrasi SIAKAD
                </p>

                <h2 class="admin-section-title">
                    Riwayat Sinkronisasi
                </h2>

                <p class="admin-section-description">
                    Data ditampilkan dari tabel
                    riwayat sinkronisasi SIAKAD.
                </p>
            </div>

            @if ($links['syncLogs'])
                <a
                    href="{{ $links['syncLogs'] }}"
                    class="simmag-secondary-button"
                >
                    Buka Log Sinkronisasi
                </a>
            @endif
        </div>

        @if ($syncRows->isNotEmpty())
            <div class="admin-table-wrapper">
                <table
                    class="admin-table"
                    style="min-width:760px;"
                >
                    <thead>
                        <tr>
                            <th>Sumber Data</th>
                            <th>Total</th>
                            <th>Berhasil</th>
                            <th>Gagal</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($syncRows as $sync)
                            <tr>
                                <td>
                                    <strong>
                                        {{ $sync['source'] }}
                                    </strong>
                                </td>

                                <td>
                                    {{ number_format(
                                        $sync['total'],
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td>
                                    {{ number_format(
                                        $sync['success'],
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td>
                                    {{ number_format(
                                        $sync['failed'],
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td>
                                    <span
                                        class="admin-status is-{{ $sync['tone'] }}"
                                    >
                                        {{ $sync['status'] }}
                                    </span>
                                </td>

                                <td>
                                    {{ $sync['time'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="admin-empty">
                <div class="admin-empty-icon">
                    <span class="material-symbols-rounded">
                        sync
                    </span>
                </div>

                <p class="admin-empty-title">
                    Belum ada riwayat sinkronisasi
                </p>

                <p class="admin-empty-description">
                    Riwayat akan tampil setelah proses
                    sinkronisasi SIAKAD dijalankan.
                </p>
            </div>
        @endif
    </section>
</div>