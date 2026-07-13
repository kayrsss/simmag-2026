@php
    $formatDate = function (mixed $value): string {
        if (blank($value)) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($value)
                ->translatedFormat('d F Y, H.i');
        } catch (\Throwable) {
            return '-';
        }
    };
@endphp

<style>
    .announcement-page {
        display: grid;
        gap: 22px;
    }

    .announcement-title {
        margin: 0;
        color: #0f172a;
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: clamp(25px, 3vw, 32px);
        font-weight: 800;
        letter-spacing: -0.035em;
    }

    .announcement-description {
        max-width: 720px;
        margin: 7px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.75;
    }

    .announcement-search {
        display: flex;
        width: 100%;
        min-height: 42px;
        align-items: center;
        gap: 9px;
        padding: 0 13px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
    }

    .announcement-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: #334155;
        font-size: 11px;
    }

    .announcement-list {
        display: grid;
        gap: 14px;
    }

    .announcement-card {
        padding: 20px;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.035);
    }

    .announcement-card-title {
        margin: 0;
        color: #1e293b;
        font-size: 14px;
        font-weight: 800;
    }

    .announcement-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 9px;
    }

    .announcement-content {
        margin: 14px 0 0;
        color: #64748b;
        font-size: 11px;
        line-height: 1.8;
        white-space: pre-line;
    }

    .announcement-pagination {
        margin-top: 18px;
    }

    @media (min-width: 640px) {
        .announcement-search {
            width: 340px;
        }
    }
</style>

<div class="announcement-page">
    <section>
        <h1 class="announcement-title">
            Pengumuman
        </h1>

        <p class="announcement-description">
            Informasi resmi mengenai jadwal, pelaksanaan,
            bimbingan, penilaian, dan kegiatan magang.
        </p>
    </section>

    <section class="simmag-card">
        <div class="simmag-card-header">
            <div>
                <h2 class="simmag-card-title">
                    Daftar Pengumuman
                </h2>

                <p class="simmag-card-description">
                    Data diambil langsung dari database SIMMAG.
                </p>
            </div>
        </div>

        <div
            style="
                padding:0 20px 20px;
            "
        >
            <div class="announcement-search">
                <span class="material-symbols-rounded">
                    search
                </span>

                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="Cari pengumuman..."
                >
            </div>
        </div>
    </section>

    @if ($announcements->isNotEmpty())
        <section class="announcement-list">
            @foreach ($announcements as $announcement)
                <article
                    wire:key="announcement-{{ $announcement->id }}"
                    class="announcement-card"
                >
                    <h2 class="announcement-card-title">
                        {{ $announcement->title }}
                    </h2>

                    <div class="announcement-meta">
                        <span class="simmag-status is-primary">
                            {{ $announcement->audience
                                ?? 'Semua Pengguna' }}
                        </span>

                        <span class="simmag-status is-neutral">
                            {{ $formatDate(
                                $announcement->published_at
                            ) }}
                        </span>
                    </div>

                    <p class="announcement-content">
                        {{ $announcement->content }}
                    </p>
                </article>
            @endforeach
        </section>

        @if ($announcements->hasPages())
            <div class="announcement-pagination">
                {{ $announcements->links() }}
            </div>
        @endif
    @else
        <section class="simmag-card">
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
                    Pengumuman resmi akan tampil pada halaman ini.
                </p>
            </div>
        </section>
    @endif
</div>