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
            str_contains(
                $normalized,
                'disetujui'
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
            || str_contains(
                $normalized,
                'diajukan'
            )
        ) {
            return 'is-warning';
        }

        return 'is-neutral';
    };

    $isReviewable = function (
        ?string $status
    ) use ($normalizeStatus): bool {
        return in_array(
            $normalizeStatus($status),
            [
                'disetujui_pl',
                'menunggu_review_dosen',
                'menunggu_persetujuan_dosen',
                'diajukan_ke_dosen',
            ],
            true
        );
    };

    $formatDate = function (
        mixed $value
    ): string {
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

    $initials = function (
        ?string $name
    ): string {
        $result = collect(
            preg_split(
                '/\s+/',
                trim(
                    $name
                    ?: 'Mahasiswa'
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

        return $result !== ''
            ? $result
            : 'MH';
    };

    $statisticCards = [
        [
            'label' => 'Total Dokumen',
            'value' => $statistics['total'] ?? 0,
            'description' => 'Seluruh KA mahasiswa',
            'icon' => 'description',
            'background' => '#eff6ff',
            'color' => '#2563eb',
        ],
        [
            'label' => 'Menunggu Review',
            'value' => $statistics['waiting'] ?? 0,
            'description' => 'Siap diproses dosen',
            'icon' => 'pending_actions',
            'background' => '#fff7ed',
            'color' => '#ea580c',
        ],
        [
            'label' => 'Disetujui',
            'value' => $statistics['approved'] ?? 0,
            'description' => 'Sudah disetujui dosen',
            'icon' => 'task_alt',
            'background' => '#ecfdf5',
            'color' => '#16a34a',
        ],
        [
            'label' => 'Perlu Revisi',
            'value' => $statistics['revision'] ?? 0,
            'description' => 'Dikembalikan ke mahasiswa',
            'icon' => 'edit_document',
            'background' => '#fef2f2',
            'color' => '#dc2626',
        ],
    ];
@endphp

<style>
    .lecturer-framework-page {
        display: grid;
        gap: 22px;
    }

    .lecturer-framework-heading {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .lecturer-framework-heading-row {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .lecturer-framework-title {
        margin: 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: clamp(25px, 3vw, 32px);
        font-weight: 800;
        letter-spacing: -0.035em;
    }

    .lecturer-framework-description {
        max-width: 760px;
        margin: 7px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.75;
    }

    .lecturer-framework-statistics {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .lecturer-framework-statistic {
        display: flex;
        min-height: 118px;
        align-items: center;
        gap: 15px;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        box-shadow:
            0 8px 28px rgba(15, 23, 42, 0.035);
    }

    .lecturer-framework-statistic-icon {
        display: flex;
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
    }

    .lecturer-framework-statistic-label {
        margin: 0;
        color: #64748b;
        font-size: 10px;
        font-weight: 600;
    }

    .lecturer-framework-statistic-value {
        margin: 5px 0 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 24px;
        font-weight: 800;
    }

    .lecturer-framework-statistic-description {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 9px;
    }

    .lecturer-framework-toolbar {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .lecturer-framework-search {
        display: flex;
        min-height: 42px;
        align-items: center;
        gap: 9px;
        padding: 0 13px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
    }

    .lecturer-framework-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: #334155;
        font-size: 11px;
    }

    .lecturer-framework-filter {
        min-height: 42px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        padding: 0 12px;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
        outline: 0;
    }

    .lecturer-framework-list {
        display: grid;
    }

    .lecturer-framework-item {
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding: 18px 20px;
        border-top: 1px solid #f1f5f9;
    }

    .lecturer-framework-item:first-child {
        border-top: 0;
    }

    .lecturer-framework-profile {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 13px;
    }

    .lecturer-framework-avatar {
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

    .lecturer-framework-information {
        min-width: 0;
        flex: 1;
    }

    .lecturer-framework-student {
        margin: 0;
        color: #1e293b;
        font-size: 11px;
        font-weight: 700;
    }

    .lecturer-framework-meta {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 8px;
        line-height: 1.55;
    }

    .lecturer-framework-document-title {
        margin: 9px 0 0;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.6;
    }

    .lecturer-framework-document-description {
        max-width: 760px;
        margin: 5px 0 0;
        color: #64748b;
        font-size: 10px;
        line-height: 1.7;
    }

    .lecturer-framework-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 9px;
    }

    .lecturer-framework-review-button {
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

    .lecturer-framework-pagination {
        padding: 18px 20px;
        border-top: 1px solid #f1f5f9;
    }

    .lecturer-framework-modal-overlay {
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

    .lecturer-framework-modal {
        width: 100%;
        max-width: 760px;
        margin: auto;
        overflow: hidden;
        border-radius: 22px;
        background: #ffffff;
        box-shadow:
            0 25px 80px rgba(15, 23, 42, 0.28);
    }

    .lecturer-framework-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 22px;
        border-bottom: 1px solid #f1f5f9;
    }

    .lecturer-framework-modal-body {
        display: grid;
        gap: 18px;
        max-height: 68vh;
        overflow-y: auto;
        padding: 22px;
    }

    .lecturer-framework-modal-footer {
        display: flex;
        flex-direction: column-reverse;
        gap: 10px;
        padding: 18px 22px;
        border-top: 1px solid #f1f5f9;
    }

    .lecturer-framework-detail-grid {
        display: grid;
        gap: 12px;
    }

    .lecturer-framework-detail {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        padding: 14px;
    }

    .lecturer-framework-detail-label {
        margin: 0 0 7px;
        color: #64748b;
        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .lecturer-framework-detail-value {
        margin: 0;
        color: #475569;
        font-size: 10px;
        line-height: 1.75;
        white-space: pre-line;
    }

    .lecturer-framework-label {
        display: block;
        margin-bottom: 8px;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
    }

    .lecturer-framework-textarea {
        width: 100%;
        min-height: 120px;
        resize: vertical;
        border: 1px solid #e5e7eb;
        border-radius: 13px;
        background: #f8fafc;
        padding: 13px;
        color: #334155;
        font-size: 11px;
        line-height: 1.7;
        outline: 0;
    }

    .lecturer-framework-textarea:focus {
        border-color: #818cf8;
        background: #ffffff;
        box-shadow:
            0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .lecturer-framework-button {
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

    .lecturer-framework-button-neutral {
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #475569;
    }

    .lecturer-framework-button-danger {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #dc2626;
    }

    .lecturer-framework-button-primary {
        border: 0;
        background: linear-gradient(
            135deg,
            #2563eb,
            #6d28d9
        );
        color: #ffffff;
    }

    @media (min-width: 640px) {
        .lecturer-framework-statistics {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .lecturer-framework-toolbar {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .lecturer-framework-search {
            width: 310px;
        }

        .lecturer-framework-heading-row {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }

        .lecturer-framework-item {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .lecturer-framework-detail-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .lecturer-framework-modal-footer {
            flex-direction: row;
            justify-content: flex-end;
        }
    }

    @media (min-width: 1100px) {
        .lecturer-framework-statistics {
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
        }
    }
</style>

<div class="lecturer-framework-page">
    <section class="lecturer-framework-heading">
        <div class="lecturer-framework-heading-row">
            <div>
                <h1 class="lecturer-framework-title">
                    Review Kerangka Acuan
                </h1>

                <p class="lecturer-framework-description">
                    Periksa Kerangka Acuan mahasiswa yang sudah
                    melewati persetujuan Pembimbing Lapangan.
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
        </div>
    </section>

    <section class="lecturer-framework-statistics">
        @foreach ($statisticCards as $statistic)
            <article class="lecturer-framework-statistic">
                <div
                    class="lecturer-framework-statistic-icon"
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
                    <p class="lecturer-framework-statistic-label">
                        {{ $statistic['label'] }}
                    </p>

                    <p class="lecturer-framework-statistic-value">
                        {{ $statistic['value'] }}
                    </p>

                    <p class="lecturer-framework-statistic-description">
                        {{ $statistic['description'] }}
                    </p>
                </div>
            </article>
        @endforeach
    </section>

    <section class="simmag-card">
        <div class="simmag-card-header">
            <div>
                <h2 class="simmag-card-title">
                    Kerangka Acuan Mahasiswa
                </h2>

                <p class="simmag-card-description">
                    Data hanya berasal dari mahasiswa bimbingan Anda.
                </p>
            </div>
        </div>

        <div class="lecturer-framework-toolbar">
            <div class="lecturer-framework-search">
                <span class="material-symbols-rounded">
                    search
                </span>

                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Cari mahasiswa, judul, atau instansi..."
                >
            </div>

            <select
                wire:model.live="statusFilter"
                class="lecturer-framework-filter"
            >
                <option value="all">
                    Semua Status
                </option>

                <option value="waiting">
                    Menunggu Review
                </option>

                <option value="approved">
                    Disetujui
                </option>

                <option value="revision">
                    Perlu Revisi
                </option>

                <option value="draft">
                    Draft dan Tahap PL
                </option>
            </select>
        </div>

        @if ($frameworks->isNotEmpty())
            <div class="lecturer-framework-list">
                @foreach ($frameworks as $framework)
                    <div
                        wire:key="framework-{{ $framework->id }}"
                        class="lecturer-framework-item"
                    >
                        <div class="lecturer-framework-profile">
                            <div class="lecturer-framework-avatar">
                                {{ $initials(
                                    $framework->student_name
                                ) }}
                            </div>

                            <div class="lecturer-framework-information">
                                <p class="lecturer-framework-student">
                                    {{ $framework->student_name
                                        ?? 'Mahasiswa' }}
                                </p>

                                <p class="lecturer-framework-meta">
                                    {{ $framework->student_identifier
                                        ?? '-' }}

                                    ·

                                    {{ $framework->company_name
                                        ?? '-' }}

                                    ·

                                    {{ $formatDate(
                                        $framework->updated_at
                                    ) }}
                                </p>

                                <p class="lecturer-framework-document-title">
                                    {{ $framework->title }}
                                </p>

                                <p class="lecturer-framework-document-description">
                                    {{ \Illuminate\Support\Str::limit(
                                        $framework->description
                                            ?? 'Tidak ada deskripsi.',
                                        160
                                    ) }}
                                </p>

                                <div class="lecturer-framework-footer">
                                    <span class="simmag-status is-primary">
                                        Versi
                                        {{ $framework->version }}
                                    </span>

                                    <span
                                        class="simmag-status {{ $statusClass(
                                            $framework->status
                                        ) }}"
                                    >
                                        {{ $formatStatus(
                                            $framework->status
                                        ) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button
                            wire:click="openReview({{ $framework->id }})"
                            type="button"
                            class="lecturer-framework-review-button"
                        >
                            <span class="material-symbols-rounded">
                                {{ $isReviewable(
                                    $framework->status
                                )
                                    ? 'fact_check'
                                    : 'visibility' }}
                            </span>

                            {{ $isReviewable(
                                $framework->status
                            )
                                ? 'Review'
                                : 'Detail' }}
                        </button>
                    </div>
                @endforeach
            </div>

            @if ($frameworks->hasPages())
                <div class="lecturer-framework-pagination">
                    {{ $frameworks->links() }}
                </div>
            @endif
        @else
            <div class="simmag-empty-state">
                <div class="simmag-empty-icon">
                    <span class="material-symbols-rounded">
                        description
                    </span>
                </div>

                <p class="simmag-empty-title">
                    Kerangka Acuan tidak ditemukan
                </p>

                <p class="simmag-empty-description">
                    Belum ada dokumen yang sesuai dengan
                    pencarian atau status yang dipilih.
                </p>
            </div>
        @endif
    </section>

    @if (
        $reviewOpen
        && $selectedFramework
    )
        <div
            wire:click.self="closeReview"
            class="lecturer-framework-modal-overlay"
        >
            <div class="lecturer-framework-modal">
                <div class="lecturer-framework-modal-header">
                    <div>
                        <p
                            style="
                                margin:0;
                                color:#4f46e5;
                                font-size:9px;
                                font-weight:800;
                                text-transform:uppercase;
                                letter-spacing:0.08em;
                            "
                        >
                            Kerangka Acuan Versi
                            {{ $selectedFramework->version }}
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
                            {{ $selectedFramework->title }}
                        </h2>

                        <p
                            style="
                                margin:6px 0 0;
                                color:#94a3b8;
                                font-size:9px;
                            "
                        >
                            {{ $selectedFramework->student_name
                                ?? 'Mahasiswa' }}

                            ·

                            {{ $selectedFramework->student_identifier
                                ?? '-' }}
                        </p>
                    </div>

                    <button
                        wire:click="closeReview"
                        type="button"
                        class="simmag-icon-button"
                    >
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>
                </div>

                <div class="lecturer-framework-modal-body">
                    <div>
                        <span
                            class="simmag-status {{ $statusClass(
                                $selectedFramework->status
                            ) }}"
                        >
                            {{ $formatStatus(
                                $selectedFramework->status
                            ) }}
                        </span>
                    </div>

                    <div class="lecturer-framework-detail-grid">
                        <div class="lecturer-framework-detail">
                            <p class="lecturer-framework-detail-label">
                                Tanggal Mulai
                            </p>

                            <p class="lecturer-framework-detail-value">
                                {{ $formatDate(
                                    $selectedFramework->start_date
                                ) }}
                            </p>
                        </div>

                        <div class="lecturer-framework-detail">
                            <p class="lecturer-framework-detail-label">
                                Target Selesai
                            </p>

                            <p class="lecturer-framework-detail-value">
                                {{ $formatDate(
                                    $selectedFramework->target_end_date
                                ) }}
                            </p>
                        </div>
                    </div>

                    <div class="lecturer-framework-detail">
                        <p class="lecturer-framework-detail-label">
                            Deskripsi
                        </p>

                        <p class="lecturer-framework-detail-value">
                            {{ $selectedFramework->description
                                ?? '-' }}
                        </p>
                    </div>

                    <div class="lecturer-framework-detail">
                        <p class="lecturer-framework-detail-label">
                            Rencana Kerja
                        </p>

                        <p class="lecturer-framework-detail-value">
                            {{ $selectedFramework->work_plan
                                ?? '-' }}
                        </p>
                    </div>

                    <div class="lecturer-framework-detail">
                        <p class="lecturer-framework-detail-label">
                            Ketentuan Kepemilikan
                        </p>

                        <p class="lecturer-framework-detail-value">
                            {{ $selectedFramework->ownership_clause
                                ?? '-' }}
                        </p>
                    </div>

                    <div class="lecturer-framework-detail">
                        <p class="lecturer-framework-detail-label">
                            Ketentuan Kerahasiaan
                        </p>

                        <p class="lecturer-framework-detail-value">
                            {{ $selectedFramework->confidentiality_clause
                                ?? '-' }}
                        </p>
                    </div>

                    <div class="lecturer-framework-detail">
                        <p class="lecturer-framework-detail-label">
                            Ketentuan Kompensasi
                        </p>

                        <p class="lecturer-framework-detail-value">
                            {{ $selectedFramework->remuneration_clause
                                ?? '-' }}
                        </p>
                    </div>

                    @if (
                        filled(
                            $selectedFramework->field_supervisor_notes
                        )
                    )
                        <div class="lecturer-framework-detail">
                            <p class="lecturer-framework-detail-label">
                                Catatan Pembimbing Lapangan
                            </p>

                            <p class="lecturer-framework-detail-value">
                                {{ $selectedFramework->field_supervisor_notes }}
                            </p>
                        </div>
                    @endif

                    @if (
                        filled(
                            $selectedFramework->file_path
                        )
                    )
                        <div>
                            <a
                                href="{{ \Illuminate\Support\Facades\Storage::url(
                                    $selectedFramework->file_path
                                ) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="simmag-secondary-button"
                            >
                                <span class="material-symbols-rounded">
                                    attach_file
                                </span>

                                Lihat File Kerangka Acuan
                            </a>
                        </div>
                    @endif

                    @if (
                        $isReviewable(
                            $selectedFramework->status
                        )
                    )
                        <div>
                            <label
                                for="lecturer-notes"
                                class="lecturer-framework-label"
                            >
                                Catatan Dosen Pembimbing
                            </label>

                            <textarea
                                wire:model="lecturerNotes"
                                id="lecturer-notes"
                                class="lecturer-framework-textarea"
                                placeholder="Isi catatan persetujuan atau alasan revisi..."
                            ></textarea>

                            @error('lecturerNotes')
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
                    @elseif (
                        filled(
                            $selectedFramework->lecturer_notes
                        )
                    )
                        <div class="lecturer-framework-detail">
                            <p class="lecturer-framework-detail-label">
                                Catatan Dosen Pembimbing
                            </p>

                            <p class="lecturer-framework-detail-value">
                                {{ $selectedFramework->lecturer_notes }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="lecturer-framework-modal-footer">
                    <button
                        wire:click="closeReview"
                        type="button"
                        class="lecturer-framework-button lecturer-framework-button-neutral"
                    >
                        Tutup
                    </button>

                    @if (
                        $isReviewable(
                            $selectedFramework->status
                        )
                    )
                        <button
                            wire:click="requestRevision"
                            wire:loading.attr="disabled"
                            type="button"
                            class="lecturer-framework-button lecturer-framework-button-danger"
                        >
                            <span class="material-symbols-rounded">
                                edit_document
                            </span>

                            Perlu Revisi
                        </button>

                        <button
                            wire:click="approve"
                            wire:loading.attr="disabled"
                            type="button"
                            class="lecturer-framework-button lecturer-framework-button-primary"
                        >
                            <span class="material-symbols-rounded">
                                task_alt
                            </span>

                            Setujui Kerangka Acuan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>