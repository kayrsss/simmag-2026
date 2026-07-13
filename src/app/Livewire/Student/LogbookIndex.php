<?php

namespace App\Livewire\Student;

use App\Models\FrameworkOfReference;
use App\Models\Internship;
use App\Models\Logbook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class LogbookIndex extends Component
{
    use WithFileUploads;

    public bool $formOpen = false;

    public bool $detailOpen = false;

    public ?int $editingId = null;

    public ?int $selectedId = null;

    public string $activityDate = '';

    public string $activity = '';

    public $evidence = null;

    public ?string $existingEvidenceName = null;

    public ?string $existingEvidencePath = null;

    public string $statusFilter = 'all';

    public string $search = '';

    public function mount(): void
    {
        $this->activityDate = now()
            ->toDateString();
    }

    private function currentInternship(): ?Internship
    {
        return Internship::query()
            ->with([
                'student',
                'company',
                'period',
                'supervisorLecturer',
            ])
            ->where(
                'student_id',
                Auth::id()
            )
            ->orderByRaw(
                "
                CASE
                    WHEN LOWER(
                        REPLACE(
                            REPLACE(
                                status,
                                ' ',
                                '_'
                            ),
                            '-',
                            '_'
                        )
                    ) IN (
                        'aktif',
                        'magang_aktif',
                        'pelaksanaan',
                        'laporan_akhir'
                    ) THEN 0
                    ELSE 1
                END
                "
            )
            ->latest('id')
            ->first();
    }

    private function approvedFramework(
        Internship $internship
    ): ?FrameworkOfReference {
        return FrameworkOfReference::query()
            ->where(
                'internship_id',
                $internship->id
            )
            ->where(
                'status',
                FrameworkOfReference::STATUS_DISETUJUI
            )
            ->orderByDesc('version')
            ->orderByDesc('id')
            ->first();
    }

    public function openCreate(): void
    {
        $internship = $this->currentInternship();

        if (! $internship) {
            session()->flash(
                'error',
                'Data magang belum tersedia.'
            );

            return;
        }

        $framework = $this->approvedFramework(
            $internship
        );

        if (! $framework) {
            session()->flash(
                'error',
                'Logbook baru dapat dibuat setelah Kerangka Acuan disetujui oleh Pembimbing Lapangan dan Dosen Pembimbing.'
            );

            return;
        }

        $this->resetForm();

        $this->activityDate = now()
            ->toDateString();

        $this->formOpen = true;

        $this->detailOpen = false;
    }

    public function openEdit(int $id): void
    {
        $logbook = $this->findOwnedLogbook($id);

        if (! $logbook) {
            session()->flash(
                'error',
                'Data Logbook tidak ditemukan.'
            );

            return;
        }

        if (! $logbook->canBeEditedByStudent()) {
            session()->flash(
                'error',
                'Logbook hanya dapat diedit saat berstatus Draft atau Perlu Revisi.'
            );

            return;
        }

        $this->editingId = $logbook->id;

        $this->activityDate =
            $logbook->activity_date
                ->format('Y-m-d');

        $this->activity =
            (string) $logbook->activity;

        $this->existingEvidenceName =
            $logbook->evidence_name;

        $this->existingEvidencePath =
            $logbook->evidence_path;

        $this->evidence = null;

        $this->formOpen = true;

        $this->detailOpen = false;

        $this->resetValidation();
    }

    public function openDetail(int $id): void
    {
        $logbook = $this->findOwnedLogbook($id);

        if (! $logbook) {
            session()->flash(
                'error',
                'Data Logbook tidak ditemukan.'
            );

            return;
        }

        $this->selectedId = $logbook->id;

        $this->detailOpen = true;

        $this->formOpen = false;
    }

    public function closeForm(): void
    {
        $this->formOpen = false;

        $this->resetForm();
    }

    public function closeDetail(): void
    {
        $this->detailOpen = false;

        $this->selectedId = null;
    }

    public function saveDraft(): void
    {
        $this->validate(
            $this->draftRules(),
            $this->validationMessages()
        );

        $this->persistLogbook(
            Logbook::STATUS_DRAFT
        );
    }

    public function submitForValidation(): void
    {
        $this->validate(
            $this->submissionRules(),
            $this->validationMessages()
        );

        $this->persistLogbook(
            Logbook::STATUS_WAITING_VALIDATION
        );
    }

    public function deleteDraft(int $id): void
    {
        $logbook = $this->findOwnedLogbook($id);

        if (! $logbook) {
            session()->flash(
                'error',
                'Data Logbook tidak ditemukan.'
            );

            return;
        }

        if (
            $logbook->status
            !== Logbook::STATUS_DRAFT
        ) {
            session()->flash(
                'error',
                'Hanya Logbook berstatus Draft yang dapat dihapus.'
            );

            return;
        }

        DB::transaction(
            function () use ($logbook): void {
                if (
                    filled(
                        $logbook->evidence_path
                    )
                    && Storage::disk('public')
                        ->exists(
                            $logbook->evidence_path
                        )
                ) {
                    Storage::disk('public')
                        ->delete(
                            $logbook->evidence_path
                        );
                }

                $logbook->delete();
            }
        );

        session()->flash(
            'success',
            'Draft Logbook berhasil dihapus.'
        );
    }

    private function persistLogbook(
        string $status
    ): void {
        $internship = $this->currentInternship();

        if (! $internship) {
            session()->flash(
                'error',
                'Data magang belum tersedia.'
            );

            return;
        }

        $framework = $this->approvedFramework(
            $internship
        );

        if (! $framework) {
            session()->flash(
                'error',
                'Logbook hanya dapat dibuat setelah Kerangka Acuan disetujui.'
            );

            return;
        }

        $logbook = $this->editingId
            ? $this->findOwnedLogbook(
                $this->editingId
            )
            : new Logbook();

        if (! $logbook) {
            session()->flash(
                'error',
                'Data Logbook tidak ditemukan.'
            );

            return;
        }

        if (
            $logbook->exists
            && ! $logbook
                ->canBeEditedByStudent()
        ) {
            session()->flash(
                'error',
                'Logbook tersebut tidak dapat diubah.'
            );

            return;
        }

        DB::transaction(
            function () use (
                $logbook,
                $status,
                $internship,
                $framework
            ): void {
                $evidenceName =
                    $logbook->evidence_name;

                $evidencePath =
                    $logbook->evidence_path;

                if ($this->evidence) {
                    $newEvidenceName =
                        $this->evidence
                            ->getClientOriginalName();

                    $newEvidencePath =
                        $this->evidence
                            ->store(
                                'logbooks/'
                                . Auth::id(),
                                'public'
                            );

                    if (
                        filled($evidencePath)
                        && Storage::disk('public')
                            ->exists($evidencePath)
                    ) {
                        Storage::disk('public')
                            ->delete($evidencePath);
                    }

                    $evidenceName =
                        $newEvidenceName;

                    $evidencePath =
                        $newEvidencePath;
                }

                $logbook->fill([
                    'internship_id' =>
                        $internship->id,

                    'framework_of_reference_id' =>
                        $framework->id,

                    'student_id' =>
                        Auth::id(),

                    'activity_date' =>
                        $this->activityDate,

                    'activity' =>
                        trim($this->activity),

                    'evidence_name' =>
                        $evidenceName,

                    'evidence_path' =>
                        $evidencePath,

                    'status' =>
                        $status,

                    'review_note' =>
                        $status ===
                        Logbook::STATUS_WAITING_VALIDATION
                            ? null
                            : $logbook->review_note,

                    'submitted_at' =>
                        $status ===
                        Logbook::STATUS_WAITING_VALIDATION
                            ? now()
                            : null,

                    'validated_at' =>
                        null,

                    'validated_by' =>
                        null,
                ]);

                $logbook->save();
            }
        );

        $this->formOpen = false;

        $this->resetForm();

        session()->flash(
            'success',
            $status === Logbook::STATUS_DRAFT
                ? 'Draft Logbook berhasil disimpan.'
                : 'Logbook berhasil dikirim kepada Pembimbing Lapangan untuk divalidasi.'
        );
    }

    private function draftRules(): array
    {
        return [
            'activityDate' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'activity' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'evidence' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:20480',
            ],
        ];
    }

    private function submissionRules(): array
    {
        $hasEvidence =
            filled(
                $this->existingEvidencePath
            )
            || filled($this->evidence);

        return [
            'activityDate' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'activity' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'evidence' => [
                $hasEvidence
                    ? 'nullable'
                    : 'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:20480',
            ],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'activityDate.required' =>
                'Tanggal kegiatan wajib diisi.',

            'activityDate.date' =>
                'Tanggal kegiatan tidak valid.',

            'activityDate.before_or_equal' =>
                'Tanggal kegiatan tidak boleh melebihi hari ini.',

            'activity.required' =>
                'Uraian aktivitas wajib diisi.',

            'activity.min' =>
                'Uraian aktivitas minimal 10 karakter.',

            'activity.max' =>
                'Uraian aktivitas maksimal 5.000 karakter.',

            'evidence.required' =>
                'Bukti pendukung wajib diunggah sebelum Logbook dikirim.',

            'evidence.file' =>
                'Bukti pendukung harus berupa file.',

            'evidence.mimes' =>
                'Bukti hanya boleh berupa PDF, JPG, JPEG, atau PNG.',

            'evidence.max' =>
                'Ukuran bukti maksimal 20 MB.',
        ];
    }

    private function findOwnedLogbook(
        int $id
    ): ?Logbook {
        $internship = $this->currentInternship();

        if (! $internship) {
            return null;
        }

        return Logbook::query()
            ->where(
                'student_id',
                Auth::id()
            )
            ->where(
                'internship_id',
                $internship->id
            )
            ->find($id);
    }

    private function resetForm(): void
    {
        $this->editingId = null;

        $this->activityDate = now()
            ->toDateString();

        $this->activity = '';

        $this->evidence = null;

        $this->existingEvidenceName =
            null;

        $this->existingEvidencePath =
            null;

        $this->resetValidation();
    }

    private function logbookQuery(): Builder
    {
        $internship = $this->currentInternship();

        return Logbook::query()
            ->with([
                'frameworkOfReference',
                'validator',
            ])
            ->where(
                'student_id',
                Auth::id()
            )
            ->when(
                $internship,
                fn (Builder $query) =>
                    $query->where(
                        'internship_id',
                        $internship->id
                    ),
                fn (Builder $query) =>
                    $query->whereRaw('1 = 0')
            );
    }

    public function render()
    {
        $internship = $this->currentInternship();

        $approvedFramework = $internship
            ? $this->approvedFramework(
                $internship
            )
            : null;

        $query = $this->logbookQuery();

        if ($this->statusFilter !== 'all') {
            $query->where(
                'status',
                $this->statusFilter
            );
        }

        if (trim($this->search) !== '') {
            $keyword =
                '%' . trim($this->search) . '%';

            $query->where(
                function (
                    Builder $builder
                ) use ($keyword): void {
                    $builder
                        ->where(
                            'activity',
                            'like',
                            $keyword
                        )
                        ->orWhere(
                            'evidence_name',
                            'like',
                            $keyword
                        );
                }
            );
        }

        $logbooks = $query
            ->orderByDesc('activity_date')
            ->orderByDesc('id')
            ->get();

        $statisticsQuery =
            $this->logbookQuery();

        $statistics = [
            'total' =>
                (clone $statisticsQuery)
                    ->count(),

            'draft' =>
                (clone $statisticsQuery)
                    ->where(
                        'status',
                        Logbook::STATUS_DRAFT
                    )
                    ->count(),

            'waiting' =>
                (clone $statisticsQuery)
                    ->where(
                        'status',
                        Logbook::STATUS_WAITING_VALIDATION
                    )
                    ->count(),

            'validated' =>
                (clone $statisticsQuery)
                    ->where(
                        'status',
                        Logbook::STATUS_VALIDATED
                    )
                    ->count(),

            'revision' =>
                (clone $statisticsQuery)
                    ->where(
                        'status',
                        Logbook::STATUS_REVISION_REQUIRED
                    )
                    ->count(),
        ];

        $selectedLogbook =
            $this->selectedId
                ? $this->findOwnedLogbook(
                    $this->selectedId
                )
                : null;

        return view(
            'livewire.student.logbook-index',
            [
                'internship' =>
                    $internship,

                'approvedFramework' =>
                    $approvedFramework,

                'logbooks' =>
                    $logbooks,

                'statistics' =>
                    $statistics,

                'selectedLogbook' =>
                    $selectedLogbook,
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    'Logbook Harian',

                'role' =>
                    'mahasiswa',
            ]
        );
    }
}