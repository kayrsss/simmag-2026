@php
    $statusClass = function (
        ?string $status
    ): string {
        $normalized = str(
            (string) $status
        )
            ->lower()
            ->replace([
                ' ',
                '-',
            ], '_')
            ->toString();

        if (
            str_contains(
                $normalized,
                'revisi'
            )
            || str_contains(
                $normalized,
                'tolak'
            )
        ) {
            return 'is-danger';
        }

        if (
            str_contains(
                $normalized,
                'menunggu'
            )
            || str_contains(
                $normalized,
                'diajukan'
            )
        ) {
            return 'is-warning';
        }

        if (
            str_contains(
                $normalized,
                'valid'
            )
            || str_contains(
                $normalized,
                'setuju'
            )
            || str_contains(
                $normalized,
                'selesai'
            )
        ) {
            return 'is-success';
        }

        return 'is-primary';
    };

    $statToneClasses = [
        'primary' =>
            'background:#eef2ff;color:#4f46e5;',

        'orange' =>
            'background:#fff7ed;color:#ea580c;',

        'green' =>
            'background:#ecfdf5;color:#16a34a;',

        'violet' =>
            'background:#f5f3ff;color:#7c3aed;',

        'warning' =>
            'background:#fff7ed;color:#d97706;',
    ];

    $taskToneClasses = [
        'primary' => [
            'icon' =>
                'background:#eef2ff;color:#4f46e5;',

            'border' =>
                'border-color:#e0e7ff;',
        ],

        'warning' => [
            'icon' =>
                'background:#fff7ed;color:#ea580c;',

            'border' =>
                'border-color:#fed7aa;',
        ],

        'danger' => [
            'icon' =>
                'background:#fef2f2;color:#dc2626;',

            'border' =>
                'border-color:#fecaca;',
        ],
    ];

    $contactToneClasses = [
        'green' =>
            'background:#ecfdf5;color:#16a34a;',

        'violet' =>
            'background:#f5f3ff;color:#7c3aed;',

        'blue' =>
            'background:#eff6ff;color:#2563eb;',
    ];
@endphp

