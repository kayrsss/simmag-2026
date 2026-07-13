@php
    $formatDate = function (mixed $value): string {
        if (blank($value)) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($value)
                ->translatedFormat('d M Y');
        } catch (\Throwable) {
            return '-';
        }
    };

    $formatStatus = function (?string $status): string {
        return str((string) $status)
            ->replace([
                '_',
                '-',
            ], ' ')
            ->title()
            ->toString();
    };

    $statusClass = function (?string $status): string {
        $normalized = str((string) $status)
            ->lower()
            ->replace([
                ' ',
                '-',
            ], '_')
            ->toString();

        if (
            str_contains($normalized, 'setuju')
            || str_contains($normalized, 'valid')
            || str_contains($normalized, 'selesai')
            || str_contains($normalized, 'aktif')
        ) {
            return 'is-success';
        }

        if (
            str_contains($normalized, 'revisi')
            || str_contains($normalized, 'tolak')
            || str_contains($normalized, 'batal')
        ) {
            return 'is-danger';
        }

        if (
            str_contains($normalized, 'menunggu')
            || str_contains($normalized, 'diajukan')
            || str_contains($normalized, 'pending')
        ) {
            return 'is-warning';
        }

        return 'is-primary';
    };

    $initials = function (?string $name): string {
        $result = collect(
            preg_split(
                '/\s+/',
                trim($name ?: 'Mahasiswa')
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

        return $result !== ''
            ? $result
            : 'MH';
    };

    $frameworkRoute =
        \Illuminate\Support\Facades\Route::has(
            'lecturer.frameworks.index'
        )
            ? route('lecturer.frameworks.index')
            : null;

    $logbookRoute =
        \Illuminate\Support\Facades\Route::has(
            'lecturer.logbooks.index'
        )
            ? route('lecturer.logbooks.index')
            : null;

    $studentRoute =
        \Illuminate\Support\Facades\Route::has(
            'lecturer.students.index'
        )
            ? route('lecturer.students.index')
            : null;

    $consultationRoute =
        \Illuminate\Support\Facades\Route::has(
            'lecturer.consultations.index'
        )
            ? route('lecturer.consultations.index')
            : null;

    $finalReportRoute =
        \Illuminate\Support\Facades\Route::has(
            'lecturer.final-reports.index'
        )
            ? route('lecturer.final-reports.index')
            : null;

    $assessmentRoute =
        \Illuminate\Support\Facades\Route::has(
            'lecturer.assessments.index'
        )
            ? route('lecturer.assessments.index')
            : null;

    $statisticCards = [
        [
            'label' => 'Mahasiswa Bimbingan',
            'value' => $statistics['students'] ?? 0,
            'description' => 'Mahasiswa yang ditugaskan',
            'icon' => 'groups',
            'background' => '#eff6ff',
            'color' => '#2563eb',
        ],
        [
            'label' => 'Kerangka Acuan',
            'value' => $statistics['frameworks_waiting'] ?? 0,
            'description' => 'Menunggu review dosen',
            'icon' => 'description',
            'background' => '#f5f3ff',
            'color' => '#7c3aed',
        ],
        [
            'label' => 'Bimbingan',
            'value' => $statistics['consultations_waiting'] ?? 0,
            'description' => 'Menunggu tanggapan',
            'icon' => 'forum',
            'background' => '#fff7ed',
            'color' => '#ea580c',
        ],
        [
            'label' => 'Laporan Akhir',
            'value' => $statistics['final_reports_waiting'] ?? 0,
            'description' => 'Menunggu review',
            'icon' => 'draft',
            'background' => '#ecfeff',
            'color' => '#0891b2',
        ],
        [
            'label' => 'Penilaian Akademik',
            'value' => $statistics['assessments_pending'] ?? 0,
            'description' => 'Belum diselesaikan',
            'icon' => 'grading',
            'background' => '#ecfdf5',
            'color' => '#16a34a',
        ],
    ];
@endphp

<style>
    .lecturer-dashboard {
        display: grid;
        gap: 22px;
    }

    .lecturer-heading {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .lecturer-title {
        margin: 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: clamp(25px, 3vw, 32px);
        font-weight: 800;
        letter-spacing: -0.035em;
    }

    .lecturer-description {
        max-width: 780px;
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.75;
    }

    .lecturer-identity {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
        padding: 8px 11px;
        border: 1px solid #e5e7eb;
        border-radius: 11px;
        background: #ffffff;
        color: #64748b;
        font-size: 9px;
        font-weight: 700;
    }

    .lecturer-notice {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 15px;
        border: 1px solid #fed7aa;
        border-radius: 14px;
        background: #fff7ed;
        color: #9a3412;
    }

    .lecturer-notice-title {
        margin: 0;
        font-size: 11px;
        font-weight: 700;
    }

    .lecturer-notice-description {
        margin: 5px 0 0;
        font-size: 9px;
        line-height: 1.65;
    }

    .lecturer-statistics {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .lecturer-statistic-card {
        display: flex;
        min-height: 122px;
        align-items: center;
        gap: 15px;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.035);
    }

    .lecturer-statistic-icon {
        display: flex;
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
    }

    .lecturer-statistic-label {
        margin: 0;
        color: #64748b;
        font-size: 10px;
        font-weight: 600;
    }

    .lecturer-statistic-value {
        margin: 5px 0 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 24px;
        font-weight: 800;
    }

    .lecturer-statistic-description {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 9px;
    }

    .lecturer-grid {
        display: grid;
        gap: 18px;
    }

    .lecturer-main,
    .lecturer-side {
        display: grid;
        align-content: start;
        gap: 18px;
    }

    .lecturer-list {
        display: grid;
    }

    .lecturer-list-item {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 16px 20px;
        border-top: 1px solid #f1f5f9;
    }

    .lecturer-list-item:first-child {
        border-top: 0;
    }

    .lecturer-item-content {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 12px;
    }

    .lecturer-avatar {
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

    .lecturer-item-information {
        min-width: 0;
        flex: 1;
    }

    .lecturer-item-name {
        margin: 0;
        color: #1e293b;
        font-size: 11px;
        font-weight: 700;
    }

    .lecturer-item-meta {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 8px;
        line-height: 1.5;
    }

    .lecturer-item-title {
        margin: 8px 0 0;
        color: #475569;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.65;
    }

    .lecturer-item-description {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 10px;
        line-height: 1.7;
    }

    .lecturer-item-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 9px;
    }

    .lecturer-student-list {
        display: grid;
    }

    .lecturer-student-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .lecturer-student-item:last-child {
        border-bottom: 0;
    }

    .lecturer-student-information {
        min-width: 0;
        flex: 1;
    }

    .lecturer-action-list {
        display: grid;
        gap: 10px;
    }

    .lecturer-action {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
        transition:
            border-color 160ms ease,
            background 160ms ease,
            transform 160ms ease;
    }

    .lecturer-action:hover {
        border-color: #c7d2fe;
        background: #eef2ff;
        transform: translateY(-1px);
    }

    .lecturer-action-icon {
        display: flex;
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        background: #eef2ff;
        color: #4f46e5;
    }

    .lecturer-action-information {
        min-width: 0;
        flex: 1;
    }

    .lecturer-action-title {
        margin: 0;
        color: #334155;
        font-size: 10px;
        font-weight: 700;
    }

    .lecturer-action-description {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 8px;
    }

    @media (min-width: 560px) {
        .lecturer-statistics {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .lecturer-list-item {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    @media (min-width: 950px) {
        .lecturer-statistics {
            grid-template-columns:
                repeat(5, minmax(0, 1fr));
        }
    }

    @media (min-width: 1180px) {
        .lecturer-grid {
            grid-template-columns:
                minmax(0, 1.7fr)
                minmax(290px, 0.72fr);
        }
    }
</style>

<div class="lecturer-dashboard">
    <section class="lecturer-heading">
        <h1 class="lecturer-title">
            Halo, {{ $lecturerName }} 👋
        </h1>

        <p class="lecturer-description">
            Pantau Kerangka Acuan, logbook, bimbingan,
            Laporan Akhir, dan penilaian akademik mahasiswa
            yang menjadi tanggung jawab Anda.
        </p>

        <span class="lecturer-identity">
            <span class="material-symbols-rounded">
                badge
            </span>

            NIDN/NIP:
            {{ $lecturerIdentifier }}
        </span>
    </section>

    @if (! $assignmentAvailable)
        <section class="lecturer-notice">
            <span class="material-symbols-rounded">
                warning
            </span>

            <div>
                <p class="lecturer-notice-title">
                    Kolom penugasan dosen belum tersedia
                </p>

                <p class="lecturer-notice-description">
                    Tabel magang harus memiliki kolom
                    supervisor_lecturer_id.
                </p>
            </div>
        </section>
    @elseif (! $hasAssignments)
        <section class="lecturer-notice">
            <span class="material-symbols-rounded">
                assignment_late
            </span>

            <div>
                <p class="lecturer-notice-title">
                    Belum ada mahasiswa bimbingan
                </p>

                <p class="lecturer-notice-description">
                    Admin belum menugaskan mahasiswa magang
                    kepada akun dosen ini.
                </p>
            </div>
        </section>
    @endif

    <section class="lecturer-statistics">
        @foreach ($statisticCards as $statistic)
            <article class="lecturer-statistic-card">
                <div
                    class="lecturer-statistic-icon"
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
                    <p class="lecturer-statistic-label">
                        {{ $statistic['label'] }}
                    </p>

                    <p class="lecturer-statistic-value">
                        {{ $statistic['value'] }}
                    </p>

                    <p class="lecturer-statistic-description">
                        {{ $statistic['description'] }}
                    </p>
                </div>
            </article>
        @endforeach
    </section>

    <section class="lecturer-grid">
        <div class="lecturer-main">
            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Kerangka Acuan Menunggu Review
                        </h2>

                        <p class="simmag-card-description">
                            Dokumen mahasiswa yang sudah masuk
                            ke tahap review Dosen Pembimbing.
                        </p>
                    </div>

                    @if ($frameworkRoute)
                        <a
                            href="{{ $frameworkRoute }}"
                            class="simmag-link-button"
                        >
                            Lihat Semua
                        </a>
                    @endif
                </div>

                @if ($pendingFrameworks->isNotEmpty())
                    <div class="lecturer-list">
                        @foreach ($pendingFrameworks as $framework)
                            <div class="lecturer-list-item">
                                <div class="lecturer-item-content">
                                    <div class="lecturer-avatar">
                                        {{ $initials(
                                            $framework->student_name
                                        ) }}
                                    </div>

                                    <div class="lecturer-item-information">
                                        <p class="lecturer-item-name">
                                            {{ $framework->student_name
                                                ?? 'Mahasiswa' }}
                                        </p>

                                        <p class="lecturer-item-meta">
                                            {{ $framework->student_identifier
                                                ?? '-' }}

                                            ·

                                            {{ $framework->company_name
                                                ?? '-' }}

                                            ·

                                            {{ $formatDate(
                                                $framework->submitted_at
                                            ) }}
                                        </p>

                                        <p class="lecturer-item-title">
                                            {{ $framework->framework_title
                                                ?? 'Kerangka Acuan Magang' }}
                                        </p>

                                        <div class="lecturer-item-footer">
                                            @if (
                                                filled(
                                                    $framework->framework_version
                                                        ?? null
                                                )
                                            )
                                                <span class="simmag-status is-primary">
                                                    Versi
                                                    {{ $framework->framework_version }}
                                                </span>
                                            @endif

                                            <span
                                                class="simmag-status {{ $statusClass(
                                                    $framework->framework_status
                                                ) }}"
                                            >
                                                {{ $formatStatus(
                                                    $framework->framework_status
                                                ) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
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
                            Tidak ada Kerangka Acuan menunggu
                        </p>

                        <p class="simmag-empty-description">
                            Belum ada dokumen mahasiswa yang perlu
                            direview oleh Dosen Pembimbing.
                        </p>
                    </div>
                @endif
            </article>

            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Logbook Terbaru
                        </h2>

                        <p class="simmag-card-description">
                            Monitoring aktivitas terbaru mahasiswa
                            bimbingan secara baca saja.
                        </p>
                    </div>

                    @if ($logbookRoute)
                        <a
                            href="{{ $logbookRoute }}"
                            class="simmag-link-button"
                        >
                            Lihat Semua
                        </a>
                    @endif
                </div>

                @if ($recentLogbooks->isNotEmpty())
                    <div class="lecturer-list">
                        @foreach ($recentLogbooks as $logbook)
                            <div class="lecturer-list-item">
                                <div class="lecturer-item-content">
                                    <div class="lecturer-avatar">
                                        {{ $initials(
                                            $logbook->student_name
                                        ) }}
                                    </div>

                                    <div class="lecturer-item-information">
                                        <p class="lecturer-item-name">
                                            {{ $logbook->student_name
                                                ?? 'Mahasiswa' }}
                                        </p>

                                        <p class="lecturer-item-meta">
                                            {{ $logbook->student_identifier
                                                ?? '-' }}

                                            ·

                                            {{ $formatDate(
                                                $logbook->activity_date
                                            ) }}
                                        </p>

                                        <p class="lecturer-item-description">
                                            {{ \Illuminate\Support\Str::limit(
                                                $logbook->activity
                                                    ?? 'Aktivitas magang',
                                                160
                                            ) }}
                                        </p>

                                        <div class="lecturer-item-footer">
                                            <span
                                                class="simmag-status {{ $statusClass(
                                                    $logbook->status
                                                ) }}"
                                            >
                                                {{ $formatStatus(
                                                    $logbook->status
                                                ) }}
                                            </span>

                                            @if (
                                                filled(
                                                    $logbook->evidence_name
                                                        ?? null
                                                )
                                            )
                                                <span class="simmag-status is-primary">
                                                    Ada Bukti
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
                            Aktivitas mahasiswa akan tampil setelah
                            mahasiswa mengisi logbook.
                        </p>
                    </div>
                @endif
            </article>
        </div>

        <aside class="lecturer-side">
            <article class="simmag-card">
                <div class="simmag-card-header">
                    <div>
                        <h2 class="simmag-card-title">
                            Mahasiswa Bimbingan
                        </h2>

                        <p class="simmag-card-description">
                            Mahasiswa yang ditugaskan ke akun Anda.
                        </p>
                    </div>

                    @if ($studentRoute)
                        <a
                            href="{{ $studentRoute }}"
                            class="simmag-link-button"
                        >
                            Lihat Semua
                        </a>
                    @endif
                </div>

                <div class="simmag-card-body">
                    @if ($recentStudents->isNotEmpty())
                        <div class="lecturer-student-list">
                            @foreach ($recentStudents as $student)
                                <div class="lecturer-student-item">
                                    <div class="lecturer-avatar">
                                        {{ $initials(
                                            $student->student_name
                                        ) }}
                                    </div>

                                    <div class="lecturer-student-information">
                                        <p class="lecturer-item-name">
                                            {{ $student->student_name
                                                ?? 'Mahasiswa' }}
                                        </p>

                                        <p class="lecturer-item-meta">
                                            {{ $student->student_identifier
                                                ?? '-' }}

                                            ·

                                            {{ $student->company_name
                                                ?? '-' }}
                                        </p>

                                        <div class="lecturer-item-footer">
                                            <span
                                                class="simmag-status {{ $statusClass(
                                                    $student->internship_status
                                                ) }}"
                                            >
                                                {{ $formatStatus(
                                                    $student->internship_status
                                                ) }}
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
                                Data mahasiswa muncul setelah admin
                                melakukan penugasan dosen.
                            </p>
                        </div>
                    @endif
                </div>
            </article>

            @if (
                $frameworkRoute
                || $logbookRoute
                || $consultationRoute
                || $finalReportRoute
                || $assessmentRoute
            )
                <article class="simmag-card">
                    <div class="simmag-card-header">
                        <div>
                            <h2 class="simmag-card-title">
                                Pekerjaan Utama
                            </h2>

                            <p class="simmag-card-description">
                                Fitur yang sudah tersedia untuk dosen.
                            </p>
                        </div>
                    </div>

                    <div class="simmag-card-body">
                        <div class="lecturer-action-list">
                            @if ($frameworkRoute)
                                <a
                                    href="{{ $frameworkRoute }}"
                                    class="lecturer-action"
                                >
                                    <div class="lecturer-action-icon">
                                        <span class="material-symbols-rounded">
                                            description
                                        </span>
                                    </div>

                                    <div class="lecturer-action-information">
                                        <p class="lecturer-action-title">
                                            Review Kerangka Acuan
                                        </p>

                                        <p class="lecturer-action-description">
                                            {{ $statistics['frameworks_waiting'] ?? 0 }}
                                            dokumen menunggu
                                        </p>
                                    </div>

                                    <span class="material-symbols-rounded">
                                        chevron_right
                                    </span>
                                </a>
                            @endif

                            @if ($consultationRoute)
                                <a
                                    href="{{ $consultationRoute }}"
                                    class="lecturer-action"
                                >
                                    <div class="lecturer-action-icon">
                                        <span class="material-symbols-rounded">
                                            forum
                                        </span>
                                    </div>

                                    <div class="lecturer-action-information">
                                        <p class="lecturer-action-title">
                                            Bimbingan
                                        </p>

                                        <p class="lecturer-action-description">
                                            {{ $statistics['consultations_waiting'] ?? 0 }}
                                            menunggu tanggapan
                                        </p>
                                    </div>

                                    <span class="material-symbols-rounded">
                                        chevron_right
                                    </span>
                                </a>
                            @endif

                            @if ($finalReportRoute)
                                <a
                                    href="{{ $finalReportRoute }}"
                                    class="lecturer-action"
                                >
                                    <div class="lecturer-action-icon">
                                        <span class="material-symbols-rounded">
                                            draft
                                        </span>
                                    </div>

                                    <div class="lecturer-action-information">
                                        <p class="lecturer-action-title">
                                            Review Laporan Akhir
                                        </p>

                                        <p class="lecturer-action-description">
                                            {{ $statistics['final_reports_waiting'] ?? 0 }}
                                            laporan menunggu
                                        </p>
                                    </div>

                                    <span class="material-symbols-rounded">
                                        chevron_right
                                    </span>
                                </a>
                            @endif

                            @if ($assessmentRoute)
                                <a
                                    href="{{ $assessmentRoute }}"
                                    class="lecturer-action"
                                >
                                    <div class="lecturer-action-icon">
                                        <span class="material-symbols-rounded">
                                            grading
                                        </span>
                                    </div>

                                    <div class="lecturer-action-information">
                                        <p class="lecturer-action-title">
                                            Penilaian Akademik
                                        </p>

                                        <p class="lecturer-action-description">
                                            {{ $statistics['assessments_pending'] ?? 0 }}
                                            belum diselesaikan
                                        </p>
                                    </div>

                                    <span class="material-symbols-rounded">
                                        chevron_right
                                    </span>
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endif
        </aside>
    </section>
</div>