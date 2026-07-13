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

    $statusClass = function (?string $status): string {
        return match ($status) {
            'aktif' => 'is-success',
            'selesai' => 'is-primary',
            'menunggu_ka' => 'is-warning',
            'batal' => 'is-danger',
            default => 'is-neutral',
        };
    };

    $formatDate = function (mixed $date): string {
        if (blank($date)) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($date)
                ->translatedFormat('d M Y');
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

    $statisticCards = [
        [
            'label' => 'Total Mahasiswa',
            'value' => $statistics['total'] ?? 0,
            'description' => 'Mahasiswa yang ditugaskan',
            'icon' => 'groups',
            'background' => '#eff6ff',
            'color' => '#2563eb',
        ],
        [
            'label' => 'Menunggu Persiapan',
            'value' => $statistics['waiting'] ?? 0,
            'description' => 'Draft atau menunggu KA',
            'icon' => 'pending_actions',
            'background' => '#fff7ed',
            'color' => '#ea580c',
        ],
        [
            'label' => 'Magang Aktif',
            'value' => $statistics['active'] ?? 0,
            'description' => 'Sedang menjalani magang',
            'icon' => 'work',
            'background' => '#ecfdf5',
            'color' => '#16a34a',
        ],
        [
            'label' => 'Selesai',
            'value' => $statistics['completed'] ?? 0,
            'description' => 'Magang telah selesai',
            'icon' => 'task_alt',
            'background' => '#f5f3ff',
            'color' => '#7c3aed',
        ],
    ];
@endphp

<style>
    .lecturer-student-page {
        display: grid;
        gap: 22px;
    }

    .lecturer-student-heading {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .lecturer-student-heading-row {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .lecturer-student-title {
        margin: 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: clamp(25px, 3vw, 32px);
        font-weight: 800;
        letter-spacing: -0.035em;
    }

    .lecturer-student-description {
        max-width: 760px;
        margin: 7px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.75;
    }

    .lecturer-student-statistics {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .lecturer-student-statistic {
        display: flex;
        min-height: 118px;
        align-items: center;
        gap: 15px;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.035);
    }

    .lecturer-student-statistic-icon {
        display: flex;
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
    }

    .lecturer-student-statistic-label {
        margin: 0;
        color: #64748b;
        font-size: 10px;
        font-weight: 600;
    }

    .lecturer-student-statistic-value {
        margin: 5px 0 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 24px;
        font-weight: 800;
    }

    .lecturer-student-statistic-description {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 9px;
    }

    .lecturer-student-toolbar {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .lecturer-student-search {
        display: flex;
        min-height: 42px;
        align-items: center;
        gap: 9px;
        padding: 0 13px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
    }

    .lecturer-student-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: #334155;
        font-size: 11px;
    }

    .lecturer-student-filter {
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

    .lecturer-student-table-wrapper {
        overflow-x: auto;
    }

    .lecturer-student-table {
        width: 100%;
        min-width: 980px;
        border-collapse: collapse;
    }

    .lecturer-student-table th {
        padding: 13px 16px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #64748b;
        font-size: 8px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .lecturer-student-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
        font-size: 10px;
        vertical-align: middle;
    }

    .lecturer-student-table tbody tr:hover {
        background: #fafafa;
    }

    .lecturer-student-profile {
        display: flex;
        min-width: 220px;
        align-items: center;
        gap: 12px;
    }

    .lecturer-student-avatar {
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

    .lecturer-student-name {
        margin: 0;
        color: #1e293b;
        font-size: 11px;
        font-weight: 700;
    }

    .lecturer-student-meta {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 8px;
        line-height: 1.5;
    }

    .lecturer-student-company {
        display: flex;
        min-width: 180px;
        align-items: flex-start;
        gap: 8px;
    }

    .lecturer-student-company .material-symbols-rounded {
        color: #64748b;
        font-size: 18px;
    }

    .lecturer-student-period {
        min-width: 130px;
    }

    .lecturer-student-supervisor {
        min-width: 170px;
    }

    .lecturer-student-date {
        min-width: 150px;
        color: #64748b;
        line-height: 1.7;
    }

    .lecturer-student-pagination {
        padding: 18px 20px;
        border-top: 1px solid #f1f5f9;
    }

    @media (min-width: 640px) {
        .lecturer-student-statistics {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .lecturer-student-toolbar {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .lecturer-student-search {
            width: 300px;
        }

        .lecturer-student-heading-row {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }
    }

    @media (min-width: 1100px) {
        .lecturer-student-statistics {
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
        }
    }
</style>

<div class="lecturer-student-page">
    <section class="lecturer-student-heading">
        <div class="lecturer-student-heading-row">
            <div>
                <h1 class="lecturer-student-title">
                    Mahasiswa Bimbingan
                </h1>

                <p class="lecturer-student-description">
                    Daftar mahasiswa magang yang ditugaskan kepada
                    akun Dosen Pembimbing yang sedang login.
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

    <section class="lecturer-student-statistics">
        @foreach ($statisticCards as $statistic)
            <article class="lecturer-student-statistic">
                <div
                    class="lecturer-student-statistic-icon"
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
                    <p class="lecturer-student-statistic-label">
                        {{ $statistic['label'] }}
                    </p>

                    <p class="lecturer-student-statistic-value">
                        {{ $statistic['value'] }}
                    </p>

                    <p class="lecturer-student-statistic-description">
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
                    Data Mahasiswa
                </h2>

                <p class="simmag-card-description">
                    Data diambil langsung dari penugasan magang.
                </p>
            </div>
        </div>

        <div class="lecturer-student-toolbar">
            <div class="lecturer-student-search">
                <span class="material-symbols-rounded">
                    search
                </span>

                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Cari nama, NIM, atau instansi..."
                >
            </div>

            <select
                wire:model.live="statusFilter"
                class="lecturer-student-filter"
            >
                <option value="all">
                    Semua Status
                </option>

                <option value="draft">
                    Draft
                </option>

                <option value="menunggu_ka">
                    Menunggu Kerangka Acuan
                </option>

                <option value="aktif">
                    Aktif
                </option>

                <option value="selesai">
                    Selesai
                </option>

                <option value="batal">
                    Batal
                </option>
            </select>
        </div>

        @if ($internships->isNotEmpty())
            <div class="lecturer-student-table-wrapper">
                <table class="lecturer-student-table">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Instansi</th>
                            <th>Periode</th>
                            <th>Pembimbing Lapangan</th>
                            <th>Pelaksanaan</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($internships as $internship)
                            <tr wire:key="internship-{{ $internship->id }}">
                                <td>
                                    <div class="lecturer-student-profile">
                                        <div class="lecturer-student-avatar">
                                            {{ $initials(
                                                $internship->student?->name
                                                    ?? $internship->student_name
                                            ) }}
                                        </div>

                                        <div>
                                            <p class="lecturer-student-name">
                                                {{ $internship->student?->name
                                                    ?? $internship->student_name
                                                    ?? 'Mahasiswa' }}
                                            </p>

                                            <p class="lecturer-student-meta">
                                                {{ $internship->student?->nim
                                                    ?? $internship->student?->identifier
                                                    ?? $internship->student_nim
                                                    ?? '-' }}
                                            </p>

                                            <p class="lecturer-student-meta">
                                                {{ $internship->student?->email
                                                    ?? $internship->student_email
                                                    ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="lecturer-student-company">
                                        <span class="material-symbols-rounded">
                                            apartment
                                        </span>

                                        <div>
                                            <p class="lecturer-student-name">
                                                {{ $internship->company?->name
                                                    ?? $internship->company_name
                                                    ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="lecturer-student-period">
                                        <p class="lecturer-student-name">
                                            {{ $internship->period?->academic_year
                                                ?? '-' }}
                                        </p>

                                        <p class="lecturer-student-meta">
                                            {{ $internship->period?->semester
                                                ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                <td>
                                    <div class="lecturer-student-supervisor">
                                        <p class="lecturer-student-name">
                                            {{ $internship->fieldSupervisor?->name
                                                ?? $internship->field_supervisor_name
                                                ?? '-' }}
                                        </p>

                                        <p class="lecturer-student-meta">
                                            {{ $internship->fieldSupervisor?->email
                                                ?? $internship->field_supervisor_email
                                                ?? '-' }}
                                        </p>
                                    </div>
                                </td>

                                <td>
                                    <div class="lecturer-student-date">
                                        <div>
                                            Mulai:
                                            {{ $formatDate(
                                                $internship->started_at
                                            ) }}
                                        </div>

                                        <div>
                                            Selesai:
                                            {{ $formatDate(
                                                $internship->ended_at
                                            ) }}
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span
                                        class="simmag-status {{ $statusClass(
                                            $internship->status
                                        ) }}"
                                    >
                                        {{ $formatStatus(
                                            $internship->status
                                        ) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($internships->hasPages())
                <div class="lecturer-student-pagination">
                    {{ $internships->links() }}
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
                    Mahasiswa tidak ditemukan
                </p>

                <p class="simmag-empty-description">
                    Belum ada mahasiswa bimbingan atau data tidak
                    sesuai dengan pencarian dan filter.
                </p>
            </div>
        @endif
    </section>
</div>