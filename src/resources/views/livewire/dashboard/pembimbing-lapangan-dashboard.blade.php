@php
    $statusClass = function (
        ?string $status
    ): string {
        $normalized = str((string) $status)
            ->lower()
            ->replace([
                ' ',
                '-',
            ], '_')
            ->toString();

        if (
            str_contains(
                $normalized,
                'selesai'
            )
            || str_contains(
                $normalized,
                'aktif'
            )
        ) {
            return 'is-success';
        }

        if (
            str_contains(
                $normalized,
                'revisi'
            )
        ) {
            return 'is-danger';
        }

        if (
            str_contains(
                $normalized,
                'menunggu'
            )
        ) {
            return 'is-warning';
        }

        return 'is-primary';
    };

    $statisticCards = [
        [
            'label' => 'Mahasiswa Magang',
            'value' => $statistics['students'],
            'description' => 'Mahasiswa yang ditugaskan',
            'icon' => 'groups',
            'background' => '#eff6ff',
            'color' => '#2563eb',
        ],
        [
            'label' => 'Kerangka Acuan',
            'value' => $statistics['frameworks_waiting'],
            'description' => 'Menunggu review',
            'icon' => 'description',
            'background' => '#f5f3ff',
            'color' => '#7c3aed',
        ],
        [
            'label' => 'Logbook',
            'value' => $statistics['logbooks_waiting'],
            'description' => 'Menunggu validasi',
            'icon' => 'fact_check',
            'background' => '#fff7ed',
            'color' => '#ea580c',
        ],
        [
            'label' => 'Penilaian',
            'value' => $statistics['assessments_pending'],
            'description' => 'Belum diselesaikan',
            'icon' => 'grading',
            'background' => '#ecfdf5',
            'color' => '#16a34a',
        ],
    ];
@endphp

