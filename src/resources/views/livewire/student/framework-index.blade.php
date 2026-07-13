@php
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

    $statusClass = match ($statusTone) {
        'success' => 'is-success',
        'warning' => 'is-warning',
        'danger' => 'is-danger',
        'neutral' => 'is-neutral',
        default => 'is-primary',
    };
@endphp

<style>
    .framework-page {
        display: grid;
        gap: 22px;
    }

    .framework-heading {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .framework-heading-label {
        margin: 0;
        color: #2563eb;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }

    .framework-heading-title {
        margin: 7px 0 0;
        color: #0f172a;
        font-family:
            "Plus Jakarta Sans",
            sans-serif;
        font-size: clamp(
            30px,
            5vw,
            48px
        );
        font-weight: 800;
        letter-spacing: -0.045em;
    }

    .framework-heading-description {
        max-width: 920px;
        margin: 10px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.8;
    }

    .framework-heading-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .framework-hero {
        position: relative;
        overflow: hidden;
        border-radius: 26px;
        background:
            linear-gradient(
                135deg,
                #1d4ed8,
                #2563eb 48%,
                #6d28d9
            );
        padding: 28px;
        color: #ffffff;
        box-shadow:
            0 22px 55px
            rgba(37, 99, 235, 0.22);
    }

    .framework-hero::before {
        position: absolute;
        top: -110px;
        right: -65px;
        width: 320px;
        height: 320px;
        border: 60px solid
            rgba(255, 255, 255, 0.06);
        border-radius: 50%;
        content: "";
    }

    .framework-hero-content {
        position: relative;
        z-index: 1;
    }

    .framework-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
    }

    .framework-version {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background:
            rgba(255, 255, 255, 0.14);
        padding: 7px 12px;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .framework-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background: #ffffff;
        padding: 7px 12px;
        color: #1d4ed8;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .framework-hero-title {
        max-width: 920px;
        margin: 23px 0 0;
        font-family:
            "Plus Jakarta Sans",
            sans-serif;
        font-size: clamp(
            23px,
            4vw,
            38px
        );
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.035em;
    }

    .framework-hero-description {
        max-width: 880px;
        margin: 13px 0 0;
        color: #dbeafe;
        font-size: 12px;
        line-height: 1.8;
    }

    .framework-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 23px;
    }

    .framework-meta-item {
        display: inline-flex;
        min-height: 43px;
        align-items: center;
        gap: 9px;
        border-radius: 13px;
        background:
            rgba(255, 255, 255, 0.12);
        padding: 10px 14px;
        font-size: 10px;
        font-weight: 700;
    }

    .framework-action-panel {
        display: flex;
        flex-direction: column;
        gap: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: #ffffff;
        padding: 20px;
        box-shadow:
            0 10px 35px
            rgba(15, 23, 42, 0.04);
    }

    .framework-action-title {
        margin: 0;
        color: #0f172a;
        font-size: 15px;
        font-weight: 800;
    }

    .framework-action-description {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 10px;
        line-height: 1.65;
    }

    .framework-action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
    }

    .framework-notes {
        display: grid;
        gap: 12px;
    }

    .framework-note {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        border: 1px solid #fecaca;
        border-radius: 15px;
        background: #fef2f2;
        padding: 15px;
        color: #991b1b;
    }

    .framework-note-title {
        margin: 0;
        font-size: 10px;
        font-weight: 800;
    }

    .framework-note-description {
        margin: 5px 0 0;
        font-size: 9px;
        line-height: 1.7;
        white-space: pre-wrap;
    }

    .framework-detail-grid {
        display: grid;
        gap: 15px;
    }

    .framework-section {
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: #ffffff;
        box-shadow:
            0 10px 35px
            rgba(15, 23, 42, 0.035);
    }

    .framework-section-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .framework-section-title {
        margin: 0;
        color: #0f172a;
        font-size: 14px;
        font-weight: 800;
    }

    .framework-section-description {
        margin: 6px 0 0;
        color: #94a3b8;
        font-size: 9px;
        line-height: 1.6;
    }

    .framework-section-body {
        padding: 20px;
    }

    .framework-information-grid {
        display: grid;
        gap: 14px;
    }

    .framework-information-item {
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        background: #f8fafc;
        padding: 14px;
    }

    .framework-information-label {
        margin: 0;
        color: #94a3b8;
        font-size: 8px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .framework-information-value {
        margin: 7px 0 0;
        color: #334155;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.75;
        white-space: pre-wrap;
    }

    .framework-form {
        display: grid;
        gap: 17px;
    }

    .framework-form-grid {
        display: grid;
        gap: 15px;
    }

    .framework-field {
        display: grid;
        gap: 7px;
    }

    .framework-field-label {
        color: #334155;
        font-size: 10px;
        font-weight: 800;
    }

    .framework-required {
        color: #dc2626;
    }

    .framework-input,
    .framework-textarea {
        width: 100%;
        border: 1px solid #dbe2ea;
        border-radius: 13px;
        background: #ffffff;
        color: #0f172a;
        outline: none;
        transition:
            border-color 160ms ease,
            box-shadow 160ms ease;
    }

    .framework-input {
        min-height: 45px;
        padding: 10px 13px;
        font-size: 10px;
    }

    .framework-textarea {
        min-height: 120px;
        resize: vertical;
        padding: 12px 13px;
        font-size: 10px;
        line-height: 1.7;
    }

    .framework-input:focus,
    .framework-textarea:focus {
        border-color: #818cf8;
        box-shadow:
            0 0 0 4px
            rgba(99, 102, 241, 0.1);
    }

    .framework-error {
        color: #dc2626;
        font-size: 8px;
        font-weight: 600;
    }

    .framework-form-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 9px;
        padding-top: 5px;
    }

    .framework-history-list {
        display: grid;
        gap: 12px;
    }

    .framework-history-item {
        display: flex;
        flex-direction: column;
        gap: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 15px;
        padding: 15px;
    }

    .framework-history-title {
        margin: 0;
        color: #1e293b;
        font-size: 11px;
        font-weight: 800;
    }

    .framework-history-meta {
        margin: 5px 0 0;
        color: #94a3b8;
        font-size: 8px;
    }

    .framework-empty {
        display: flex;
        min-height: 230px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px;
        text-align: center;
    }

    .framework-empty-icon {
        display: flex;
        width: 60px;
        height: 60px;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #eef2ff;
        color: #4f46e5;
    }

    .framework-empty-title {
        margin: 15px 0 0;
        color: #334155;
        font-size: 12px;
        font-weight: 800;
    }

    .framework-empty-description {
        max-width: 430px;
        margin: 7px 0 0;
        color: #94a3b8;
        font-size: 9px;
        line-height: 1.7;
    }

    @media (min-width: 700px) {
        .framework-heading {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
        }

        .framework-action-panel {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .framework-form-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .framework-information-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
        }

        .framework-history-item {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }
</style>

<div class="framework-page">
    <section class="framework-heading">
        <div>
            <p class="framework-heading-label">
                Fase Persiapan Magang
            </p>

            <h1 class="framework-heading-title">
                Kerangka Acuan
            </h1>

            <p class="framework-heading-description">
                Susun ruang lingkup pekerjaan, rencana kerja,
                serta ketentuan pelaksanaan magang untuk ditinjau
                oleh Pembimbing Lapangan dan Dosen Pembimbing.
            </p>
        </div>

        <div class="framework-heading-actions">
            <button
                type="button"
                wire:click="toggleHistory"
                class="simmag-secondary-button"
            >
                <span class="material-symbols-rounded">
                    history
                </span>

                {{ $showHistory
                    ? 'Tutup Riwayat'
                    : 'Lihat Riwayat' }}
            </button>
        </div>
    </section>

    @if (! $internship)
        <section class="framework-section">
            <div class="framework-empty">
                <div class="framework-empty-icon">
                    <span class="material-symbols-rounded">
                        work_off
                    </span>
                </div>

                <p class="framework-empty-title">
                    Data magang belum tersedia
                </p>

                <p class="framework-empty-description">
                    Hubungi Administrator agar data magang,
                    instansi, Dosen Pembimbing, dan Pembimbing
                    Lapangan dapat ditentukan.
                </p>
            </div>
        </section>
    @else
        @if ($currentFramework)
            <section class="framework-hero">
                <div class="framework-hero-content">
                    <div class="framework-badges">
                        <span class="framework-version">
                            Versi
                            {{ $currentFramework->version }}
                        </span>

                        <span class="framework-status">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <h2 class="framework-hero-title">
                        {{ $currentFramework->title }}
                    </h2>

                    <p class="framework-hero-description">
                        {{ $currentFramework->description }}
                    </p>

                    <div class="framework-meta">
                        <span class="framework-meta-item">
                            <span class="material-symbols-rounded">
                                person
                            </span>

                            {{ $internship->student?->name
                                ?? auth()->user()?->name }}
                        </span>

                        <span class="framework-meta-item">
                            <span class="material-symbols-rounded">
                                badge
                            </span>

                            {{ $internship->student?->nim
                                ?? $internship->student?->identifier
                                ?? '-' }}
                        </span>

                        <span class="framework-meta-item">
                            <span class="material-symbols-rounded">
                                apartment
                            </span>

                            {{ $internship->company?->name
                                ?? 'Instansi belum ditentukan' }}
                        </span>
                    </div>
                </div>
            </section>
        @endif

        <section class="framework-action-panel">
            <div>
                <h2 class="framework-action-title">
                    Tindakan Kerangka Acuan
                </h2>

                <p class="framework-action-description">
                    Status saat ini:
                    <strong>{{ $statusLabel }}</strong>
                </p>
            </div>

            <div class="framework-action-buttons">
                @if (! $currentFramework)
                    <button
                        type="button"
                        wire:click="startRevision"
                        class="simmag-primary-button"
                    >
                        <span class="material-symbols-rounded">
                            add
                        </span>

                        Buat Kerangka Acuan
                    </button>
                @elseif ($canEdit)
                    <button
                        type="button"
                        wire:click="startRevision"
                        class="simmag-primary-button"
                    >
                        <span class="material-symbols-rounded">
                            edit
                        </span>

                        {{ str_contains(
                            $statusKey,
                            'revisi'
                        )
                            ? 'Perbaiki Kerangka Acuan'
                            : 'Lanjutkan Pengisian' }}
                    </button>
                @elseif ($canCreateNewVersion)
                    <button
                        type="button"
                        wire:click="requestNewVersion"
                        wire:confirm="Buat versi baru dari Kerangka Acuan yang sudah disetujui?"
                        class="simmag-primary-button"
                    >
                        <span class="material-symbols-rounded">
                            difference
                        </span>

                        Buat Versi Baru
                    </button>
                @else
                    <button
                        type="button"
                        disabled
                        class="simmag-secondary-button"
                    >
                        <span class="material-symbols-rounded">
                            hourglass_top
                        </span>

                        Sedang Ditinjau
                    </button>
                @endif
            </div>
        </section>

        @if (
            $currentFramework
            && str_contains(
                $statusKey,
                'revisi'
            )
        )
            <section class="framework-notes">
                @if ($currentFramework->field_supervisor_notes)
                    <div class="framework-note">
                        <span class="material-symbols-rounded">
                            feedback
                        </span>

                        <div>
                            <p class="framework-note-title">
                                Catatan Pembimbing Lapangan
                            </p>

                            <p class="framework-note-description">
                                {{ $currentFramework
                                    ->field_supervisor_notes }}
                            </p>
                        </div>
                    </div>
                @endif

                @if ($currentFramework->lecturer_notes)
                    <div class="framework-note">
                        <span class="material-symbols-rounded">
                            rate_review
                        </span>

                        <div>
                            <p class="framework-note-title">
                                Catatan Dosen Pembimbing
                            </p>

                            <p class="framework-note-description">
                                {{ $currentFramework
                                    ->lecturer_notes }}
                            </p>
                        </div>
                    </div>
                @endif
            </section>
        @endif

        @if ($formOpen)
            <section class="framework-section">
                <div class="framework-section-header">
                    <h2 class="framework-section-title">
                        {{ $currentFramework
                            ? 'Edit Kerangka Acuan'
                            : 'Buat Kerangka Acuan' }}
                    </h2>

                    <p class="framework-section-description">
                        Isi seluruh data dengan lengkap sebelum
                        diajukan kepada pembimbing.
                    </p>
                </div>

                <div class="framework-section-body">
                    <form
                        wire:submit="submitForReview"
                        class="framework-form"
                    >
                        <div class="framework-field">
                            <label class="framework-field-label">
                                Judul Kerangka Acuan
                                <span class="framework-required">
                                    *
                                </span>
                            </label>

                            <input
                                type="text"
                                wire:model="title"
                                class="framework-input"
                                placeholder="Contoh: Pengembangan Sistem Monitoring Internal"
                            >

                            @error('title')
                                <span class="framework-error">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="framework-field">
                            <label class="framework-field-label">
                                Deskripsi Pekerjaan
                                <span class="framework-required">
                                    *
                                </span>
                            </label>

                            <textarea
                                wire:model="description"
                                class="framework-textarea"
                                placeholder="Jelaskan ruang lingkup pekerjaan selama magang"
                            ></textarea>

                            @error('description')
                                <span class="framework-error">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="framework-form-grid">
                            <div class="framework-field">
                                <label class="framework-field-label">
                                    Tanggal Mulai
                                    <span class="framework-required">
                                        *
                                    </span>
                                </label>

                                <input
                                    type="date"
                                    wire:model="startDate"
                                    class="framework-input"
                                >

                                @error('startDate')
                                    <span class="framework-error">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="framework-field">
                                <label class="framework-field-label">
                                    Target Selesai
                                    <span class="framework-required">
                                        *
                                    </span>
                                </label>

                                <input
                                    type="date"
                                    wire:model="targetEndDate"
                                    class="framework-input"
                                >

                                @error('targetEndDate')
                                    <span class="framework-error">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="framework-field">
                            <label class="framework-field-label">
                                Rencana Kerja
                                <span class="framework-required">
                                    *
                                </span>
                            </label>

                            <textarea
                                wire:model="workPlan"
                                class="framework-textarea"
                                placeholder="Tuliskan tahapan dan target pekerjaan"
                            ></textarea>

                            @error('workPlan')
                                <span class="framework-error">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="framework-field">
                            <label class="framework-field-label">
                                Ketentuan Kepemilikan
                            </label>

                            <textarea
                                wire:model="ownershipClause"
                                class="framework-textarea"
                                placeholder="Ketentuan kepemilikan hasil pekerjaan"
                            ></textarea>

                            @error('ownershipClause')
                                <span class="framework-error">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="framework-field">
                            <label class="framework-field-label">
                                Ketentuan Kerahasiaan
                            </label>

                            <textarea
                                wire:model="confidentialityClause"
                                class="framework-textarea"
                                placeholder="Ketentuan mengenai kerahasiaan data"
                            ></textarea>

                            @error('confidentialityClause')
                                <span class="framework-error">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="framework-field">
                            <label class="framework-field-label">
                                Ketentuan Remunerasi
                            </label>

                            <textarea
                                wire:model="remunerationClause"
                                class="framework-textarea"
                                placeholder="Ketentuan uang saku atau remunerasi"
                            ></textarea>

                            @error('remunerationClause')
                                <span class="framework-error">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="framework-form-actions">
                            <button
                                type="button"
                                wire:click="cancelEdit"
                                class="simmag-secondary-button"
                            >
                                Batal
                            </button>

                            <button
                                type="button"
                                wire:click="saveDraft"
                                wire:loading.attr="disabled"
                                class="simmag-secondary-button"
                            >
                                <span class="material-symbols-rounded">
                                    save
                                </span>

                                Simpan Draft
                            </button>

                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="simmag-primary-button"
                            >
                                <span class="material-symbols-rounded">
                                    send
                                </span>

                                {{ str_contains(
                                    $statusKey,
                                    'revisi'
                                )
                                    ? 'Ajukan Ulang'
                                    : 'Ajukan Review' }}
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        @elseif ($currentFramework)
            <section class="framework-detail-grid">
                <article class="framework-section">
                    <div class="framework-section-header">
                        <h2 class="framework-section-title">
                            Detail Pelaksanaan
                        </h2>

                        <p class="framework-section-description">
                            Informasi jadwal dan rencana pekerjaan.
                        </p>
                    </div>

                    <div class="framework-section-body">
                        <div class="framework-information-grid">
                            <div class="framework-information-item">
                                <p class="framework-information-label">
                                    Tanggal Mulai
                                </p>

                                <p class="framework-information-value">
                                    {{ $formatDate(
                                        $currentFramework
                                            ->start_date
                                    ) }}
                                </p>
                            </div>

                            <div class="framework-information-item">
                                <p class="framework-information-label">
                                    Target Selesai
                                </p>

                                <p class="framework-information-value">
                                    {{ $formatDate(
                                        $currentFramework
                                            ->target_end_date
                                    ) }}
                                </p>
                            </div>

                            <div
                                class="framework-information-item"
                                style="grid-column:1/-1;"
                            >
                                <p class="framework-information-label">
                                    Rencana Kerja
                                </p>

                                <p class="framework-information-value">
                                    {{ $currentFramework
                                        ->work_plan
                                        ?: '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="framework-section">
                    <div class="framework-section-header">
                        <h2 class="framework-section-title">
                            Ketentuan Pelaksanaan
                        </h2>

                        <p class="framework-section-description">
                            Ketentuan yang disepakati selama kegiatan magang.
                        </p>
                    </div>

                    <div class="framework-section-body">
                        <div class="framework-information-grid">
                            <div class="framework-information-item">
                                <p class="framework-information-label">
                                    Kepemilikan
                                </p>

                                <p class="framework-information-value">
                                    {{ $currentFramework
                                        ->ownership_clause
                                        ?: '-' }}
                                </p>
                            </div>

                            <div class="framework-information-item">
                                <p class="framework-information-label">
                                    Kerahasiaan
                                </p>

                                <p class="framework-information-value">
                                    {{ $currentFramework
                                        ->confidentiality_clause
                                        ?: '-' }}
                                </p>
                            </div>

                            <div
                                class="framework-information-item"
                                style="grid-column:1/-1;"
                            >
                                <p class="framework-information-label">
                                    Remunerasi
                                </p>

                                <p class="framework-information-value">
                                    {{ $currentFramework
                                        ->remuneration_clause
                                        ?: '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </article>
            </section>
        @endif

        @if ($showHistory)
            <section class="framework-section">
                <div class="framework-section-header">
                    <h2 class="framework-section-title">
                        Riwayat Versi
                    </h2>

                    <p class="framework-section-description">
                        Semua versi Kerangka Acuan tersimpan
                        dan tidak dihapus dari sistem.
                    </p>
                </div>

                <div class="framework-section-body">
                    @if ($history->isNotEmpty())
                        <div class="framework-history-list">
                            @foreach ($history as $framework)
                                @php
                                    $historyStatus =
                                        str(
                                            $framework->status
                                        )
                                            ->replace(
                                                [
                                                    '_',
                                                    '-',
                                                ],
                                                ' '
                                            )
                                            ->title()
                                            ->toString();
                                @endphp

                                <div class="framework-history-item">
                                    <div>
                                        <p class="framework-history-title">
                                            Versi
                                            {{ $framework->version }}
                                            —
                                            {{ $framework->title }}
                                        </p>

                                        <p class="framework-history-meta">
                                            Diperbarui
                                            {{ $framework->updated_at
                                                ?->translatedFormat(
                                                    'd F Y, H.i'
                                                ) }}
                                            WIB
                                        </p>
                                    </div>

                                    <span
                                        class="simmag-status {{ $framework->id === $currentFramework?->id
                                            ? $statusClass
                                            : 'is-neutral' }}"
                                    >
                                        {{ $historyStatus }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="framework-empty">
                            <div class="framework-empty-icon">
                                <span class="material-symbols-rounded">
                                    history
                                </span>
                            </div>

                            <p class="framework-empty-title">
                                Riwayat belum tersedia
                            </p>
                        </div>
                    @endif
                </div>
            </section>
        @endif
    @endif
</div>