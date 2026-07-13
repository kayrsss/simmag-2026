@php
    $user = auth()->user();

    $studentName = $user?->name
        ?? 'Mahasiswa SIMMAG';

    $studentNim = $user?->nim
        ?? $user?->username
        ?? '-';

    $statusClasses = [
        'Draft' =>
            'bg-slate-100 text-slate-700',

        'Menunggu_Validasi' =>
            'bg-amber-50 text-amber-700',

        'Tervalidasi' =>
            'bg-emerald-50 text-emerald-700',

        'Perlu_Revisi' =>
            'bg-red-50 text-red-700',
    ];
@endphp

<div class="space-y-5 sm:space-y-6">
    <section
        class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between"
    >
        <div>
            <span
                class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-blue-600"
            >
                Fase Pelaksanaan Magang
            </span>

            <h1
                class="mt-2 font-display text-3xl font-extrabold text-slate-900 sm:text-4xl"
            >
                Logbook Harian
            </h1>

            <p
                class="mt-3 max-w-3xl text-sm leading-6 text-slate-500"
            >
                Catat aktivitas harian dan unggah bukti
                pendukung untuk divalidasi oleh Pembimbing
                Lapangan.
            </p>
        </div>

        <button
            wire:click="openCreate"
            wire:loading.attr="disabled"
            type="button"
            class="inline-flex min-h-11 w-fit items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-violet-600 px-5 text-sm font-bold text-white shadow-lg shadow-blue-200 transition hover:opacity-90 disabled:opacity-60"
        >
            <span
                class="material-symbols-rounded text-[20px]"
            >
                add
            </span>

            Tambah Logbook
        </button>
    </section>

    @if (session('success'))
        <section
            class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4"
        >
            <span
                class="material-symbols-rounded text-emerald-600"
            >
                check_circle
            </span>

            <p
                class="text-sm font-semibold text-emerald-700"
            >
                {{ session('success') }}
            </p>
        </section>
    @endif

    @if (session('error'))
        <section
            class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4"
        >
            <span
                class="material-symbols-rounded text-red-600"
            >
                error
            </span>

            <p
                class="text-sm font-semibold text-red-700"
            >
                {{ session('error') }}
            </p>
        </section>
    @endif

    <section
        class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-blue-600 to-violet-700 p-6 text-white shadow-panel sm:p-8"
    >
        <div
            class="absolute -right-24 -top-28 h-80 w-80 rounded-full border-[64px] border-white/5"
        ></div>

        <div
            class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
        >
            <div class="max-w-3xl">
                <div class="flex flex-wrap gap-3">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[10px] font-extrabold uppercase backdrop-blur"
                    >
                        <span
                            class="h-2 w-2 rounded-full bg-emerald-300"
                        ></span>

                        Magang Aktif
                    </span>

                    <span
                        class="inline-flex rounded-full bg-white px-3 py-1.5 text-[10px] font-extrabold uppercase text-blue-700"
                    >
                        Kerangka Acuan Disetujui
                    </span>
                </div>

                <h2
                    class="mt-5 font-display text-2xl font-extrabold sm:text-3xl"
                >
                    Logbook {{ $studentName }}
                </h2>

                <p
                    class="mt-3 max-w-2xl text-sm leading-6 text-blue-100"
                >
                    Logbook berisi tanggal kegiatan, uraian
                    aktivitas, dan bukti pendukung. Tidak ada
                    input durasi kegiatan.
                </p>

                <div
                    class="mt-5 flex flex-wrap gap-3"
                >
                    <span
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-xs font-semibold"
                    >
                        <span
                            class="material-symbols-rounded text-[18px]"
                        >
                            badge
                        </span>

                        {{ $studentNim }}
                    </span>

                    <span
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-xs font-semibold"
                    >
                        <span
                            class="material-symbols-rounded text-[18px]"
                        >
                            apartment
                        </span>

                        PT Teknologi Jaya
                    </span>

                    <span
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-xs font-semibold"
                    >
                        <span
                            class="material-symbols-rounded text-[18px]"
                        >
                            person
                        </span>

                        Budi Santoso
                    </span>
                </div>
            </div>

            <div
                class="hidden h-32 w-32 rotate-6 items-center justify-center rounded-[32px] border border-white/15 bg-white/10 lg:flex"
            >
                <span
                    class="material-symbols-rounded -rotate-6 text-[68px]"
                >
                    edit_note
                </span>
            </div>
        </div>
    </section>

    <section
        class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-5"
    >
        @foreach ([
            [
                'label' => 'Total Logbook',
                'value' => $statistics['total'],
                'icon' => 'library_books',
                'class' => 'bg-blue-50 text-blue-600',
            ],
            [
                'label' => 'Draft',
                'value' => $statistics['draft'],
                'icon' => 'draft',
                'class' => 'bg-slate-100 text-slate-600',
            ],
            [
                'label' => 'Menunggu Validasi',
                'value' => $statistics['waiting'],
                'icon' => 'pending_actions',
                'class' => 'bg-amber-50 text-amber-600',
            ],
            [
                'label' => 'Tervalidasi',
                'value' => $statistics['validated'],
                'icon' => 'task_alt',
                'class' => 'bg-emerald-50 text-emerald-600',
            ],
            [
                'label' => 'Perlu Revisi',
                'value' => $statistics['revision'],
                'icon' => 'edit_document',
                'class' => 'bg-red-50 text-red-600',
            ],
        ] as $stat)
            <article
                class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card"
            >
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl {{ $stat['class'] }}"
                >
                    <span class="material-symbols-rounded">
                        {{ $stat['icon'] }}
                    </span>
                </div>

                <p
                    class="mt-4 text-[10px] font-bold text-slate-500"
                >
                    {{ $stat['label'] }}
                </p>

                <p
                    class="mt-1 font-display text-xl font-extrabold text-slate-900"
                >
                    {{ $stat['value'] }}
                </p>
            </article>
        @endforeach
    </section>

    <section
        class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-card"
    >
        <div
            class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6"
        >
            <div>
                <span
                    class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-blue-600"
                >
                    Riwayat Logbook
                </span>

                <h2
                    class="mt-2 font-display text-lg font-bold text-slate-900"
                >
                    Daftar Aktivitas Harian
                </h2>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">
                <div
                    class="flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3"
                >
                    <span
                        class="material-symbols-rounded text-[19px] text-slate-400"
                    >
                        search
                    </span>

                    <input
                        wire:model.live.debounce.300ms="search"
                        type="search"
                        placeholder="Cari aktivitas..."
                        class="w-full border-0 bg-transparent p-0 text-xs focus:ring-0 sm:w-44"
                    >
                </div>

                <select
                    wire:model.live="statusFilter"
                    class="min-h-10 rounded-xl border-slate-200 text-xs"
                >
                    <option value="all">
                        Semua Status
                    </option>

                    <option value="Draft">
                        Draft
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
        </div>

        <div class="overflow-x-auto">
            <table
                class="w-full min-w-[900px] border-collapse"
            >
                <thead>
                    <tr class="bg-slate-50">
                        <th
                            class="px-5 py-3 text-left text-[10px] font-extrabold uppercase text-slate-400"
                        >
                            Tanggal
                        </th>

                        <th
                            class="px-5 py-3 text-left text-[10px] font-extrabold uppercase text-slate-400"
                        >
                            Aktivitas
                        </th>

                        <th
                            class="px-5 py-3 text-left text-[10px] font-extrabold uppercase text-slate-400"
                        >
                            Bukti
                        </th>

                        <th
                            class="px-5 py-3 text-left text-[10px] font-extrabold uppercase text-slate-400"
                        >
                            Status
                        </th>

                        <th
                            class="px-5 py-3 text-right text-[10px] font-extrabold uppercase text-slate-400"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($logbooks as $logbook)
                        <tr
                            wire:key="logbook-{{ $logbook->id }}"
                            class="hover:bg-slate-50"
                        >
                            <td
                                class="px-5 py-4 text-xs font-bold text-slate-700"
                            >
                                {{ $logbook->activity_date->translatedFormat('d F Y') }}
                            </td>

                            <td class="px-5 py-4">
                                <p
                                    class="max-w-[400px] text-xs leading-5 text-slate-600"
                                >
                                    {{ \Illuminate\Support\Str::limit(
                                        $logbook->activity,
                                        140
                                    ) }}
                                </p>

                                @if ($logbook->review_note)
                                    <p
                                        class="mt-2 text-[9px] leading-4 text-red-600"
                                    >
                                        Catatan:
                                        {{ $logbook->review_note }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                @if ($logbook->evidence_name)
                                    <span
                                        class="inline-flex max-w-[190px] items-center gap-2 rounded-xl bg-blue-50 px-3 py-2 text-blue-700"
                                    >
                                        <span
                                            class="material-symbols-rounded text-[17px]"
                                        >
                                            attachment
                                        </span>

                                        <span
                                            class="truncate text-[9px] font-semibold"
                                        >
                                            {{ $logbook->evidence_name }}
                                        </span>
                                    </span>
                                @else
                                    <span
                                        class="text-[10px] text-slate-400"
                                    >
                                        Belum ada file
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-[9px] font-extrabold uppercase {{ $statusClasses[$logbook->status] ?? 'bg-slate-100 text-slate-700' }}"
                                >
                                    {{ str_replace(
                                        '_',
                                        ' ',
                                        $logbook->status
                                    ) }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <button
                                        wire:click="openDetail({{ $logbook->id }})"
                                        type="button"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-blue-50 hover:text-blue-600"
                                    >
                                        <span
                                            class="material-symbols-rounded text-[19px]"
                                        >
                                            visibility
                                        </span>
                                    </button>

                                    @if ($logbook->canBeEditedByStudent())
                                        <button
                                            wire:click="openEdit({{ $logbook->id }})"
                                            type="button"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-violet-50 hover:text-violet-600"
                                        >
                                            <span
                                                class="material-symbols-rounded text-[19px]"
                                            >
                                                edit
                                            </span>
                                        </button>
                                    @endif

                                    @if ($logbook->status === 'Draft')
                                        <button
                                            wire:click="deleteDraft({{ $logbook->id }})"
                                            wire:confirm="Hapus draft logbook ini?"
                                            type="button"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-red-200 text-red-500 hover:bg-red-50"
                                        >
                                            <span
                                                class="material-symbols-rounded text-[19px]"
                                            >
                                                delete
                                            </span>
                                        </button>
                                    @endif

                                    @if ($logbook->status === 'Tervalidasi')
                                        <span
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"
                                            title="Read-only"
                                        >
                                            <span
                                                class="material-symbols-rounded text-[19px]"
                                            >
                                                lock
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="px-5 py-16 text-center"
                            >
                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"
                                >
                                    <span
                                        class="material-symbols-rounded text-[28px]"
                                    >
                                        edit_note
                                    </span>
                                </div>

                                <p
                                    class="mt-4 text-sm font-bold text-slate-700"
                                >
                                    Belum ada logbook
                                </p>

                                <p
                                    class="mt-2 text-xs text-slate-400"
                                >
                                    Tekan tombol Tambah Logbook
                                    untuk membuat aktivitas pertama.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Modal Tambah/Edit --}}
    @if ($formOpen)
        <div
            wire:click.self="closeForm"
            class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-slate-950/55 p-4 backdrop-blur-sm"
        >
            <form
                wire:submit="submitForValidation"
                class="my-6 w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl"
            >
                <div
                    class="flex items-start justify-between border-b border-slate-100 p-5 sm:p-6"
                >
                    <div>
                        <span
                            class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-blue-600"
                        >
                            Form Logbook Harian
                        </span>

                        <h2
                            class="mt-2 font-display text-xl font-bold text-slate-900"
                        >
                            {{ $editingId
                                ? 'Perbaiki Logbook'
                                : 'Tambah Logbook Baru' }}
                        </h2>
                    </div>

                    <button
                        wire:click="closeForm"
                        type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50"
                    >
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>
                </div>

                <div class="space-y-5 p-5 sm:p-6">
                    <div>
                        <label
                            for="activity-date"
                            class="mb-2 block text-sm font-bold text-slate-700"
                        >
                            Tanggal Kegiatan
                        </label>

                        <input
                            wire:model="activityDate"
                            id="activity-date"
                            type="date"
                            max="{{ now()->toDateString() }}"
                            class="min-h-12 w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-blue-500 focus:ring-blue-500"
                        >

                        @error('activityDate')
                            <p
                                class="mt-2 text-xs font-semibold text-red-600"
                            >
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="activity"
                            class="mb-2 block text-sm font-bold text-slate-700"
                        >
                            Uraian Aktivitas
                        </label>

                        <textarea
                            wire:model="activity"
                            id="activity"
                            rows="6"
                            class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm leading-7 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Jelaskan aktivitas yang dikerjakan..."
                        ></textarea>

                        @error('activity')
                            <p
                                class="mt-2 text-xs font-semibold text-red-600"
                            >
                                {{ $message }}
                            </p>
                        @else
                            <p
                                class="mt-2 text-[10px] text-slate-400"
                            >
                                Tidak perlu mengisi durasi kegiatan.
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="evidence"
                            class="mb-2 block text-sm font-bold text-slate-700"
                        >
                            Bukti Pendukung
                        </label>

                        <label
                            for="evidence"
                            class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-8 text-center hover:border-blue-300 hover:bg-blue-50"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
                            >
                                <span class="material-symbols-rounded">
                                    upload_file
                                </span>
                            </div>

                            <p
                                class="mt-3 text-sm font-bold text-slate-800"
                            >
                                Pilih bukti pendukung
                            </p>

                            <p
                                class="mt-1 text-xs text-slate-500"
                            >
                                PDF, JPG, JPEG, PNG — maksimal 20 MB
                            </p>

                            <input
                                wire:model="evidence"
                                id="evidence"
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="hidden"
                            >
                        </label>

                        <div
                            wire:loading
                            wire:target="evidence"
                            class="mt-3 flex items-center gap-2 text-xs font-semibold text-blue-600"
                        >
                            <span
                                class="material-symbols-rounded animate-spin text-[18px]"
                            >
                                progress_activity
                            </span>

                            Mengunggah file...
                        </div>

                        @if ($evidence)
                            <div
                                class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3"
                            >
                                <p
                                    class="truncate text-xs font-bold text-emerald-800"
                                >
                                    {{ $evidence->getClientOriginalName() }}
                                </p>
                            </div>
                        @elseif ($existingEvidenceName)
                            <div
                                class="mt-3 rounded-xl border border-blue-200 bg-blue-50 p-3"
                            >
                                <p
                                    class="truncate text-xs font-bold text-blue-800"
                                >
                                    {{ $existingEvidenceName }}
                                </p>
                            </div>
                        @endif

                        @error('evidence')
                            <p
                                class="mt-2 text-xs font-semibold text-red-600"
                            >
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div
                        class="rounded-2xl border border-amber-100 bg-amber-50 p-4"
                    >
                        <p
                            class="text-[11px] leading-5 text-amber-800"
                        >
                            Setelah dikirim, logbook tidak dapat
                            diubah sampai Pembimbing Lapangan
                            memberikan status Perlu Revisi.
                        </p>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-slate-100 p-5 sm:flex-row sm:justify-end sm:p-6"
                >
                    <button
                        wire:click="closeForm"
                        type="button"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 px-5 text-sm font-bold text-slate-600 hover:bg-slate-50"
                    >
                        Batal
                    </button>

                    <button
                        wire:click="saveDraft"
                        type="button"
                        wire:loading.attr="disabled"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-5 text-sm font-bold text-blue-700 hover:bg-blue-100 disabled:opacity-60"
                    >
                        <span
                            class="material-symbols-rounded text-[19px]"
                        >
                            save
                        </span>

                        Simpan Draft
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submitForValidation"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-violet-600 px-5 text-sm font-bold text-white shadow-lg shadow-blue-200 disabled:opacity-60"
                    >
                        <span
                            wire:loading.remove
                            wire:target="submitForValidation"
                            class="material-symbols-rounded text-[19px]"
                        >
                            send
                        </span>

                        <span
                            wire:loading.remove
                            wire:target="submitForValidation"
                        >
                            Kirim untuk Validasi
                        </span>

                        <span
                            wire:loading
                            wire:target="submitForValidation"
                        >
                            Mengirim...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Modal Detail --}}
    @if ($detailOpen && $selectedLogbook)
        <div
            wire:click.self="closeDetail"
            class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-slate-950/55 p-4 backdrop-blur-sm"
        >
            <div
                class="my-6 w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl"
            >
                <div
                    class="flex items-start justify-between border-b border-slate-100 p-5 sm:p-6"
                >
                    <div>
                        <span
                            class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-blue-600"
                        >
                            Detail Logbook
                        </span>

                        <h2
                            class="mt-2 font-display text-xl font-bold text-slate-900"
                        >
                            {{ $selectedLogbook->activity_date->translatedFormat('d F Y') }}
                        </h2>
                    </div>

                    <button
                        wire:click="closeDetail"
                        type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50"
                    >
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>
                </div>

                <div class="space-y-5 p-5 sm:p-6">
                    <span
                        class="inline-flex rounded-full px-3 py-1.5 text-[9px] font-extrabold uppercase {{ $statusClasses[$selectedLogbook->status] ?? 'bg-slate-100 text-slate-700' }}"
                    >
                        {{ str_replace(
                            '_',
                            ' ',
                            $selectedLogbook->status
                        ) }}
                    </span>

                    @if ($selectedLogbook->review_note)
                        <div
                            class="rounded-2xl border border-red-200 bg-red-50 p-4"
                        >
                            <p
                                class="text-xs font-bold text-red-900"
                            >
                                Catatan Pembimbing Lapangan
                            </p>

                            <p
                                class="mt-2 text-sm leading-6 text-red-800"
                            >
                                {{ $selectedLogbook->review_note }}
                            </p>
                        </div>
                    @endif

                    <div>
                        <p
                            class="text-[10px] font-extrabold uppercase text-slate-400"
                        >
                            Uraian Aktivitas
                        </p>

                        <p
                            class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600"
                        >
                            {{ $selectedLogbook->activity }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="text-[10px] font-extrabold uppercase text-slate-400"
                        >
                            Bukti Pendukung
                        </p>

                        @if ($selectedLogbook->evidence_path)
                            <a
                                href="{{ \Illuminate\Support\Facades\Storage::url(
                                    $selectedLogbook->evidence_path
                                ) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-3 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-blue-50"
                            >
                                <span
                                    class="material-symbols-rounded text-blue-600"
                                >
                                    attachment
                                </span>

                                <span
                                    class="min-w-0 flex-1 truncate text-xs font-bold text-slate-800"
                                >
                                    {{ $selectedLogbook->evidence_name }}
                                </span>

                                <span
                                    class="material-symbols-rounded text-slate-400"
                                >
                                    open_in_new
                                </span>
                            </a>
                        @else
                            <p
                                class="mt-3 text-xs text-slate-400"
                            >
                                Belum ada bukti pendukung.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>