<style>
    .field-dashboard {
        display: grid;
        gap: 22px;
    }

    .field-dashboard-heading {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .field-dashboard-title {
        margin: 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: clamp(25px, 3vw, 32px);
        font-weight: 800;
        letter-spacing: -0.035em;
    }

    .field-dashboard-description {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.7;
    }

    .field-statistics {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .field-statistic-card {
        display: flex;
        min-height: 126px;
        align-items: center;
        gap: 16px;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        box-shadow:
            0 8px 28px rgba(15, 23, 42, 0.035);
    }

    .field-statistic-icon {
        display: flex;
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 17px;
    }

    .field-statistic-label {
        margin: 0;
        color: #64748b;
        font-size: 10px;
        font-weight: 600;
    }

    .field-statistic-value {
        margin: 6px 0 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 25px;
        font-weight: 800;
    }

    .field-statistic-description {
        margin: 5px 0 0;
        color: #94a3b8;
        font-size: 9px;
    }

    .field-dashboard-columns {
        display: grid;
        gap: 18px;
    }

    .field-main-column,
    .field-side-column {
        display: grid;
        align-content: start;
        gap: 18px;
    }

    .field-logbook-list {
        display: grid;
    }

    .field-logbook-item {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 16px 20px;
        border-top: 1px solid #f1f5f9;
    }

    .field-logbook-item:first-child {
        border-top: 0;
    }

    .field-logbook-content {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 12px;
    }

    .field-avatar {
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
        font-size: 10px;
        font-weight: 800;
    }

    .field-logbook-information {
        min-width: 0;
        flex: 1;
    }

    .field-student-name {
        margin: 0;
        color: #1e293b;
        font-size: 11px;
        font-weight: 700;
    }

    .field-student-identifier {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 8px;
    }

    .field-logbook-activity {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 10px;
        line-height: 1.65;
    }

    .field-student-list {
        display: grid;
    }

    .field-student-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .field-student-item:last-child {
        border-bottom: 0;
    }

    .field-student-content {
        min-width: 0;
        flex: 1;
    }

    .field-student-status {
        margin-top: 7px;
    }

    .field-primary-action {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border: 1px solid #e0e7ff;
        border-radius: 15px;
        background: #eef2ff;
        transition:
            background 160ms ease,
            transform 160ms ease;
    }

    .field-primary-action:hover {
        background: #e0e7ff;
        transform: translateY(-1px);
    }

    .field-primary-action-icon {
        display: flex;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #4f46e5;
        color: #ffffff;
    }

    .field-primary-action-content {
        min-width: 0;
        flex: 1;
    }

    .field-primary-action-title {
        margin: 0;
        color: #312e81;
        font-size: 11px;
        font-weight: 700;
    }

    .field-primary-action-description {
        margin: 5px 0 0;
        color: #6366f1;
        font-size: 9px;
    }

    .field-notice {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 15px;
        border: 1px solid #fed7aa;
        border-radius: 14px;
        background: #fff7ed;
        color: #9a3412;
    }

    .field-notice-title {
        margin: 0;
        font-size: 11px;
        font-weight: 700;
    }

    .field-notice-description {
        margin: 5px 0 0;
        font-size: 9px;
        line-height: 1.6;
    }

    @media (min-width: 560px) {
        .field-statistics {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .field-logbook-item {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    @media (min-width: 1180px) {
        .field-statistics {
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
        }

        .field-dashboard-columns {
            grid-template-columns:
                minmax(0, 1.75fr)
                minmax(290px, 0.72fr);
        }
    }
</style>

<div class="field-dashboard">
    <section class="field-dashboard-heading">
        <h1 class="field-dashboard-title">
            Halo, {{ $supervisorName }} 👋
        </h1>

        <p class="field-dashboard-description">
            Berikut tugas dan data mahasiswa magang
            yang menjadi tanggung jawab Anda.
        </p>
    </section>

    @if (! $assignmentAvailable)
        <section class="field-notice">
            <span class="material-symbols-rounded">
                warning
            </span>

            <div>
                <p class="field-notice-title">
                    Penugasan Pembimbing Lapangan belum terdeteksi
                </p>

                <p class="field-notice-description">
                    Pastikan data magang memiliki Pembimbing Lapangan
                    atau terhubung dengan instansi yang sesuai.
                </p>
            </div>
        </section>
    @endif

    <section class="field-statistics">
        @foreach ($statisticCards as $statistic)
            <article class="field-statistic-card">
                <div
                    class="field-statistic-icon"
                    style="
                        background: {{ $statistic['background'] }};
                        color: {{ $statistic['color'] }};
                    "
                >
                    <span class="material-symbols-rounded">
                        {{ $statistic['icon'] }}
                    </span>
                </div>

                <div>
                    <p class="field-statistic-label">
                        {{ $statistic['label'] }}
                    </p>

                    <p class="field-statistic-value">
                        {{ $statistic['value'] }}
                    </p>

                    <p class="field-statistic-description">
                        {{ $statistic['description'] }}
                    </p>
                </div>
            </article>
        @endforeach
    </section>

    <section class="field-dashboard-columns">
        <div class="field-main-column">
            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Logbook Menunggu Validasi
                        </h2>

                        <p class="simmag-card-description">
                            Periksa aktivitas mahasiswa
                            dan berikan keputusan validasi.
                        </p>
                    </div>

                    <a
                        href="{{ route(
                            'field-supervisor.logbooks.index'
                        ) }}"
                        class="simmag-link-button"
                    >
                        Lihat Semua
                    </a>
                </div>

                @if ($pendingLogbooks->isNotEmpty())
                    <div class="field-logbook-list">
                        @foreach ($pendingLogbooks as $logbook)
                            @php
                                $initials = collect(
                                    preg_split(
                                        '/\s+/',
                                        trim(
                                            $logbook->student_name
                                                ?? 'Mahasiswa'
                                        )
                                    )
                                )
                                    ->filter()
                                    ->take(2)
                                    ->map(
                                        fn (string $word): string =>
                                            mb_strtoupper(
                                                mb_substr(
                                                    $word,
                                                    0,
                                                    1
                                                )
                                            )
                                    )
                                    ->implode('');

                                if ($initials === '') {
                                    $initials = 'MH';
                                }
                            @endphp

                            <div class="field-logbook-item">
                                <div class="field-logbook-content">
                                    <div class="field-avatar">
                                        {{ $initials }}
                                    </div>

                                    <div class="field-logbook-information">
                                        <p class="field-student-name">
                                            {{ $logbook->student_name
                                                ?? 'Mahasiswa' }}
                                        </p>

                                        <p class="field-student-identifier">
                                            {{ $logbook->student_identifier
                                                ?? '-' }}
                                            ·
                                            {{ \Carbon\Carbon::parse(
                                                $logbook->activity_date
                                            )->translatedFormat(
                                                'd F Y'
                                            ) }}
                                        </p>

                                        <p class="field-logbook-activity">
                                            {{ \Illuminate\Support\Str::limit(
                                                $logbook->activity,
                                                150
                                            ) }}
                                        </p>
                                    </div>
                                </div>

                                <a
                                    href="{{ route(
                                        'field-supervisor.logbooks.index',
                                        [
                                            'review' =>
                                                $logbook->id,
                                        ]
                                    ) }}"
                                    class="simmag-primary-button"
                                >
                                    <span class="material-symbols-rounded">
                                        fact_check
                                    </span>

                                    Validasi
                                </a>
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
                            Tidak ada logbook menunggu
                        </p>

                        <p class="simmag-empty-description">
                            Semua logbook terbaru sudah diproses.
                        </p>
                    </div>
                @endif
            </article>

            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Mahasiswa Magang
                        </h2>

                        <p class="simmag-card-description">
                            Mahasiswa yang terhubung dengan akun Anda.
                        </p>
                    </div>
                </div>

                <div class="simmag-card-body">
                    @if ($recentStudents->isNotEmpty())
                        <div class="field-student-list">
                            @foreach ($recentStudents as $student)
                                <div class="field-student-item">
                                    <div
                                        class="field-avatar"
                                        style="
                                            background:linear-gradient(
                                                135deg,
                                                #0ea5e9,
                                                #4f46e5
                                            );
                                        "
                                    >
                                        <span class="material-symbols-rounded">
                                            person
                                        </span>
                                    </div>

                                    <div class="field-student-content">
                                        <p class="field-student-name">
                                            {{ $student->student_name
                                                ?? 'Mahasiswa' }}
                                        </p>

                                        <p class="field-student-identifier">
                                            {{ $student->student_identifier
                                                ?? '-' }}
                                        </p>

                                        <div class="field-student-status">
                                            <span
                                                class="simmag-status {{ $statusClass(
                                                    $student->internship_status
                                                ) }}"
                                            >
                                                {{ str(
                                                    $student->internship_status
                                                        ?? 'Aktif'
                                                )
                                                    ->replace([
                                                        '_',
                                                        '-',
                                                    ], ' ')
                                                    ->title() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="simmag-empty-state">
                            <div class="simmag-empty-icon">
                                <span class="material-symbols-rounded">
                                    groups
                                </span>
                            </div>

                            <p class="simmag-empty-title">
                                Belum ada mahasiswa
                            </p>

                            <p class="simmag-empty-description">
                                Mahasiswa akan tampil setelah penugasan tersedia.
                            </p>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <aside class="field-side-column">
            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Pekerjaan Utama
                        </h2>

                        <p class="simmag-card-description">
                            Fitur yang saat ini sudah dapat digunakan.
                        </p>
                    </div>
                </div>

                <div class="simmag-card-body">
                    <a
                        href="{{ route(
                            'field-supervisor.logbooks.index'
                        ) }}"
                        class="field-primary-action"
                    >
                        <div class="field-primary-action-icon">
                            <span class="material-symbols-rounded">
                                fact_check
                            </span>
                        </div>

                        <div class="field-primary-action-content">
                            <p class="field-primary-action-title">
                                Validasi Logbook
                            </p>

                            <p class="field-primary-action-description">
                                {{ $statistics['logbooks_waiting'] }}
                                logbook menunggu diproses
                            </p>
                        </div>

                        <span class="material-symbols-rounded">
                            chevron_right
                        </span>
                    </a>
                </div>
            </article>

            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Alur Pembimbing Lapangan
                        </h2>

                        <p class="simmag-card-description">
                            Sesuai proses bisnis SIMMAG.
                        </p>
                    </div>
                </div>

                <div class="simmag-card-body">
                    <div
                        style="
                            display:grid;
                            gap:10px;
                        "
                    >
                        @foreach ([
                            [
                                'icon' => 'description',
                                'label' => 'Review Kerangka Acuan',
                            ],
                            [
                                'icon' => 'fact_check',
                                'label' => 'Validasi Logbook',
                            ],
                            [
                                'icon' => 'grading',
                                'label' => 'Penilaian Lapangan',
                            ],
                        ] as $flow)
                            <div
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:11px;
                                    padding:12px;
                                    border:1px solid #e5e7eb;
                                    border-radius:13px;
                                    background:#f8fafc;
                                "
                            >
                                <span
                                    class="material-symbols-rounded"
                                    style="color:#4f46e5;"
                                >
                                    {{ $flow['icon'] }}
                                </span>

                                <span
                                    style="
                                        color:#475569;
                                        font-size:10px;
                                        font-weight:600;
                                    "
                                >
                                    {{ $flow['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </article>
        </aside>
    </section>
</div>