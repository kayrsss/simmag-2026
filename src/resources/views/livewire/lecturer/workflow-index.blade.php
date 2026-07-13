@php
    $formatStatus = function (?string $status): string {
        return str((string) $status)
            ->replace([
                '_',
                '-',
            ], ' ')
            ->title()
            ->toString();
    };

    $normalizeStatus = function (?string $status): string {
        return str((string) $status)
            ->lower()
            ->replace([
                ' ',
                '-',
            ], '_')
            ->toString();
    };

    $statusClass = function (?string $status) use (
        $normalizeStatus
    ): string {
        $normalized =
            $normalizeStatus($status);

        if (
            str_contains($normalized, 'selesai')
            || str_contains($normalized, 'disetujui')
            || str_contains($normalized, 'tervalidasi')
            || str_contains($normalized, 'submitted')
        ) {
            return 'is-success';
        }

        if (
            str_contains($normalized, 'revisi')
            || str_contains($normalized, 'ditolak')
        ) {
            return 'is-danger';
        }

        if (
            str_contains($normalized, 'menunggu')
            || str_contains($normalized, 'pending')
            || str_contains($normalized, 'diajukan')
            || str_contains($normalized, 'belum')
        ) {
            return 'is-warning';
        }

        return 'is-neutral';
    };

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

    $cards = [
        [
            'label' => 'Total Data',
            'value' => $statistics['total'] ?? 0,
            'icon' => $config['icon'],
            'background' => '#eff6ff',
            'color' => '#2563eb',
        ],
        [
            'label' => 'Menunggu',
            'value' => $statistics['waiting'] ?? 0,
            'icon' => 'pending_actions',
            'background' => '#fff7ed',
            'color' => '#ea580c',
        ],
        [
            'label' => 'Selesai',
            'value' => $statistics['completed'] ?? 0,
            'icon' => 'task_alt',
            'background' => '#ecfdf5',
            'color' => '#16a34a',
        ],
        [
            'label' => 'Perlu Revisi',
            'value' => $statistics['revision'] ?? 0,
            'icon' => 'edit_document',
            'background' => '#fef2f2',
            'color' => '#dc2626',
        ],
    ];
@endphp