<style>
    .student-dashboard {
        display: grid;
        gap: 22px;
    }

    .student-heading-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .student-greeting {
        margin: 0;
        color: #0f172a;
        font-family:
            "Plus Jakarta Sans",
            sans-serif;
        font-size: clamp(
            25px,
            4vw,
            34px
        );
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .student-greeting-description {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.7;
    }

    .student-quick-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 11px;
    }

    .student-quick-action {
        display: flex;
        min-height: 82px;
        align-items: center;
        gap: 13px;
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #ffffff;
        transition:
            border-color 160ms ease,
            background 160ms ease,
            transform 160ms ease,
            box-shadow 160ms ease;
    }

    .student-quick-action:hover {
        border-color: #c7d2fe;
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow:
            0 12px 30px
            rgba(15, 23, 42, 0.06);
    }

    .student-quick-icon {
        display: flex;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #eef2ff;
        color: #4f46e5;
    }

    .student-quick-content {
        min-width: 0;
        flex: 1;
    }

    .student-quick-title {
        margin: 0;
        color: #334155;
        font-size: 10px;
        font-weight: 800;
    }

    .student-quick-description {
        margin: 5px 0 0;
        color: #94a3b8;
        font-size: 8px;
        line-height: 1.5;
    }

    .student-stat-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .student-stat-card {
        display: flex;
        min-height: 126px;
        align-items: center;
        gap: 16px;
        padding: 18px;
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

    .student-stat-card:hover {
        border-color: #c7d2fe;
        transform: translateY(-2px);
        box-shadow:
            0 14px 34px
            rgba(15, 23, 42, 0.07);
    }

    .student-stat-icon {
        display: flex;
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 17px;
    }

    .student-stat-label {
        margin: 0;
        color: #64748b;
        font-size: 10px;
        font-weight: 600;
    }

    .student-stat-value {
        margin: 6px 0 0;
        color: #0f172a;
        font-family:
            "Plus Jakarta Sans",
            sans-serif;
        font-size: 25px;
        font-weight: 800;
    }

    .student-stat-description {
        margin: 5px 0 0;
        color: #94a3b8;
        font-size: 9px;
    }

    .student-dashboard-columns {
        display: grid;
        gap: 18px;
    }

    .student-dashboard-main,
    .student-dashboard-sidebar {
        display: grid;
        align-content: start;
        gap: 18px;
    }

    .student-card-body {
        padding: 18px 20px;
    }

    .student-task-list {
        display: grid;
        gap: 10px;
    }

    .student-task-item {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .student-task-content {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 12px;
    }

    .student-task-icon {
        display: flex;
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
    }

    .student-task-title {
        margin: 0;
        color: #1e293b;
        font-size: 11px;
        font-weight: 700;
    }

    .student-task-description {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 9px;
        line-height: 1.65;
    }

    .student-task-action {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        gap: 5px;
    }

    .student-table-wrapper {
        overflow-x: auto;
    }

    .student-table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
    }

    .student-table th {
        padding: 11px 12px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #64748b;
        font-size: 8px;
        font-weight: 700;
        text-align: left;
        text-transform: uppercase;
    }

    .student-table td {
        padding: 13px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
        font-size: 9px;
        vertical-align: middle;
    }

    .student-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .student-table-activity {
        max-width: 320px;
        overflow: hidden;
        color: #334155;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .student-table-evidence {
        display: inline-flex;
        max-width: 180px;
        align-items: center;
        gap: 6px;
        color: #64748b;
    }

    .student-table-evidence span:last-child {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .student-contact-list {
        display: grid;
    }

    .student-contact-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .student-contact-item:last-child {
        border-bottom: 0;
    }

    .student-contact-avatar {
        display: flex;
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .student-contact-content {
        min-width: 0;
        flex: 1;
    }

    .student-contact-role {
        margin: 0;
        color: #1e293b;
        font-size: 10px;
        font-weight: 700;
    }

    .student-contact-name {
        overflow: hidden;
        margin: 4px 0 0;
        color: #64748b;
        font-size: 9px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .student-contact-detail {
        overflow: hidden;
        margin: 3px 0 0;
        color: #94a3b8;
        font-size: 8px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .student-contact-actions {
        display: flex;
        flex-shrink: 0;
        gap: 6px;
    }

    .student-contact-action {
        display: flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        color: #64748b;
        transition:
            background 160ms ease,
            color 160ms ease;
    }

    .student-contact-action:hover {
        background: #eef2ff;
        color: #4f46e5;
    }

    .student-agenda-list {
        display: grid;
        gap: 10px;
    }

    .student-agenda-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        transition:
            border-color 160ms ease,
            background 160ms ease;
    }

    a.student-agenda-item:hover {
        border-color: #c7d2fe;
        background: #f8fafc;
    }

    .student-agenda-date {
        display: flex;
        width: 46px;
        height: 52px;
        flex-shrink: 0;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #f8fafc;
        color: #2563eb;
    }

    .student-agenda-date strong {
        font-size: 16px;
        font-weight: 800;
    }

    .student-agenda-date span {
        margin-top: 2px;
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .student-agenda-title {
        margin: 0;
        color: #1e293b;
        font-size: 10px;
        font-weight: 700;
    }

    .student-agenda-description {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 8px;
        line-height: 1.5;
    }

    .student-agenda-time {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 8px;
    }

    .student-announcement-list {
        display: grid;
    }

    .student-announcement-item {
        position: relative;
        display: block;
        padding: 14px 0 14px 18px;
        border-bottom: 1px solid #f1f5f9;
    }

    .student-announcement-item:last-child {
        border-bottom: 0;
    }

    .student-announcement-item::before {
        position: absolute;
        top: 20px;
        left: 2px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #2563eb;
        content: "";
    }

    .student-announcement-title {
        margin: 0;
        color: #1e293b;
        font-size: 10px;
        font-weight: 700;
        line-height: 1.5;
    }

    .student-announcement-description {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 8px;
        line-height: 1.6;
    }

    .student-announcement-date {
        margin: 6px 0 0;
        color: #94a3b8;
        font-size: 8px;
    }

    @media (min-width: 560px) {
        .student-quick-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .student-stat-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .student-task-item {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    @media (min-width: 900px) {
        .student-quick-grid {
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
        }
    }

    @media (min-width: 1180px) {
        .student-stat-grid {
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
        }

        .student-dashboard-columns {
            grid-template-columns:
                minmax(0, 1.8fr)
                minmax(290px, 0.72fr);
        }

        .student-quick-grid {
            grid-template-columns:
                repeat(6, minmax(0, 1fr));
        }
    }
</style>

<div class="student-dashboard">
    <section class="student-heading-row">
        <h1 class="student-greeting">
            Halo, {{ $studentName }} 👋
        </h1>

        <p class="student-greeting-description">
            Pantau kegiatan magang, lengkapi tugas,
            dan akses seluruh fitur mahasiswa dari halaman ini.
        </p>
    </section>

    <section class="student-quick-grid">
        @foreach ($quickActions as $action)
            <a
                href="{{ $action['url'] }}"
                class="student-quick-action"
            >
                <div class="student-quick-icon">
                    <span class="material-symbols-rounded">
                        {{ $action['icon'] }}
                    </span>
                </div>

                <div class="student-quick-content">
                    <p class="student-quick-title">
                        {{ $action['label'] }}
                    </p>

                    <p class="student-quick-description">
                        {{ $action['description'] }}
                    </p>
                </div>

                <span class="material-symbols-rounded">
                    chevron_right
                </span>
            </a>
        @endforeach
    </section>

    <section class="student-stat-grid">
        @foreach ($statistics as $statistic)
            <a
                href="{{ $statistic['url'] }}"
                class="student-stat-card"
            >
                <div
                    class="student-stat-icon"
                    style="{{ $statToneClasses[$statistic['tone']] ?? $statToneClasses['primary'] }}"
                >
                    <span class="material-symbols-rounded">
                        {{ $statistic['icon'] }}
                    </span>
                </div>

                <div>
                    <p class="student-stat-label">
                        {{ $statistic['label'] }}
                    </p>

                    <p class="student-stat-value">
                        {{ $statistic['value'] }}
                    </p>

                    <p class="student-stat-description">
                        {{ $statistic['description'] }}
                    </p>
                </div>
            </a>
        @endforeach
    </section>

    <section class="student-dashboard-columns">
        <div class="student-dashboard-main">
            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Yang Perlu Kamu Kerjakan
                        </h2>

                        <p class="simmag-card-description">
                            Tugas ditampilkan berdasarkan status
                            data magang di database.
                        </p>
                    </div>
                </div>

                <div class="student-card-body">
                    @if (count($tasks) > 0)
                        <div class="student-task-list">
                            @foreach ($tasks as $task)
                                @php
                                    $tone =
                                        $taskToneClasses[
                                            $task['tone']
                                        ]
                                        ?? $taskToneClasses[
                                            'primary'
                                        ];
                                @endphp

                                <div
                                    class="student-task-item"
                                    style="{{ $tone['border'] }}"
                                >
                                    <div class="student-task-content">
                                        <div
                                            class="student-task-icon"
                                            style="{{ $tone['icon'] }}"
                                        >
                                            <span class="material-symbols-rounded">
                                                {{ $task['icon'] }}
                                            </span>
                                        </div>

                                        <div>
                                            <p class="student-task-title">
                                                {{ $task['title'] }}
                                            </p>

                                            <p class="student-task-description">
                                                {{ $task['description'] }}
                                            </p>
                                        </div>
                                    </div>

                                    @if (
                                        filled($task['url'])
                                        && filled($task['action'])
                                    )
                                        <div class="student-task-action">
                                            <a
                                                href="{{ $task['url'] }}"
                                                class="simmag-secondary-button"
                                            >
                                                {{ $task['action'] }}
                                            </a>

                                            <span class="material-symbols-rounded">
                                                chevron_right
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="simmag-empty-state">
                            <div class="simmag-empty-icon">
                                <span class="material-symbols-rounded">
                                    task_alt
                                </span>
                            </div>

                            <p class="simmag-empty-title">
                                Tidak ada tugas mendesak
                            </p>

                            <p class="simmag-empty-description">
                                Semua proses yang membutuhkan
                                tindakan sudah selesai.
                            </p>
                        </div>
                    @endif
                </div>
            </article>

            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Logbook Terbaru
                        </h2>

                        <p class="simmag-card-description">
                            Aktivitas terbaru yang tersimpan
                            pada database.
                        </p>
                    </div>

                    @if (
                        Route::has(
                            'student.logbooks.index'
                        )
                    )
                        <a
                            href="{{ route(
                                'student.logbooks.index'
                            ) }}"
                            class="simmag-secondary-button"
                        >
                            Lihat Semua
                        </a>
                    @endif
                </div>

                @if ($latestLogbooks->isNotEmpty())
                    <div class="student-table-wrapper">
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Aktivitas</th>
                                    <th>Bukti</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($latestLogbooks as $logbook)
                                    <tr>
                                        <td>
                                            {{ $logbook['date']
                                                ? $logbook['date']
                                                    ->translatedFormat(
                                                        'd M Y'
                                                    )
                                                : '-' }}
                                        </td>

                                        <td>
                                            <div class="student-table-activity">
                                                {{ $logbook['activity'] }}
                                            </div>
                                        </td>

                                        <td>
                                            @if ($logbook['evidence'])
                                                <span class="student-table-evidence">
                                                    <span class="material-symbols-rounded">
                                                        attach_file
                                                    </span>

                                                    <span>
                                                        {{ $logbook['evidence'] }}
                                                    </span>
                                                </span>
                                            @else
                                                <span style="color:#94a3b8;">
                                                    Tidak ada
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <span
                                                class="simmag-status {{ $statusClass(
                                                    $logbook['status']
                                                ) }}"
                                            >
                                                {{ $logbook['status'] }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($logbook['url'])
                                                <a
                                                    href="{{ $logbook['url'] }}"
                                                    class="simmag-icon-button"
                                                    style="
                                                        width:34px;
                                                        height:34px;
                                                        border-radius:10px;
                                                    "
                                                    title="Buka Logbook"
                                                    aria-label="Buka Logbook"
                                                >
                                                    <span class="material-symbols-rounded">
                                                        visibility
                                                    </span>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="simmag-empty-state">
                        <div class="simmag-empty-icon">
                            <span class="material-symbols-rounded">
                                edit_note
                            </span>
                        </div>

                        <p class="simmag-empty-title">
                            Belum ada logbook
                        </p>

                        <p class="simmag-empty-description">
                            Gunakan menu Isi Logbook untuk
                            mencatat aktivitas harian.
                        </p>

                        @if (
                            Route::has(
                                'student.logbooks.index'
                            )
                        )
                            <a
                                href="{{ route(
                                    'student.logbooks.index'
                                ) }}"
                                class="simmag-primary-button"
                                style="margin-top:15px;"
                            >
                                Isi Logbook
                            </a>
                        @endif
                    </div>
                @endif
            </article>
        </div>

        <aside class="student-dashboard-sidebar">
            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Kontak Terkait
                        </h2>

                        <p class="simmag-card-description">
                            Pembimbing dan pengelola magang.
                        </p>
                    </div>
                </div>

                <div class="student-card-body">
                    <div class="student-contact-list">
                        @foreach ($contacts as $contact)
                            <div class="student-contact-item">
                                <div
                                    class="student-contact-avatar"
                                    style="{{ $contactToneClasses[$contact['tone']] ?? $contactToneClasses['blue'] }}"
                                >
                                    <span class="material-symbols-rounded">
                                        person
                                    </span>
                                </div>

                                <div class="student-contact-content">
                                    <p class="student-contact-role">
                                        {{ $contact['role'] }}
                                    </p>

                                    <p class="student-contact-name">
                                        {{ $contact['name'] }}
                                    </p>

                                    @if ($contact['email'])
                                        <p class="student-contact-detail">
                                            {{ $contact['email'] }}
                                        </p>
                                    @elseif ($contact['phone'])
                                        <p class="student-contact-detail">
                                            {{ $contact['phone'] }}
                                        </p>
                                    @endif
                                </div>

                                <div class="student-contact-actions">
                                    @if ($contact['email'])
                                        <a
                                            href="mailto:{{ $contact['email'] }}"
                                            class="student-contact-action"
                                            title="Kirim Email"
                                            aria-label="Kirim Email"
                                        >
                                            <span class="material-symbols-rounded">
                                                mail
                                            </span>
                                        </a>
                                    @endif

                                    @if ($contact['whatsapp_url'])
                                        <a
                                            href="{{ $contact['whatsapp_url'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="student-contact-action"
                                            title="Buka WhatsApp"
                                            aria-label="Buka WhatsApp"
                                        >
                                            <span class="material-symbols-rounded">
                                                chat
                                            </span>
                                        </a>
                                    @elseif ($contact['phone_url'])
                                        <a
                                            href="{{ $contact['phone_url'] }}"
                                            class="student-contact-action"
                                            title="Hubungi"
                                            aria-label="Hubungi"
                                        >
                                            <span class="material-symbols-rounded">
                                                call
                                            </span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>

            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Agenda Mendatang
                        </h2>

                        <p class="simmag-card-description">
                            Jadwal bimbingan dan batas periode.
                        </p>
                    </div>
                </div>

                <div class="student-card-body">
                    @if ($agenda->isNotEmpty())
                        <div class="student-agenda-list">
                            @foreach ($agenda as $agendaItem)
                                @if ($agendaItem['url'])
                                    <a
                                        href="{{ $agendaItem['url'] }}"
                                        class="student-agenda-item"
                                    >
                                        <div class="student-agenda-date">
                                            <strong>
                                                {{ $agendaItem['date']->format('d') }}
                                            </strong>

                                            <span>
                                                {{ $agendaItem['date']->translatedFormat('M') }}
                                            </span>
                                        </div>

                                        <div>
                                            <p class="student-agenda-title">
                                                {{ $agendaItem['title'] }}
                                            </p>

                                            <p class="student-agenda-description">
                                                {{ $agendaItem['description'] }}
                                            </p>

                                            <p class="student-agenda-time">
                                                {{ $agendaItem['date']->translatedFormat(
                                                    'd F Y'
                                                ) }}
                                            </p>
                                        </div>
                                    </a>
                                @else
                                    <div class="student-agenda-item">
                                        <div class="student-agenda-date">
                                            <strong>
                                                {{ $agendaItem['date']->format('d') }}
                                            </strong>

                                            <span>
                                                {{ $agendaItem['date']->translatedFormat('M') }}
                                            </span>
                                        </div>

                                        <div>
                                            <p class="student-agenda-title">
                                                {{ $agendaItem['title'] }}
                                            </p>

                                            <p class="student-agenda-description">
                                                {{ $agendaItem['description'] }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="simmag-empty-state">
                            <div class="simmag-empty-icon">
                                <span class="material-symbols-rounded">
                                    event_available
                                </span>
                            </div>

                            <p class="simmag-empty-title">
                                Belum ada agenda
                            </p>

                            <p class="simmag-empty-description">
                                Jadwal bimbingan akan tampil
                                setelah pengajuan dibuat.
                            </p>
                        </div>
                    @endif
                </div>
            </article>

            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Pengumuman Terbaru
                        </h2>

                        <p class="simmag-card-description">
                            Informasi terbaru dari SIMMAG.
                        </p>
                    </div>

                    @if (
                        Route::has(
                            'announcements.index'
                        )
                    )
                        <a
                            href="{{ route(
                                'announcements.index'
                            ) }}"
                            class="simmag-secondary-button"
                        >
                            Lihat Semua
                        </a>
                    @endif
                </div>

                <div class="student-card-body">
                    @if ($announcements->isNotEmpty())
                        <div class="student-announcement-list">
                            @foreach ($announcements as $announcement)
                                @if ($announcement['url'])
                                    <a
                                        href="{{ $announcement['url'] }}"
                                        class="student-announcement-item"
                                    >
                                        <p class="student-announcement-title">
                                            {{ $announcement['title'] }}
                                        </p>

                                        @if ($announcement['description'])
                                            <p class="student-announcement-description">
                                                {{ $announcement['description'] }}
                                            </p>
                                        @endif

                                        @if ($announcement['date'])
                                            <p class="student-announcement-date">
                                                {{ $announcement['date']->translatedFormat(
                                                    'd F Y'
                                                ) }}
                                            </p>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="simmag-empty-state">
                            <div class="simmag-empty-icon">
                                <span class="material-symbols-rounded">
                                    campaign
                                </span>
                            </div>

                            <p class="simmag-empty-title">
                                Belum ada pengumuman
                            </p>

                            <p class="simmag-empty-description">
                                Pengumuman terbaru akan tampil di sini.
                            </p>
                        </div>
                    @endif
                </div>
            </article>
        </aside>
    </section>
</div>