@php
    $formatDate = function (mixed $value): string {
        if (blank($value)) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($value)
                ->translatedFormat('d F Y');
        } catch (\Throwable) {
            return '-';
        }
    };

    $formatDateTime = function (mixed $value): string {
        if (blank($value)) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($value)
                ->translatedFormat(
                    'd F Y, H.i'
                );
        } catch (\Throwable) {
            return '-';
        }
    };

    $normalizeStatus = function (
        ?string $status
    ): string {
        return str((string) $status)
            ->lower()
            ->replace([
                ' ',
                '-',
            ], '_')
            ->toString();
    };

    $formatStatus = function (
        ?string $status
    ): string {
        if (blank($status)) {
            return 'Belum Tersedia';
        }

        return str((string) $status)
            ->replace([
                '_',
                '-',
            ], ' ')
            ->lower()
            ->title()
            ->toString();
    };

    $statusClass = function (
        ?string $status
    ) use (
        $normalizeStatus
    ): string {
        $normalized =
            $normalizeStatus($status);

        if (
            str_contains(
                $normalized,
                'disetujui'
            )
            || str_contains(
                $normalized,
                'selesai'
            )
            || str_contains(
                $normalized,
                'ditanggapi'
            )
        ) {
            return 'is-success';
        }

        if (
            str_contains(
                $normalized,
                'revisi'
            )
            || str_contains(
                $normalized,
                'ditolak'
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

        return 'is-neutral';
    };

    $fieldCriteria = [
        'discipline_score' =>
            'Kedisiplinan',

        'initiative_score' =>
            'Inisiatif',

        'teamwork_score' =>
            'Kerja Sama',

        'communication_score' =>
            'Komunikasi',

        'adaptability_score' =>
            'Kemampuan Adaptasi',

        'diligence_score' =>
            'Ketekunan',

        'appearance_score' =>
            'Penampilan',

        'honesty_score' =>
            'Kejujuran',

        'critical_thinking_score' =>
            'Berpikir Kritis',

        'responsibility_score' =>
            'Tanggung Jawab',
    ];

    $lecturerCriteria = [
        'consistency_score' =>
            'Konsistensi',

        'logbook_completeness_score' =>
            'Kelengkapan Logbook',

        'neatness_score' =>
            'Kerapian',

        'content_completeness_score' =>
            'Kelengkapan Isi',

        'writing_flow_score' =>
            'Alur Penulisan',

        'grammar_score' =>
            'Tata Bahasa',
    ];

    $fieldScore =
        $fieldAssessment?->overall_score;

    $lecturerScore =
        $lecturerAssessment?->overall_score;

    $assessmentCompleted =
        $fieldAssessment !== null
        && $lecturerAssessment !== null
        && $fieldScore !== null
        && $lecturerScore !== null;

    $finalScore =
        $assessmentCompleted
            ? round(
                (
                    (float) $fieldScore
                    + (float) $lecturerScore
                ) / 2,
                2
            )
            : null;

    $finalReportStatus =
        $normalizeStatus(
            $finalReport?->status
        );

    $finalReportNeedsRevision =
        str_contains(
            $finalReportStatus,
            'revisi'
        );

    $canUploadFinalReport =
        ! $finalReport
        || $finalReportNeedsRevision;
@endphp

<style>
    .student-workflow-page {
        display: grid;
        gap: 20px;
    }

    .workflow-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 14px;
        font-size: 10px;
        font-weight: 700;
        line-height: 1.6;
    }

    .workflow-alert.is-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .workflow-alert.is-error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .workflow-alert
    .material-symbols-rounded {
        flex-shrink: 0;
        font-size: 20px;
    }

    .workflow-hero {
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        background:
            radial-gradient(
                circle at top right,
                rgba(99, 102, 241, 0.14),
                transparent 38%
            ),
            linear-gradient(
                135deg,
                #ffffff,
                #f8fafc
            );
        padding: 23px;
    }

    .workflow-hero-content {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .workflow-hero-icon {
        display: flex;
        width: 54px;
        height: 54px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 17px;
        background:
            linear-gradient(
                135deg,
                #2563eb,
                #7c3aed
            );
        color: #ffffff;
    }

    .workflow-eyebrow {
        margin: 0;
        color: #4f46e5;
        font-size: 8px;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .workflow-title {
        margin: 7px 0 0;
        color: #0f172a;
        font-family:
            "Plus Jakarta Sans",
            sans-serif;
        font-size: clamp(
            25px,
            4vw,
            36px
        );
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .workflow-description {
        max-width: 760px;
        margin: 8px 0 0;
        color: #64748b;
        font-size: 10px;
        line-height: 1.7;
    }

    .workflow-summary {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .workflow-summary-item {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .workflow-summary-item
    .material-symbols-rounded {
        color: #4f46e5;
        font-size: 20px;
    }

    .workflow-summary-label {
        margin: 0;
        color: #94a3b8;
        font-size: 7px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .workflow-summary-value {
        overflow: hidden;
        margin: 3px 0 0;
        color: #334155;
        font-size: 9px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .workflow-card {
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: #ffffff;
    }

    .workflow-card-header {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .workflow-card-heading {
        display: flex;
        align-items: flex-start;
        gap: 11px;
    }

    .workflow-card-icon {
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

    .workflow-card-title {
        margin: 0;
        color: #0f172a;
        font-size: 13px;
        font-weight: 800;
    }

    .workflow-card-description {
        margin: 5px 0 0;
        color: #94a3b8;
        font-size: 8px;
        line-height: 1.65;
    }

    .workflow-form {
        display: grid;
        gap: 17px;
        padding: 20px;
    }

    .workflow-form-grid {
        display: grid;
        gap: 15px;
    }

    .workflow-field {
        display: grid;
        gap: 7px;
    }

    .workflow-label {
        color: #334155;
        font-size: 9px;
        font-weight: 800;
    }

    .workflow-required {
        color: #dc2626;
    }

    .workflow-input,
    .workflow-textarea {
        width: 100%;
        border: 1px solid #dbe2ea;
        border-radius: 13px;
        background: #ffffff;
        color: #0f172a;
        outline: 0;
    }

    .workflow-input {
        min-height: 45px;
        padding: 10px 13px;
        font-size: 10px;
    }

    .workflow-textarea {
        min-height: 125px;
        resize: vertical;
        padding: 12px 13px;
        font-size: 10px;
        line-height: 1.7;
    }

    .workflow-input:focus,
    .workflow-textarea:focus {
        border-color: #818cf8;
        box-shadow:
            0 0 0 4px
            rgba(99, 102, 241, 0.1);
    }

    .workflow-help {
        margin: 0;
        color: #94a3b8;
        font-size: 8px;
        line-height: 1.5;
    }

    .workflow-error {
        margin: 0;
        color: #dc2626;
        font-size: 8px;
        font-weight: 700;
    }

    .workflow-form-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 9px;
    }

    .workflow-list {
        display: grid;
        gap: 12px;
        padding: 18px;
    }

    .workflow-item {
        padding: 17px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #ffffff;
    }

    .workflow-item-header {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .workflow-item-title {
        margin: 0;
        color: #1e293b;
        font-size: 11px;
        font-weight: 800;
    }

    .workflow-item-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px 12px;
        margin-top: 6px;
        color: #94a3b8;
        font-size: 8px;
    }

    .workflow-meta-part {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .workflow-meta-part
    .material-symbols-rounded {
        font-size: 14px;
    }

    .workflow-item-content {
        margin: 13px 0 0;
        color: #64748b;
        font-size: 9px;
        line-height: 1.75;
        white-space: pre-wrap;
    }

    .workflow-follow-up,
    .workflow-revision-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 14px;
        padding: 13px;
        border-radius: 13px;
    }

    .workflow-follow-up {
        border: 1px solid #c7d2fe;
        background: #eef2ff;
        color: #4338ca;
    }

    .workflow-revision-note {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .workflow-note-title {
        margin: 0;
        font-size: 8px;
        font-weight: 800;
    }

    .workflow-note-content {
        margin: 5px 0 0;
        font-size: 9px;
        line-height: 1.7;
        white-space: pre-wrap;
    }

    .workflow-item-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
    }

    .workflow-empty {
        display: flex;
        min-height: 240px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px 20px;
        text-align: center;
    }

    .workflow-empty-icon {
        display: flex;
        width: 58px;
        height: 58px;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #eef2ff;
        color: #4f46e5;
    }

    .workflow-empty-title {
        margin: 14px 0 0;
        color: #334155;
        font-size: 11px;
        font-weight: 800;
    }

    .workflow-empty-description {
        max-width: 430px;
        margin: 7px 0 0;
        color: #94a3b8;
        font-size: 9px;
        line-height: 1.7;
    }

    .workflow-report {
        display: grid;
        gap: 15px;
        padding: 20px;
    }

    .workflow-report-file {
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #f8fafc;
    }

    .workflow-report-info {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .workflow-report-icon {
        display: flex;
        width: 47px;
        height: 47px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #fef2f2;
        color: #dc2626;
    }

    .workflow-report-title {
        margin: 0;
        color: #1e293b;
        font-size: 11px;
        font-weight: 800;
    }

    .workflow-report-meta {
        margin: 5px 0 0;
        color: #94a3b8;
        font-size: 8px;
    }

    .assessment-final {
        border-radius: 22px;
        background:
            linear-gradient(
                135deg,
                #1d4ed8,
                #4f46e5,
                #7c3aed
            );
        padding: 24px;
        color: #ffffff;
    }

    .assessment-final-content {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .assessment-final-label {
        margin: 0;
        color: #dbeafe;
        font-size: 8px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .assessment-final-score {
        margin: 7px 0 0;
        font-size: clamp(
            35px,
            7vw,
            52px
        );
        font-weight: 800;
    }

    .assessment-final-description {
        margin: 5px 0 0;
        color: #dbeafe;
        font-size: 9px;
        line-height: 1.7;
    }

    .assessment-final-status {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 7px;
        padding: 8px 11px;
        border-radius: 999px;
        background:
            rgba(255, 255, 255, 0.14);
        font-size: 8px;
        font-weight: 800;
    }

    .assessment-grid {
        display: grid;
        gap: 15px;
    }

    .assessment-card {
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 19px;
        background: #ffffff;
    }

    .assessment-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px;
        border-bottom: 1px solid #f1f5f9;
    }

    .assessment-card-title {
        margin: 0;
        color: #1e293b;
        font-size: 10px;
        font-weight: 800;
    }

    .assessment-card-score {
        color: #4f46e5;
        font-size: 24px;
        font-weight: 800;
    }

    .assessment-criteria {
        display: grid;
        padding: 5px 18px 16px;
    }

    .assessment-criterion {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid #f1f5f9;
        color: #64748b;
        font-size: 9px;
    }

    .assessment-note {
        margin: 0 18px 18px;
        padding: 13px;
        border-radius: 13px;
        background: #f8faff;
        color: #64748b;
        font-size: 9px;
        line-height: 1.7;
        white-space: pre-wrap;
    }

    @media (min-width: 600px) {
        .workflow-form-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .workflow-summary {
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
        }

        .workflow-item-header {
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
        }

        .workflow-report-file {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    @media (min-width: 760px) {
        .workflow-card-header {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .assessment-final-content {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    @media (min-width: 900px) {
        .assessment-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="student-workflow-page">
    @if (session('success'))
        <div class="workflow-alert is-success">
            <span class="material-symbols-rounded">
                check_circle
            </span>

            <span>
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if (session('error'))
        <div class="workflow-alert is-error">
            <span class="material-symbols-rounded">
                error
            </span>

            <span>
                {{ session('error') }}
            </span>
        </div>
    @endif

    <section class="workflow-hero">
        <div class="workflow-hero-content">
            <div class="workflow-hero-icon">
                <span class="material-symbols-rounded">
                    @if ($module === 'consultations')
                        forum
                    @elseif ($module === 'final_reports')
                        description
                    @else
                        workspace_premium
                    @endif
                </span>
            </div>

            <div>
                <p class="workflow-eyebrow">
                    @if ($module === 'consultations')
                        Pendampingan Akademik
                    @elseif ($module === 'final_reports')
                        Tahap Akhir Magang
                    @else
                        Evaluasi Kegiatan Magang
                    @endif
                </p>

                <h1 class="workflow-title">
                    @if ($module === 'consultations')
                        Bimbingan
                    @elseif ($module === 'final_reports')
                        Laporan Akhir
                    @else
                        Hasil Penilaian
                    @endif
                </h1>

                <p class="workflow-description">
                    @if ($module === 'consultations')
                        Ajukan jadwal bimbingan,
                        sampaikan kendala, dan lihat
                        tindak lanjut dari Dosen Pembimbing.
                    @elseif ($module === 'final_reports')
                        Unggah Laporan Akhir dalam
                        format PDF untuk diperiksa
                        Dosen Pembimbing.
                    @else
                        Lihat hasil penilaian
                        Pembimbing Lapangan dan
                        Dosen Pembimbing.
                    @endif
                </p>
            </div>
        </div>
    </section>

    @if (! $internship)
        <section class="workflow-card">
            <div class="workflow-empty">
                <div class="workflow-empty-icon">
                    <span class="material-symbols-rounded">
                        work_off
                    </span>
                </div>

                <p class="workflow-empty-title">
                    Data magang belum tersedia
                </p>

                <p class="workflow-empty-description">
                    Hubungi Administrator untuk
                    menentukan periode, instansi,
                    dan pembimbing magang.
                </p>
            </div>
        </section>
    @else
        <section class="workflow-summary">
            <div class="workflow-summary-item">
                <span class="material-symbols-rounded">
                    person
                </span>

                <div>
                    <p class="workflow-summary-label">
                        Mahasiswa
                    </p>

                    <p class="workflow-summary-value">
                        {{ $internship->student?->name
                            ?? auth()->user()?->name
                            ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="workflow-summary-item">
                <span class="material-symbols-rounded">
                    apartment
                </span>

                <div>
                    <p class="workflow-summary-label">
                        Instansi
                    </p>

                    <p class="workflow-summary-value">
                        {{ $internship->company?->name
                            ?? 'Belum ditentukan' }}
                    </p>
                </div>
            </div>

            <div class="workflow-summary-item">
                <span class="material-symbols-rounded">
                    school
                </span>

                <div>
                    <p class="workflow-summary-label">
                        Dosen Pembimbing
                    </p>

                    <p class="workflow-summary-value">
                        {{ $internship
                            ->supervisorLecturer
                            ?->name
                            ?? 'Belum ditentukan' }}
                    </p>
                </div>
            </div>
        </section>

        @if ($module === 'consultations')
            <section class="workflow-card">
                <div class="workflow-card-header">
                    <div class="workflow-card-heading">
                        <div class="workflow-card-icon">
                            <span class="material-symbols-rounded">
                                add_comment
                            </span>
                        </div>

                        <div>
                            <h2 class="workflow-card-title">
                                Ajukan Bimbingan
                            </h2>

                            <p class="workflow-card-description">
                                Pengajuan akan dikirim
                                kepada Dosen Pembimbing.
                            </p>
                        </div>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route(
                        'student.consultations.store'
                    ) }}"
                    class="workflow-form"
                >
                    @csrf

                    <div class="workflow-form-grid">
                        <div class="workflow-field">
                            <label class="workflow-label">
                                Tanggal Bimbingan

                                <span class="workflow-required">
                                    *
                                </span>
                            </label>

                            <input
                                type="date"
                                name="consultation_date"
                                value="{{ old(
                                    'consultation_date',
                                    now()->format('Y-m-d')
                                ) }}"
                                class="workflow-input"
                            >

                            @error('consultation_date')
                                <p class="workflow-error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="workflow-field">
                            <label class="workflow-label">
                                Tautan Pertemuan
                            </label>

                            <input
                                type="url"
                                name="meeting_link"
                                value="{{ old(
                                    'meeting_link'
                                ) }}"
                                class="workflow-input"
                                placeholder="https://meet.google.com/..."
                            >

                            <p class="workflow-help">
                                Kosongkan apabila pertemuan
                                dilakukan secara langsung.
                            </p>

                            @error('meeting_link')
                                <p class="workflow-error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="workflow-field">
                        <label class="workflow-label">
                            Topik Bimbingan

                            <span class="workflow-required">
                                *
                            </span>
                        </label>

                        <input
                            type="text"
                            name="topic"
                            value="{{ old('topic') }}"
                            class="workflow-input"
                            placeholder="Contoh: Review Bab 3 Laporan Magang"
                        >

                        @error('topic')
                            <p class="workflow-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="workflow-field">
                        <label class="workflow-label">
                            Catatan atau Kendala

                            <span class="workflow-required">
                                *
                            </span>
                        </label>

                        <textarea
                            name="notes"
                            class="workflow-textarea"
                            placeholder="Jelaskan progres atau kendala yang ingin dibahas."
                        >{{ old('notes') }}</textarea>

                        @error('notes')
                            <p class="workflow-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="workflow-form-actions">
                        <button
                            type="reset"
                            class="simmag-secondary-button"
                        >
                            Bersihkan
                        </button>

                        <button
                            type="submit"
                            class="simmag-primary-button"
                        >
                            <span class="material-symbols-rounded">
                                send
                            </span>

                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </section>

            <section class="workflow-card">
                <div class="workflow-card-header">
                    <div class="workflow-card-heading">
                        <div class="workflow-card-icon">
                            <span class="material-symbols-rounded">
                                history
                            </span>
                        </div>

                        <div>
                            <h2 class="workflow-card-title">
                                Riwayat Bimbingan
                            </h2>

                            <p class="workflow-card-description">
                                Seluruh pengajuan dan
                                tindak lanjut bimbingan.
                            </p>
                        </div>
                    </div>

                    <span class="simmag-status is-primary">
                        {{ $consultations->count() }}
                        Bimbingan
                    </span>
                </div>

                @if ($consultations->isNotEmpty())
                    <div class="workflow-list">
                        @foreach ($consultations as $consultation)
                            <article class="workflow-item">
                                <div class="workflow-item-header">
                                    <div>
                                        <h3 class="workflow-item-title">
                                            {{ $consultation->topic }}
                                        </h3>

                                        <div class="workflow-item-meta">
                                            <span class="workflow-meta-part">
                                                <span class="material-symbols-rounded">
                                                    calendar_today
                                                </span>

                                                {{ $formatDate(
                                                    $consultation
                                                        ->consultation_date
                                                ) }}
                                            </span>

                                            <span class="workflow-meta-part">
                                                <span class="material-symbols-rounded">
                                                    school
                                                </span>

                                                {{ $consultation
                                                    ->lecturer
                                                    ?->name
                                                    ?? 'Dosen Pembimbing' }}
                                            </span>
                                        </div>
                                    </div>

                                    <span
                                        class="simmag-status {{ $statusClass(
                                            $consultation->status
                                        ) }}"
                                    >
                                        {{ $formatStatus(
                                            $consultation->status
                                        ) }}
                                    </span>
                                </div>

                                <p class="workflow-item-content">
                                    {{ $consultation->notes }}
                                </p>

                                @if (filled(
                                    $consultation->follow_up
                                ))
                                    <div class="workflow-follow-up">
                                        <span class="material-symbols-rounded">
                                            reply
                                        </span>

                                        <div>
                                            <p class="workflow-note-title">
                                                Tindak Lanjut Dosen
                                            </p>

                                            <p class="workflow-note-content">
                                                {{ $consultation
                                                    ->follow_up }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <div class="workflow-item-actions">
                                    @if (filled(
                                        $consultation
                                            ->meeting_link
                                    ))
                                        <a
                                            href="{{ $consultation
                                                ->meeting_link }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="simmag-primary-button"
                                        >
                                            <span class="material-symbols-rounded">
                                                video_call
                                            </span>

                                            Buka Pertemuan
                                        </a>
                                    @endif

                                    @if (
                                        $consultation->status
                                        === 'Diajukan'
                                    )
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'student.consultations.destroy',
                                                $consultation
                                            ) }}"
                                            onsubmit="return confirm('Hapus pengajuan Bimbingan ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="simmag-secondary-button"
                                            >
                                                <span class="material-symbols-rounded">
                                                    delete
                                                </span>

                                                Hapus Pengajuan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="workflow-empty">
                        <div class="workflow-empty-icon">
                            <span class="material-symbols-rounded">
                                forum
                            </span>
                        </div>

                        <p class="workflow-empty-title">
                            Belum ada bimbingan
                        </p>

                        <p class="workflow-empty-description">
                            Isi formulir di atas untuk
                            membuat pengajuan Bimbingan.
                        </p>
                    </div>
                @endif
            </section>
        @elseif ($module === 'final_reports')
            @if ($formOpen)
                <section class="workflow-card">
                    <div class="workflow-card-header">
                        <div class="workflow-card-heading">
                            <div class="workflow-card-icon">
                                <span class="material-symbols-rounded">
                                    upload_file
                                </span>
                            </div>

                            <div>
                                <h2 class="workflow-card-title">
                                    Unggah Laporan Akhir
                                </h2>

                                <p class="workflow-card-description">
                                    Format PDF maksimal 20 MB.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form
                        wire:submit="submitFinalReport"
                        class="workflow-form"
                    >
                        <div class="workflow-field">
                            <label class="workflow-label">
                                File Laporan PDF
                            </label>

                            <input
                                type="file"
                                wire:model="reportFile"
                                accept=".pdf,application/pdf"
                                class="workflow-input"
                            >

                            @error('reportFile')
                                <p class="workflow-error">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="workflow-form-actions">
                            <button
                                type="button"
                                wire:click="closeForm"
                                class="simmag-secondary-button"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                class="simmag-primary-button"
                            >
                                Kirim Laporan
                            </button>
                        </div>
                    </form>
                </section>
            @endif

            <section class="workflow-card">
                <div class="workflow-card-header">
                    <div class="workflow-card-heading">
                        <div class="workflow-card-icon">
                            <span class="material-symbols-rounded">
                                description
                            </span>
                        </div>

                        <div>
                            <h2 class="workflow-card-title">
                                Status Laporan Akhir
                            </h2>

                            <p class="workflow-card-description">
                                Laporan hanya dapat diganti
                                saat berstatus Perlu Revisi.
                            </p>
                        </div>
                    </div>

                    @if ($finalReport)
                        <span
                            class="simmag-status {{ $statusClass(
                                $finalReport->status
                            ) }}"
                        >
                            {{ $formatStatus(
                                $finalReport->status
                            ) }}
                        </span>
                    @endif
                </div>

                @if ($finalReport)
                    <div class="workflow-report">
                        <div class="workflow-report-file">
                            <div class="workflow-report-info">
                                <div class="workflow-report-icon">
                                    <span class="material-symbols-rounded">
                                        picture_as_pdf
                                    </span>
                                </div>

                                <div>
                                    <p class="workflow-report-title">
                                        Laporan Akhir Magang
                                    </p>

                                    <p class="workflow-report-meta">
                                        Dikirim
                                        {{ $formatDateTime(
                                            $finalReport
                                                ->created_at
                                        ) }}
                                    </p>
                                </div>
                            </div>

                            <div class="workflow-item-actions">
                                <a
                                    href="{{ \Illuminate\Support\Facades\Storage::url(
                                        $finalReport->file_path
                                    ) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="simmag-secondary-button"
                                >
                                    Lihat PDF
                                </a>

                                @if (
                                    $finalReportNeedsRevision
                                )
                                    <button
                                        type="button"
                                        wire:click="openForm"
                                        class="simmag-primary-button"
                                    >
                                        Unggah Revisi
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if (filled(
                            $finalReport->revision_notes
                        ))
                            <div class="workflow-revision-note">
                                <span class="material-symbols-rounded">
                                    rate_review
                                </span>

                                <div>
                                    <p class="workflow-note-title">
                                        Catatan Revisi Dosen
                                    </p>

                                    <p class="workflow-note-content">
                                        {{ $finalReport
                                            ->revision_notes }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="workflow-empty">
                        <div class="workflow-empty-icon">
                            <span class="material-symbols-rounded">
                                upload_file
                            </span>
                        </div>

                        <p class="workflow-empty-title">
                            Laporan belum diunggah
                        </p>

                        <p class="workflow-empty-description">
                            Unggah laporan setelah kegiatan
                            dan logbook selesai.
                        </p>

                        @if ($canUploadFinalReport)
                            <div style="margin-top: 15px;">
                                <button
                                    type="button"
                                    wire:click="openForm"
                                    class="simmag-primary-button"
                                >
                                    Unggah Laporan
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </section>
        @else
            <section class="assessment-final">
                <div class="assessment-final-content">
                    <div>
                        <p class="assessment-final-label">
                            Nilai Akhir Magang
                        </p>

                        <p class="assessment-final-score">
                            {{ $finalScore !== null
                                ? number_format(
                                    $finalScore,
                                    2,
                                    ',',
                                    '.'
                                )
                                : '-' }}
                        </p>

                        <p class="assessment-final-description">
                            Nilai akhir dihitung setelah
                            penilaian PL dan dosen tersedia.
                        </p>
                    </div>

                    <div class="assessment-final-status">
                        <span class="material-symbols-rounded">
                            {{ $assessmentCompleted
                                ? 'verified'
                                : 'hourglass_top' }}
                        </span>

                        {{ $assessmentCompleted
                            ? 'Penilaian Lengkap'
                            : 'Menunggu Penilaian' }}
                    </div>
                </div>
            </section>

            <section class="assessment-grid">
                <article class="assessment-card">
                    <div class="assessment-card-header">
                        <p class="assessment-card-title">
                            Pembimbing Lapangan
                        </p>

                        <span class="assessment-card-score">
                            {{ $fieldAssessment
                                ? number_format(
                                    (float) $fieldAssessment
                                        ->overall_score,
                                    2,
                                    ',',
                                    '.'
                                )
                                : '-' }}
                        </span>
                    </div>

                    @if ($fieldAssessment)
                        <div class="assessment-criteria">
                            @foreach (
                                $fieldCriteria
                                as $column => $label
                            )
                                <div class="assessment-criterion">
                                    <span>
                                        {{ $label }}
                                    </span>

                                    <strong>
                                        {{ $fieldAssessment
                                            ->{$column}
                                            ?? '-' }}
                                    </strong>
                                </div>
                            @endforeach
                        </div>

                        @if (filled(
                            $fieldAssessment->notes
                        ))
                            <div class="assessment-note">
                                {{ $fieldAssessment->notes }}
                            </div>
                        @endif
                    @else
                        <div class="workflow-empty">
                            <p class="workflow-empty-title">
                                Belum dinilai
                            </p>
                        </div>
                    @endif
                </article>

                <article class="assessment-card">
                    <div class="assessment-card-header">
                        <p class="assessment-card-title">
                            Dosen Pembimbing
                        </p>

                        <span class="assessment-card-score">
                            {{ $lecturerAssessment
                                ? number_format(
                                    (float) $lecturerAssessment
                                        ->overall_score,
                                    2,
                                    ',',
                                    '.'
                                )
                                : '-' }}
                        </span>
                    </div>

                    @if ($lecturerAssessment)
                        <div class="assessment-criteria">
                            @foreach (
                                $lecturerCriteria
                                as $column => $label
                            )
                                <div class="assessment-criterion">
                                    <span>
                                        {{ $label }}
                                    </span>

                                    <strong>
                                        {{ $lecturerAssessment
                                            ->{$column}
                                            ?? '-' }}
                                    </strong>
                                </div>
                            @endforeach
                        </div>

                        @if (filled(
                            $lecturerAssessment->notes
                        ))
                            <div class="assessment-note">
                                {{ $lecturerAssessment->notes }}
                            </div>
                        @endif
                    @else
                        <div class="workflow-empty">
                            <p class="workflow-empty-title">
                                Belum dinilai
                            </p>
                        </div>
                    @endif
                </article>
            </section>
        @endif
    @endif
</div>