<style>
    .workflow-page {
        display: grid;
        gap: 22px;
    }

    .workflow-heading-row {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .workflow-title {
        margin: 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: clamp(25px, 3vw, 32px);
        font-weight: 800;
        letter-spacing: -0.035em;
    }

    .workflow-description {
        max-width: 760px;
        margin: 7px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.75;
    }

    .workflow-statistics {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .workflow-statistic {
        display: flex;
        min-height: 112px;
        align-items: center;
        gap: 15px;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.035);
    }

    .workflow-statistic-icon {
        display: flex;
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
    }

    .workflow-statistic-label {
        margin: 0;
        color: #64748b;
        font-size: 10px;
        font-weight: 600;
    }

    .workflow-statistic-value {
        margin: 5px 0 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 24px;
        font-weight: 800;
    }

    .workflow-toolbar {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .workflow-search {
        display: flex;
        min-height: 42px;
        align-items: center;
        gap: 9px;
        padding: 0 13px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
    }

    .workflow-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: #334155;
        font-size: 11px;
    }

    .workflow-filter {
        min-height: 42px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        padding: 0 12px;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
    }

    .workflow-list {
        display: grid;
    }

    .workflow-item {
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding: 18px 20px;
        border-top: 1px solid #f1f5f9;
    }

    .workflow-item:first-child {
        border-top: 0;
    }

    .workflow-profile {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 13px;
    }

    .workflow-avatar {
        display: flex;
        width: 44px;
        height: 44px;
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

    .workflow-information {
        min-width: 0;
        flex: 1;
    }

    .workflow-student {
        margin: 0;
        color: #1e293b;
        font-size: 11px;
        font-weight: 700;
    }

    .workflow-meta {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 8px;
        line-height: 1.55;
    }

    .workflow-record-title {
        margin: 9px 0 0;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
    }

    .workflow-record-description {
        max-width: 760px;
        margin: 5px 0 0;
        color: #64748b;
        font-size: 10px;
        line-height: 1.7;
    }

    .workflow-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 9px;
    }

    .workflow-action {
        display: inline-flex;
        min-height: 39px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid #c7d2fe;
        border-radius: 11px;
        background: #eef2ff;
        padding: 9px 13px;
        color: #4f46e5;
        font-size: 9px;
        font-weight: 700;
        cursor: pointer;
    }

    .workflow-pagination {
        padding: 18px 20px;
        border-top: 1px solid #f1f5f9;
    }

    .workflow-modal-overlay {
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

    .workflow-modal {
        width: 100%;
        max-width: 720px;
        margin: auto;
        overflow: hidden;
        border-radius: 22px;
        background: #ffffff;
        box-shadow: 0 25px 80px rgba(15, 23, 42, 0.28);
    }

    .workflow-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 22px;
        border-bottom: 1px solid #f1f5f9;
    }

    .workflow-modal-body {
        display: grid;
        gap: 18px;
        max-height: 68vh;
        overflow-y: auto;
        padding: 22px;
    }

    .workflow-modal-footer {
        display: flex;
        flex-direction: column-reverse;
        gap: 10px;
        padding: 18px 22px;
        border-top: 1px solid #f1f5f9;
    }

    .workflow-detail {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        padding: 14px;
    }

    .workflow-detail-label {
        margin: 0 0 7px;
        color: #64748b;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .workflow-detail-value {
        margin: 0;
        color: #475569;
        font-size: 10px;
        line-height: 1.75;
        white-space: pre-line;
    }

    .workflow-label {
        display: block;
        margin-bottom: 8px;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
    }

    .workflow-input,
    .workflow-textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 13px;
        background: #f8fafc;
        padding: 13px;
        color: #334155;
        font-size: 11px;
        outline: 0;
    }

    .workflow-textarea {
        min-height: 120px;
        resize: vertical;
        line-height: 1.7;
    }

    .workflow-button {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 11px;
        padding: 9px 15px;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
    }

    .workflow-button-neutral {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #475569;
    }

    .workflow-button-primary {
        border: 0;
        background: linear-gradient(
            135deg,
            #2563eb,
            #6d28d9
        );
        color: #ffffff;
    }

    .workflow-button-danger {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #dc2626;
    }

    @media (min-width: 640px) {
        .workflow-heading-row {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }

        .workflow-statistics {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .workflow-toolbar {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .workflow-search {
            width: 320px;
        }

        .workflow-item {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .workflow-modal-footer {
            flex-direction: row;
            justify-content: flex-end;
        }
    }

    @media (min-width: 1100px) {
        .workflow-statistics {
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
        }
    }
</style>

<div class="workflow-page">
    <section class="workflow-heading-row">
        <div>
            <h1 class="workflow-title">
                {{ $config['page_title'] }}
            </h1>

            <p class="workflow-description">
                {{ $config['description'] }}
            </p>
        </div>

        <a
            href="{{ route('dashboard.dosen') }}"
            class="simmag-secondary-button"
        >
            <span class="material-symbols-rounded">
                arrow_back
            </span>

            Kembali
        </a>
    </section>

    <section class="workflow-statistics">
        @foreach ($cards as $card)
            <article class="workflow-statistic">
                <div
                    class="workflow-statistic-icon"
                    style="
                        background: {{ $card['background'] }};
                        color: {{ $card['color'] }};
                    "
                >
                    <span class="material-symbols-rounded">
                        {{ $card['icon'] }}
                    </span>
                </div>

                <div>
                    <p class="workflow-statistic-label">
                        {{ $card['label'] }}
                    </p>

                    <p class="workflow-statistic-value">
                        {{ $card['value'] }}
                    </p>
                </div>
            </article>
        @endforeach
    </section>

    <section class="simmag-card">
        <div class="simmag-card-header">
            <div>
                <h2 class="simmag-card-title">
                    Data {{ $config['page_title'] }}
                </h2>

                <p class="simmag-card-description">
                    Hanya data mahasiswa bimbingan Anda yang ditampilkan.
                </p>
            </div>
        </div>

        <div class="workflow-toolbar">
            <div class="workflow-search">
                <span class="material-symbols-rounded">
                    search
                </span>

                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Cari mahasiswa, NIM, instansi, atau data..."
                >
            </div>

            <select
                wire:model.live="statusFilter"
                class="workflow-filter"
            >
                <option value="all">
                    Semua Status
                </option>

                <option value="waiting">
                    Menunggu
                </option>

                <option value="completed">
                    Selesai
                </option>

                <option value="revision">
                    Perlu Revisi
                </option>
            </select>
        </div>

        @if ($records->isNotEmpty())
            <div class="workflow-list">
                @foreach ($records as $record)
                    <div
                        wire:key="workflow-{{ $module }}-{{ $record->row_id }}"
                        class="workflow-item"
                    >
                        <div class="workflow-profile">
                            <div class="workflow-avatar">
                                {{ $initials(
                                    $record->student_name
                                ) }}
                            </div>

                            <div class="workflow-information">
                                <p class="workflow-student">
                                    {{ $record->student_name
                                        ?? 'Mahasiswa' }}
                                </p>

                                <p class="workflow-meta">
                                    {{ $record->student_identifier
                                        ?? '-' }}

                                    ·

                                    {{ $record->company_name
                                        ?? '-' }}

                                    ·

                                    {{ $formatDate(
                                        $record->event_date
                                    ) }}
                                </p>

                                <p class="workflow-record-title">
                                    {{ $record->title
                                        ?? $config['page_title'] }}
                                </p>

                                @if (filled($record->description))
                                    <p class="workflow-record-description">
                                        {{ \Illuminate\Support\Str::limit(
                                            $record->description,
                                            180
                                        ) }}
                                    </p>
                                @endif

                                <div class="workflow-footer">
                                    <span
                                        class="simmag-status {{ $statusClass(
                                            $record->status
                                        ) }}"
                                    >
                                        {{ $formatStatus(
                                            $record->status
                                        ) }}
                                    </span>

                                    @if (
                                        filled(
                                            $record->score
                                        )
                                    )
                                        <span class="simmag-status is-primary">
                                            Nilai:
                                            {{ number_format(
                                                (float) $record->score,
                                                0
                                            ) }}
                                        </span>
                                    @endif

                                    @if (
                                        filled(
                                            $record->file_path
                                        )
                                    )
                                        <span class="simmag-status is-primary">
                                            Ada Berkas
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button
                            wire:click="openDetail({{ $record->row_id }})"
                            type="button"
                            class="workflow-action"
                        >
                            <span class="material-symbols-rounded">
                                {{ $module === 'logbooks'
                                    ? 'visibility'
                                    : 'fact_check' }}
                            </span>

                            {{ $module === 'logbooks'
                                ? 'Lihat'
                                : 'Proses' }}
                        </button>
                    </div>
                @endforeach
            </div>

            @if ($records->hasPages())
                <div class="workflow-pagination">
                    {{ $records->links() }}
                </div>
            @endif
        @else
            <div class="simmag-empty-state">
                <div class="simmag-empty-icon">
                    <span class="material-symbols-rounded">
                        {{ $config['icon'] }}
                    </span>
                </div>

                <p class="simmag-empty-title">
                    {{ $config['empty_title'] }}
                </p>

                <p class="simmag-empty-description">
                    {{ $config['empty_description'] }}
                </p>
            </div>
        @endif
    </section>

    @if (
        $detailOpen
        && $selectedRecord
    )
        <div
            wire:click.self="closeDetail"
            class="workflow-modal-overlay"
        >
            <div class="workflow-modal">
                <div class="workflow-modal-header">
                    <div>
                        <p
                            style="
                                margin:0;
                                color:#4f46e5;
                                font-size:9px;
                                font-weight:800;
                                text-transform:uppercase;
                            "
                        >
                            {{ $config['page_title'] }}
                        </p>

                        <h2
                            style="
                                margin:7px 0 0;
                                color:#0f172a;
                                font-family:'Plus Jakarta Sans', sans-serif;
                                font-size:20px;
                                font-weight:800;
                            "
                        >
                            {{ $selectedRecord->student_name
                                ?? 'Mahasiswa' }}
                        </h2>

                        <p
                            style="
                                margin:5px 0 0;
                                color:#94a3b8;
                                font-size:9px;
                            "
                        >
                            {{ $selectedRecord->student_identifier
                                ?? '-' }}

                            ·

                            {{ $selectedRecord->company_name
                                ?? '-' }}
                        </p>
                    </div>

                    <button
                        wire:click="closeDetail"
                        type="button"
                        class="simmag-icon-button"
                    >
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>
                </div>

                <div class="workflow-modal-body">
                    <div>
                        <span
                            class="simmag-status {{ $statusClass(
                                $selectedRecord->status
                            ) }}"
                        >
                            {{ $formatStatus(
                                $selectedRecord->status
                            ) }}
                        </span>
                    </div>

                    <div class="workflow-detail">
                        <p class="workflow-detail-label">
                            Judul
                        </p>

                        <p class="workflow-detail-value">
                            {{ $selectedRecord->title
                                ?? $config['page_title'] }}
                        </p>
                    </div>

                    <div class="workflow-detail">
                        <p class="workflow-detail-label">
                            Tanggal
                        </p>

                        <p class="workflow-detail-value">
                            {{ $formatDate(
                                $selectedRecord->event_date
                            ) }}
                        </p>
                    </div>

                    @if (
                        filled(
                            $selectedRecord->description
                        )
                    )
                        <div class="workflow-detail">
                            <p class="workflow-detail-label">
                                Uraian
                            </p>

                            <p class="workflow-detail-value">
                                {{ $selectedRecord->description }}
                            </p>
                        </div>
                    @endif

                    @if (
                        filled(
                            $selectedRecord->file_path
                        )
                    )
                        <div>
                            <a
                                href="{{ \Illuminate\Support\Facades\Storage::url(
                                    $selectedRecord->file_path
                                ) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="simmag-secondary-button"
                            >
                                <span class="material-symbols-rounded">
                                    attach_file
                                </span>

                                {{ $selectedRecord->file_name
                                    ?? 'Lihat Berkas' }}
                            </a>
                        </div>
                    @endif

                    @if ($module === 'consultations')
                        <div>
                            <label class="workflow-label">
                                Tanggapan Dosen
                            </label>

                            <textarea
                                wire:model="reviewNote"
                                class="workflow-textarea"
                                placeholder="Tuliskan arahan atau hasil bimbingan..."
                            ></textarea>

                            @error('reviewNote')
                                <p
                                    style="
                                        margin:7px 0 0;
                                        color:#dc2626;
                                        font-size:9px;
                                        font-weight:600;
                                    "
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endif

                    @if ($module === 'final_reports')
                        <div>
                            <label class="workflow-label">
                                Catatan Review
                            </label>

                            <textarea
                                wire:model="reviewNote"
                                class="workflow-textarea"
                                placeholder="Tuliskan catatan persetujuan atau alasan revisi..."
                            ></textarea>

                            @error('reviewNote')
                                <p
                                    style="
                                        margin:7px 0 0;
                                        color:#dc2626;
                                        font-size:9px;
                                        font-weight:600;
                                    "
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endif

                    @if ($module === 'assessments')
                        <div>
                            <label class="workflow-label">
                                Nilai Akademik
                            </label>

                            <input
                                wire:model="score"
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                class="workflow-input"
                                placeholder="0 sampai 100"
                            >

                            @error('score')
                                <p
                                    style="
                                        margin:7px 0 0;
                                        color:#dc2626;
                                        font-size:9px;
                                        font-weight:600;
                                    "
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="workflow-label">
                                Catatan Penilaian
                            </label>

                            <textarea
                                wire:model="reviewNote"
                                class="workflow-textarea"
                                placeholder="Tuliskan catatan penilaian akademik..."
                            ></textarea>
                        </div>
                    @endif

                    @if (
                        $module === 'logbooks'
                        && filled(
                            $selectedRecord->review_note
                        )
                    )
                        <div class="workflow-detail">
                            <p class="workflow-detail-label">
                                Catatan Pembimbing Lapangan
                            </p>

                            <p class="workflow-detail-value">
                                {{ $selectedRecord->review_note }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="workflow-modal-footer">
                    <button
                        wire:click="closeDetail"
                        type="button"
                        class="workflow-button workflow-button-neutral"
                    >
                        Tutup
                    </button>

                    @if ($module === 'consultations')
                        <button
                            wire:click="submitConsultationResponse"
                            wire:loading.attr="disabled"
                            type="button"
                            class="workflow-button workflow-button-primary"
                        >
                            <span class="material-symbols-rounded">
                                send
                            </span>

                            Simpan Tanggapan
                        </button>
                    @endif

                    @if ($module === 'final_reports')
                        <button
                            wire:click="requestFinalReportRevision"
                            wire:loading.attr="disabled"
                            type="button"
                            class="workflow-button workflow-button-danger"
                        >
                            <span class="material-symbols-rounded">
                                edit_document
                            </span>

                            Perlu Revisi
                        </button>

                        <button
                            wire:click="approveFinalReport"
                            wire:loading.attr="disabled"
                            type="button"
                            class="workflow-button workflow-button-primary"
                        >
                            <span class="material-symbols-rounded">
                                task_alt
                            </span>

                            Setujui
                        </button>
                    @endif

                    @if ($module === 'assessments')
                        <button
                            wire:click="saveAssessment"
                            wire:loading.attr="disabled"
                            type="button"
                            class="workflow-button workflow-button-primary"
                        >
                            <span class="material-symbols-rounded">
                                save
                            </span>

                            Simpan Penilaian
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>