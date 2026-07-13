@php
    $formatStatus = fn (?string $status): string =>
        str((string) $status)
            ->replace([
                '_',
                '-',
            ], ' ')
            ->title()
            ->toString();

    $formatDate = function (mixed $value): string {
        if (blank($value)) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse(
                $value
            )->translatedFormat(
                'd F Y'
            );
        } catch (\Throwable) {
            return '-';
        }
    };

    $criteria = [
        'discipline_score' => 'Kedisiplinan',
        'initiative_score' => 'Inisiatif',
        'teamwork_score' => 'Kerja Sama',
        'communication_score' => 'Komunikasi',
        'adaptability_score' => 'Kemampuan Adaptasi',
        'diligence_score' => 'Ketekunan',
        'appearance_score' => 'Penampilan',
        'honesty_score' => 'Kejujuran',
        'critical_thinking_score' => 'Berpikir Kritis',
        'responsibility_score' => 'Tanggung Jawab',
    ];
@endphp

<style>
    .field-workflow-page {
        display: grid;
        gap: 22px;
    }

    .field-workflow-heading {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .field-workflow-title {
        margin: 0;
        color: #0f172a;
        font-family:
            "Plus Jakarta Sans",
            sans-serif;
        font-size: clamp(26px, 4vw, 34px);
        font-weight: 800;
        letter-spacing: -0.035em;
    }

    .field-workflow-description {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.75;
    }

    .field-workflow-toolbar {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .field-workflow-input,
    .field-workflow-select,
    .field-workflow-textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 13px;
        background: #f8fafc;
        padding: 12px 13px;
        color: #334155;
        font-size: 11px;
        outline: 0;
    }

    .field-workflow-textarea {
        min-height: 120px;
        resize: vertical;
        line-height: 1.7;
    }

    .field-workflow-list {
        display: grid;
    }

    .field-workflow-item {
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding: 19px 20px;
        border-top: 1px solid #f1f5f9;
    }

    .field-workflow-item:first-child {
        border-top: 0;
    }

    .field-workflow-name {
        margin: 0;
        color: #1e293b;
        font-size: 13px;
        font-weight: 800;
    }

    .field-workflow-meta {
        margin: 6px 0 0;
        color: #94a3b8;
        font-size: 9px;
        line-height: 1.6;
    }

    .field-workflow-description-text {
        margin: 10px 0 0;
        color: #64748b;
        font-size: 10px;
        line-height: 1.7;
    }

    .field-workflow-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .field-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
        padding: 20px;
        background: rgba(15, 23, 42, 0.58);
        backdrop-filter: blur(5px);
    }

    .field-modal {
        width: 100%;
        max-width: 780px;
        margin: auto;
        overflow: hidden;
        border-radius: 22px;
        background: #ffffff;
        box-shadow:
            0 25px 80px
            rgba(15, 23, 42, 0.28);
    }

    .field-modal-header,
    .field-modal-footer {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .field-modal-footer {
        border-top: 1px solid #f1f5f9;
        border-bottom: 0;
    }

    .field-modal-body {
        display: grid;
        gap: 16px;
        max-height: 68vh;
        overflow-y: auto;
        padding: 20px;
    }

    .field-detail {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        padding: 14px;
    }

    .field-detail-label {
        margin: 0 0 7px;
        color: #64748b;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .field-detail-value {
        margin: 0;
        color: #475569;
        font-size: 10px;
        line-height: 1.75;
        white-space: pre-line;
    }

    .field-score-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    @media (min-width: 640px) {
        .field-workflow-heading,
        .field-workflow-toolbar,
        .field-workflow-item {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .field-workflow-input {
            max-width: 330px;
        }

        .field-workflow-select {
            max-width: 210px;
        }

        .field-modal-header,
        .field-modal-footer {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .field-score-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="field-workflow-page">
    <section class="field-workflow-heading">
        <div>
            <h1 class="field-workflow-title">
                @if ($module === 'students')
                    Daftar Mahasiswa
                @elseif ($module === 'frameworks')
                    Review Kerangka Acuan
                @else
                    Penilaian Lapangan
                @endif
            </h1>

            <p class="field-workflow-description">
                Data hanya menampilkan mahasiswa yang ditugaskan
                kepada akun Pembimbing Lapangan ini.
            </p>
        </div>
    </section>

    <section class="simmag-card">
        <div class="field-workflow-toolbar">
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                class="field-workflow-input"
                placeholder="Cari mahasiswa, NIM, judul, atau instansi..."
            >

            <select
                wire:model.live="statusFilter"
                class="field-workflow-select"
            >
                <option value="all">
                    Semua Status
                </option>

                @if ($module === 'frameworks')
                    <option value="Menunggu_Review">
                        Menunggu Review
                    </option>

                    <option value="Disetujui_PL">
                        Disetujui PL
                    </option>

                    <option value="Perlu_Revisi">
                        Perlu Revisi
                    </option>

                    <option value="Disetujui">
                        Disetujui Dosen
                    </option>
                @else
                    <option value="Draft">
                        Draft
                    </option>

                    <option value="aktif">
                        Aktif
                    </option>

                    <option value="selesai">
                        Selesai
                    </option>
                @endif
            </select>
        </div>

        @if ($records->isNotEmpty())
            <div class="field-workflow-list">
                @foreach ($records as $record)
                    <article class="field-workflow-item">
                        <div>
                            @if ($module === 'frameworks')
                                <h2 class="field-workflow-name">
                                    {{ $record->internship?->student?->name
                                        ?? 'Mahasiswa' }}
                                </h2>

                                <p class="field-workflow-meta">
                                    {{ $record->internship?->student?->nim
                                        ?? $record->internship?->student?->identifier
                                        ?? '-' }}

                                    ·

                                    {{ $record->internship?->company?->name
                                        ?? '-' }}

                                    · Versi {{ $record->version }}
                                </p>

                                <p class="field-workflow-description-text">
                                    {{ $record->title }}
                                </p>

                                <div
                                    class="field-workflow-actions"
                                    style="margin-top:12px;"
                                >
                                    <span class="simmag-status is-primary">
                                        {{ $formatStatus(
                                            $record->status
                                        ) }}
                                    </span>
                                </div>
                            @else
                                <h2 class="field-workflow-name">
                                    {{ $record->student?->name
                                        ?? 'Mahasiswa' }}
                                </h2>

                                <p class="field-workflow-meta">
                                    {{ $record->student?->nim
                                        ?? $record->student?->identifier
                                        ?? '-' }}

                                    ·

                                    {{ $record->company?->name
                                        ?? '-' }}

                                    ·

                                    {{ $record->period?->name
                                        ?? '-' }}
                                </p>

                                <div
                                    class="field-workflow-actions"
                                    style="margin-top:12px;"
                                >
                                    <span class="simmag-status is-primary">
                                        {{ $formatStatus(
                                            $record->status
                                        ) }}
                                    </span>

                                    @if (
                                        $module === 'assessments'
                                        && $record->fieldAssessment
                                    )
                                        <span class="simmag-status is-success">
                                            Nilai:
                                            {{ number_format(
                                                (float) $record
                                                    ->fieldAssessment
                                                    ->overall_score,
                                                2
                                            ) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="field-workflow-actions">
                            @if ($module === 'students')
                                @if (
                                    Route::has(
                                        'field-supervisor.frameworks.index'
                                    )
                                )
                                    <a
                                        href="{{ route(
                                            'field-supervisor.frameworks.index'
                                        ) }}"
                                        class="simmag-secondary-button"
                                    >
                                        Kerangka Acuan
                                    </a>
                                @endif

                                @if (
                                    Route::has(
                                        'field-supervisor.logbooks.index'
                                    )
                                )
                                    <a
                                        href="{{ route(
                                            'field-supervisor.logbooks.index'
                                        ) }}"
                                        class="simmag-secondary-button"
                                    >
                                        Logbook
                                    </a>
                                @endif
                            @elseif ($module === 'frameworks')
                                <button
                                    wire:click="openFramework({{ $record->id }})"
                                    type="button"
                                    class="simmag-primary-button"
                                >
                                    Review
                                </button>
                            @else
                                <button
                                    wire:click="openAssessment({{ $record->id }})"
                                    type="button"
                                    class="simmag-primary-button"
                                >
                                    Isi Penilaian
                                </button>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($records->hasPages())
                <div style="padding:18px 20px;">
                    {{ $records->links() }}
                </div>
            @endif
        @else
            <div class="simmag-empty-state">
                <div class="simmag-empty-icon">
                    <span class="material-symbols-rounded">
                        groups
                    </span>
                </div>

                <p class="simmag-empty-title">
                    Data belum tersedia
                </p>

                <p class="simmag-empty-description">
                    Pastikan mahasiswa sudah ditugaskan kepada
                    akun Pembimbing Lapangan ini.
                </p>
            </div>
        @endif
    </section>

    @if (
        $modalOpen
        && $module === 'frameworks'
        && $selectedFramework
    )
        <div
            wire:click.self="closeModal"
            class="field-modal-overlay"
        >
            <div class="field-modal">
                <div class="field-modal-header">
                    <div>
                        <h2 class="field-workflow-name">
                            {{ $selectedFramework->title }}
                        </h2>

                        <p class="field-workflow-meta">
                            {{ $selectedFramework->internship?->student?->name }}

                            · Versi
                            {{ $selectedFramework->version }}
                        </p>
                    </div>

                    <button
                        wire:click="closeModal"
                        type="button"
                        class="simmag-icon-button"
                    >
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>
                </div>

                <div class="field-modal-body">
                    <div class="field-detail">
                        <p class="field-detail-label">
                            Deskripsi
                        </p>

                        <p class="field-detail-value">
                            {{ $selectedFramework->description
                                ?? '-' }}
                        </p>
                    </div>

                    <div class="field-detail">
                        <p class="field-detail-label">
                            Rencana Kerja
                        </p>

                        <p class="field-detail-value">
                            {{ $selectedFramework->work_plan
                                ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="field-detail-label">
                            Catatan Pembimbing Lapangan
                        </label>

                        <textarea
                            wire:model="reviewNotes"
                            class="field-workflow-textarea"
                            placeholder="Isi catatan persetujuan atau revisi..."
                        ></textarea>

                        @error('reviewNotes')
                            <p class="simmag-flash is-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="field-modal-footer">
                    <button
                        wire:click="closeModal"
                        type="button"
                        class="simmag-secondary-button"
                    >
                        Tutup
                    </button>

                    @if (
                        $selectedFramework->status
                        === 'Menunggu_Review'
                    )
                        <div class="field-workflow-actions">
                            <button
                                wire:click="requestFrameworkRevision"
                                type="button"
                                class="simmag-secondary-button"
                            >
                                Perlu Revisi
                            </button>

                            <button
                                wire:click="approveFramework"
                                type="button"
                                class="simmag-primary-button"
                            >
                                Setujui
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if (
        $modalOpen
        && $module === 'assessments'
        && $selectedInternship
    )
        <div
            wire:click.self="closeModal"
            class="field-modal-overlay"
        >
            <div class="field-modal">
                <div class="field-modal-header">
                    <div>
                        <h2 class="field-workflow-name">
                            Penilaian
                            {{ $selectedInternship->student?->name }}
                        </h2>

                        <p class="field-workflow-meta">
                            Setiap kriteria memiliki nilai 0 sampai 100.
                        </p>
                    </div>

                    <button
                        wire:click="closeModal"
                        type="button"
                        class="simmag-icon-button"
                    >
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>
                </div>

                <div class="field-modal-body">
                    <div class="field-score-grid">
                        @foreach ($criteria as $column => $label)
                            <div>
                                <label class="field-detail-label">
                                    {{ $label }}
                                </label>

                                <input
                                    wire:model="scores.{{ $column }}"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="field-workflow-input"
                                >

                                @error("scores.{$column}")
                                    <p class="simmag-flash is-error">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <label class="field-detail-label">
                            Catatan Penilaian
                        </label>

                        <textarea
                            wire:model="assessmentNotes"
                            class="field-workflow-textarea"
                            placeholder="Catatan mengenai kinerja mahasiswa..."
                        ></textarea>
                    </div>
                </div>

                <div class="field-modal-footer">
                    <button
                        wire:click="closeModal"
                        type="button"
                        class="simmag-secondary-button"
                    >
                        Tutup
                    </button>

                    <button
                        wire:click="saveAssessment"
                        wire:loading.attr="disabled"
                        type="button"
                        class="simmag-primary-button"
                    >
                        Simpan Penilaian
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>