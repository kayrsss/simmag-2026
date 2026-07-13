@php
    $statusStyles = [
        'Draft' => [
            'background' => '#f1f5f9',
            'color' => '#475569',
            'label' => 'Draft',
        ],
        'Menunggu_Validasi' => [
            'background' => '#fff7ed',
            'color' => '#c2410c',
            'label' => 'Menunggu Validasi',
        ],
        'Tervalidasi' => [
            'background' => '#ecfdf5',
            'color' => '#15803d',
            'label' => 'Tervalidasi',
        ],
        'Perlu_Revisi' => [
            'background' => '#fef2f2',
            'color' => '#dc2626',
            'label' => 'Perlu Revisi',
        ],
    ];
@endphp

<div>
    <style>
        .validation-page {
            display: grid;
            gap: 22px;
        }

        .validation-heading {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .validation-heading-row {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .validation-title {
            margin: 0;
            color: #0f172a;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: clamp(26px, 3vw, 34px);
            font-weight: 800;
            letter-spacing: -0.035em;
        }

        .validation-description {
            max-width: 720px;
            margin: 8px 0 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.7;
        }

        .validation-stat-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .validation-stat-card {
            padding: 18px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.035);
        }

        .validation-stat-icon {
            display: flex;
            width: 42px;
            height: 42px;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
        }

        .validation-stat-label {
            margin: 14px 0 0;
            color: #64748b;
            font-size: 10px;
            font-weight: 600;
        }

        .validation-stat-value {
            margin: 5px 0 0;
            color: #0f172a;
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 23px;
            font-weight: 800;
        }

        .validation-toolbar {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .validation-search {
            display: flex;
            min-height: 42px;
            align-items: center;
            gap: 9px;
            padding: 0 13px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #f8fafc;
        }

        .validation-search input {
            width: 100%;
            border: 0;
            outline: 0;
            background: transparent;
            color: #334155;
            font-size: 11px;
        }

        .validation-filter {
            min-height: 42px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #ffffff;
            padding: 0 12px;
            color: #475569;
            font-size: 11px;
            font-weight: 600;
        }

        .validation-table-wrapper {
            overflow-x: auto;
        }

        .validation-table {
            width: 100%;
            min-width: 920px;
            border-collapse: collapse;
        }

        .validation-table th {
            padding: 13px 16px;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #64748b;
            font-size: 8px;
            font-weight: 800;
            text-align: left;
            text-transform: uppercase;
        }

        .validation-table td {
            padding: 15px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
            font-size: 10px;
            vertical-align: middle;
        }

        .validation-table tbody tr:hover {
            background: #fafafa;
        }

        .validation-student-name {
            margin: 0;
            color: #1e293b;
            font-size: 11px;
            font-weight: 700;
        }

        .validation-student-id {
            margin: 4px 0 0;
            color: #94a3b8;
            font-size: 8px;
        }

        .validation-activity {
            max-width: 330px;
            color: #475569;
            line-height: 1.6;
        }

        .validation-status {
            display: inline-flex;
            min-height: 24px;
            align-items: center;
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 8px;
            font-weight: 800;
            white-space: nowrap;
        }

        .validation-action {
            display: inline-flex;
            min-height: 36px;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid #c7d2fe;
            border-radius: 10px;
            background: #eef2ff;
            padding: 8px 12px;
            color: #4f46e5;
            font-size: 9px;
            font-weight: 700;
            cursor: pointer;
        }

        .validation-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            padding: 20px;
            background: rgba(15, 23, 42, 0.56);
            backdrop-filter: blur(5px);
        }

        .validation-modal {
            width: 100%;
            max-width: 680px;
            margin: auto;
            overflow: hidden;
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 25px 80px rgba(15, 23, 42, 0.25);
        }

        .validation-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 22px;
            border-bottom: 1px solid #f1f5f9;
        }

        .validation-modal-body {
            display: grid;
            gap: 20px;
            padding: 22px;
        }

        .validation-modal-footer {
            display: flex;
            flex-direction: column-reverse;
            gap: 10px;
            padding: 18px 22px;
            border-top: 1px solid #f1f5f9;
        }

        .validation-label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
        }

        .validation-textarea {
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

        .validation-textarea:focus {
            border-color: #818cf8;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .validation-button {
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

        .validation-button-neutral {
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #475569;
        }

        .validation-button-danger {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #dc2626;
        }

        .validation-button-primary {
            border: 0;
            background: linear-gradient(135deg, #2563eb, #6d28d9);
            color: #ffffff;
        }

        @media (min-width: 700px) {
            .validation-heading-row {
                flex-direction: row;
                align-items: flex-end;
                justify-content: space-between;
            }

            .validation-toolbar {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }

            .validation-search {
                width: 260px;
            }

            .validation-modal-footer {
                flex-direction: row;
                justify-content: flex-end;
            }
        }

        @media (min-width: 1000px) {
            .validation-stat-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
    </style>

    <div class="validation-page">
        <section class="validation-heading">
            <div class="validation-heading-row">
                <div>
                    <h1 class="validation-title">
                        Validasi Logbook
                    </h1>

                    <p class="validation-description">
                        Periksa aktivitas dan bukti pendukung mahasiswa
                        sebelum memberikan validasi atau catatan revisi.
                    </p>
                </div>

                <a
                    href="{{ route('dashboard.pembimbing-lapangan') }}"
                    class="simmag-secondary-button"
                >
                    <span class="material-symbols-rounded">
                        arrow_back
                    </span>

                    Kembali
                </a>
            </div>
        </section>

        <section class="validation-stat-grid">
            @foreach ([
                [
                    'label' => 'Total Logbook',
                    'value' => $statistics['total'] ?? 0,
                    'icon' => 'library_books',
                    'background' => '#eff6ff',
                    'color' => '#2563eb',
                ],
                [
                    'label' => 'Menunggu Validasi',
                    'value' => $statistics['waiting'] ?? 0,
                    'icon' => 'pending_actions',
                    'background' => '#fff7ed',
                    'color' => '#ea580c',
                ],
                [
                    'label' => 'Tervalidasi',
                    'value' => $statistics['validated'] ?? 0,
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
            ] as $statistic)
                <article class="validation-stat-card">
                    <div
                        class="validation-stat-icon"
                        style="
                            background: {{ $statistic['background'] }};
                            color: {{ $statistic['color'] }};
                        "
                    >
                        <span class="material-symbols-rounded">
                            {{ $statistic['icon'] }}
                        </span>
                    </div>

                    <p class="validation-stat-label">
                        {{ $statistic['label'] }}
                    </p>

                    <p class="validation-stat-value">
                        {{ $statistic['value'] }}
                    </p>
                </article>
            @endforeach
        </section>

        <section class="simmag-card">
            <div class="simmag-card-header">
                <div>
                    <h2 class="simmag-card-title">
                        Logbook Mahasiswa
                    </h2>

                    <p class="simmag-card-description">
                        Data diambil langsung dari database SIMMAG.
                    </p>
                </div>
            </div>

            <div class="validation-toolbar">
                <div class="validation-search">
                    <span class="material-symbols-rounded">
                        search
                    </span>

                    <input
                        wire:model.live.debounce.300ms="search"
                        type="search"
                        placeholder="Cari mahasiswa atau aktivitas..."
                    >
                </div>

                <select
                    wire:model.live="statusFilter"
                    class="validation-filter"
                >
                    <option value="all">
                        Semua Status
                    </option>

                    <option value="Menunggu_Validasi">
                        Menunggu Validasi
                    </option>

                    <option value="Tervalidasi">
                        Tervalidasi
                    </option>

                    <option value="Perlu_Revisi">
                        Perlu Revisi
                    </option>
                </select>
            </div>

            @if (($logbooks ?? collect())->isNotEmpty())
                <div class="validation-table-wrapper">
                    <table class="validation-table">
                        <thead>
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Tanggal</th>
                                <th>Aktivitas</th>
                                <th>Bukti</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($logbooks as $logbook)
                                @php
                                    $currentStatus =
                                        $statusStyles[$logbook->status]
                                        ?? $statusStyles['Draft'];
                                @endphp

                                <tr wire:key="logbook-{{ $logbook->id }}">
                                    <td>
                                        <p class="validation-student-name">
                                            {{ $logbook->student?->name ?? 'Mahasiswa' }}
                                        </p>

                                        <p class="validation-student-id">
                                            {{ $logbook->student?->nim
                                                ?? $logbook->student?->identifier
                                                ?? $logbook->student?->username
                                                ?? '-' }}
                                        </p>
                                    </td>

                                    <td>
                                        {{ $logbook->activity_date?->translatedFormat('d M Y') ?? '-' }}
                                    </td>

                                    <td>
                                        <div class="validation-activity">
                                            {{ \Illuminate\Support\Str::limit(
                                                $logbook->activity,
                                                130
                                            ) }}
                                        </div>
                                    </td>

                                    <td>
                                        @if ($logbook->evidence_path)
                                            <a
                                                href="{{ \Illuminate\Support\Facades\Storage::url(
                                                    $logbook->evidence_path
                                                ) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="simmag-link-button"
                                            >
                                                <span class="material-symbols-rounded">
                                                    attach_file
                                                </span>

                                                Lihat Bukti
                                            </a>
                                        @else
                                            <span style="color:#94a3b8;">
                                                Tidak ada
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <span
                                            class="validation-status"
                                            style="
                                                background: {{ $currentStatus['background'] }};
                                                color: {{ $currentStatus['color'] }};
                                            "
                                        >
                                            {{ $currentStatus['label'] }}
                                        </span>
                                    </td>

                                    <td>
                                        <button
                                            wire:click="openReview({{ $logbook->id }})"
                                            type="button"
                                            class="validation-action"
                                        >
                                            <span class="material-symbols-rounded">
                                                {{ $logbook->status === 'Menunggu_Validasi'
                                                    ? 'fact_check'
                                                    : 'visibility' }}
                                            </span>

                                            {{ $logbook->status === 'Menunggu_Validasi'
                                                ? 'Validasi'
                                                : 'Detail' }}
                                        </button>
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
                            task_alt
                        </span>
                    </div>

                    <p class="simmag-empty-title">
                        Tidak ada logbook
                    </p>

                    <p class="simmag-empty-description">
                        Belum ada logbook mahasiswa yang sesuai dengan filter.
                    </p>
                </div>
            @endif
        </section>
    </div>

    @if ($reviewOpen && $selectedLogbook)
        @php
            $selectedStatus =
                $statusStyles[$selectedLogbook->status]
                ?? $statusStyles['Draft'];
        @endphp

        <div
            wire:click.self="closeReview"
            class="validation-modal-overlay"
        >
            <div class="validation-modal">
                <div class="validation-modal-header">
                    <div>
                        <p
                            style="
                                margin:0;
                                color:#4f46e5;
                                font-size:9px;
                                font-weight:800;
                                text-transform:uppercase;
                                letter-spacing:0.1em;
                            "
                        >
                            Detail Logbook
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
                            {{ $selectedLogbook->student?->name ?? 'Mahasiswa' }}
                        </h2>

                        <p
                            style="
                                margin:5px 0 0;
                                color:#94a3b8;
                                font-size:9px;
                            "
                        >
                            {{ $selectedLogbook->activity_date?->translatedFormat('d F Y') ?? '-' }}
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

                <div class="validation-modal-body">
                    <div>
                        <span
                            class="validation-status"
                            style="
                                background: {{ $selectedStatus['background'] }};
                                color: {{ $selectedStatus['color'] }};
                            "
                        >
                            {{ $selectedStatus['label'] }}
                        </span>
                    </div>

                    <div>
                        <p class="validation-label">
                            Uraian Aktivitas
                        </p>

                        <div
                            style="
                                border:1px solid #e5e7eb;
                                border-radius:14px;
                                background:#f8fafc;
                                padding:15px;
                                color:#475569;
                                font-size:11px;
                                line-height:1.8;
                                white-space:pre-line;
                            "
                        >
                            {{ $selectedLogbook->activity }}
                        </div>
                    </div>

                    <div>
                        <p class="validation-label">
                            Bukti Pendukung
                        </p>

                        @if ($selectedLogbook->evidence_path)
                            <a
                                href="{{ \Illuminate\Support\Facades\Storage::url(
                                    $selectedLogbook->evidence_path
                                ) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="simmag-secondary-button"
                            >
                                <span class="material-symbols-rounded">
                                    attach_file
                                </span>

                                {{ $selectedLogbook->evidence_name ?? 'Lihat Bukti' }}
                            </a>
                        @else
                            <p
                                style="
                                    margin:0;
                                    color:#94a3b8;
                                    font-size:10px;
                                "
                            >
                                Tidak ada bukti pendukung.
                            </p>
                        @endif
                    </div>

                    @if ($selectedLogbook->status === 'Menunggu_Validasi')
                        <div>
                            <label
                                for="review-note"
                                class="validation-label"
                            >
                                Catatan Pembimbing Lapangan
                            </label>

                            <textarea
                                wire:model="reviewNote"
                                id="review-note"
                                class="validation-textarea"
                                placeholder="Isi catatan validasi atau alasan revisi..."
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
                    @elseif ($selectedLogbook->review_note)
                        <div>
                            <p class="validation-label">
                                Catatan Validasi
                            </p>

                            <div
                                style="
                                    border:1px solid #e5e7eb;
                                    border-radius:14px;
                                    background:#f8fafc;
                                    padding:15px;
                                    color:#475569;
                                    font-size:11px;
                                    line-height:1.8;
                                "
                            >
                                {{ $selectedLogbook->review_note }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="validation-modal-footer">
                    <button
                        wire:click="closeReview"
                        type="button"
                        class="validation-button validation-button-neutral"
                    >
                        Tutup
                    </button>

                    @if ($selectedLogbook->status === 'Menunggu_Validasi')
                        <button
                            wire:click="requestRevision"
                            wire:loading.attr="disabled"
                            type="button"
                            class="validation-button validation-button-danger"
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
                            class="validation-button validation-button-primary"
                        >
                            <span class="material-symbols-rounded">
                                task_alt
                            </span>

                            Validasi Logbook
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>