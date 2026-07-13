<?php

namespace App\Livewire\Student;

use App\Models\FrameworkOfReference;
use App\Models\Internship;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class FrameworkIndex extends Component
{
    public bool $showHistory = false;

    public bool $formOpen = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $description = '';

    public string $startDate = '';

    public string $targetEndDate = '';

    public string $workPlan = '';

    public string $ownershipClause = '';

    public string $confidentialityClause = '';

    public string $remunerationClause = '';

    public function mount(): void
    {
        $internship = $this->currentInternship();

        if (! $internship) {
            return;
        }

        $framework = $this->latestFramework(
            $internship
        );

        /*
        |--------------------------------------------------------------------------
        | Kerangka Acuan belum ada
        |--------------------------------------------------------------------------
        |
        | Form langsung dibuka supaya mahasiswa tidak perlu menekan tombol
        | "Buat Kerangka Acuan".
        |
        */

        if (! $framework) {
            $this->clearForm();

            $this->startDate = filled(
                $internship->started_at
            )
                ? Carbon::parse(
                    $internship->started_at
                )->format('Y-m-d')
                : now()->format('Y-m-d');

            $this->targetEndDate = filled(
                $internship->ended_at
            )
                ? Carbon::parse(
                    $internship->ended_at
                )->format('Y-m-d')
                : now()
                    ->addMonths(3)
                    ->format('Y-m-d');

            $this->formOpen = true;

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Kerangka Acuan masih dapat diedit
        |--------------------------------------------------------------------------
        */

        if (
            $this->isEditableStatus(
                $framework->status
            )
        ) {
            $this->fillForm(
                $framework
            );

            if (
                str_contains(
                    $this->normalizeStatus(
                        $framework->status
                    ),
                    'revisi'
                )
            ) {
                $this->formOpen = true;
            }
        }
    }

    protected function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
                'min:10',
                'max:10000',
            ],

            'startDate' => [
                'required',
                'date',
            ],

            'targetEndDate' => [
                'required',
                'date',
                'after_or_equal:startDate',
            ],

            'workPlan' => [
                'required',
                'string',
                'min:10',
                'max:20000',
            ],

            'ownershipClause' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'confidentialityClause' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'remunerationClause' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'title' =>
                'judul Kerangka Acuan',

            'description' =>
                'deskripsi pekerjaan',

            'startDate' =>
                'tanggal mulai',

            'targetEndDate' =>
                'target tanggal selesai',

            'workPlan' =>
                'rencana kerja',

            'ownershipClause' =>
                'ketentuan kepemilikan',

            'confidentialityClause' =>
                'ketentuan kerahasiaan',

            'remunerationClause' =>
                'ketentuan remunerasi',
        ];
    }

    private function currentInternship(): ?Internship
    {
        return Internship::query()
            ->with([
                'student.programStudy',
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

    private function latestFramework(
        Internship $internship
    ): ?FrameworkOfReference {
        return FrameworkOfReference::query()
            ->where(
                'internship_id',
                $internship->id
            )
            ->orderByDesc('version')
            ->orderByDesc('id')
            ->first();
    }

    private function normalizeStatus(
        ?string $status
    ): string {
        return Str::of(
            (string) $status
        )
            ->lower()
            ->replace(
                [
                    ' ',
                    '-',
                ],
                '_'
            )
            ->toString();
    }

    private function isEditableStatus(
        ?string $status
    ): bool {
        return in_array(
            $this->normalizeStatus(
                $status
            ),
            [
                'draft',
                'perlu_revisi',
                'revisi',
            ],
            true
        );
    }

    private function isApprovedStatus(
        ?string $status
    ): bool {
        return in_array(
            $this->normalizeStatus(
                $status
            ),
            [
                'disetujui',
                'approved',
                'selesai',
            ],
            true
        );
    }

    private function statusLabel(
        ?string $status
    ): string {
        if (blank($status)) {
            return 'Belum Dibuat';
        }

        return Str::of(
            (string) $status
        )
            ->replace(
                [
                    '_',
                    '-',
                ],
                ' '
            )
            ->lower()
            ->title()
            ->toString();
    }

    private function statusTone(
        ?string $status
    ): string {
        $normalized = $this->normalizeStatus(
            $status
        );

        if (
            str_contains(
                $normalized,
                'revisi'
            )
        ) {
            return 'danger';
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
            || $normalized ===
                'disetujui_pl'
        ) {
            return 'warning';
        }

        if (
            str_contains(
                $normalized,
                'disetujui'
            )
            || str_contains(
                $normalized,
                'approved'
            )
        ) {
            return 'success';
        }

        if (
            str_contains(
                $normalized,
                'draft'
            )
        ) {
            return 'neutral';
        }

        return 'primary';
    }

    private function fillForm(
        FrameworkOfReference $framework
    ): void {
        $this->editingId =
            $framework->id;

        $this->title =
            (string) $framework->title;

        $this->description =
            (string) $framework->description;

        $this->startDate =
            filled($framework->start_date)
                ? Carbon::parse(
                    $framework->start_date
                )->format('Y-m-d')
                : '';

        $this->targetEndDate =
            filled($framework->target_end_date)
                ? Carbon::parse(
                    $framework->target_end_date
                )->format('Y-m-d')
                : '';

        $this->workPlan =
            (string) $framework->work_plan;

        $this->ownershipClause =
            (string) $framework
                ->ownership_clause;

        $this->confidentialityClause =
            (string) $framework
                ->confidentiality_clause;

        $this->remunerationClause =
            (string) $framework
                ->remuneration_clause;
    }

    private function clearForm(): void
    {
        $this->reset(
            'editingId',
            'title',
            'description',
            'startDate',
            'targetEndDate',
            'workPlan',
            'ownershipClause',
            'confidentialityClause',
            'remunerationClause'
        );

        $this->resetValidation();
    }

    public function toggleHistory(): void
    {
        $this->showHistory =
            ! $this->showHistory;
    }

    public function startRevision(): void
    {
        $internship = $this->currentInternship();

        if (! $internship) {
            session()->flash(
                'error',
                'Data magang belum tersedia.'
            );

            return;
        }

        $framework = $this->latestFramework(
            $internship
        );

        if (
            $framework
            && ! $this->isEditableStatus(
                $framework->status
            )
        ) {
            session()->flash(
                'error',
                'Kerangka Acuan sedang ditinjau atau sudah disetujui.'
            );

            return;
        }

        if ($framework) {
            $this->fillForm(
                $framework
            );
        } else {
            $this->clearForm();

            $this->startDate = filled(
                $internship->started_at
            )
                ? Carbon::parse(
                    $internship->started_at
                )->format('Y-m-d')
                : now()->format('Y-m-d');

            $this->targetEndDate = filled(
                $internship->ended_at
            )
                ? Carbon::parse(
                    $internship->ended_at
                )->format('Y-m-d')
                : now()
                    ->addMonths(3)
                    ->format('Y-m-d');
        }

        $this->formOpen = true;
    }

    public function cancelEdit(): void
    {
        $internship = $this->currentInternship();

        $framework = $internship
            ? $this->latestFramework(
                $internship
            )
            : null;

        /*
        |--------------------------------------------------------------------------
        | Belum memiliki Kerangka Acuan
        |--------------------------------------------------------------------------
        |
        | Form tidak ditutup agar mahasiswa tetap dapat membuat versi pertama.
        |
        */

        if (! $framework) {
            $this->resetValidation();

            $this->formOpen = true;

            return;
        }

        $this->formOpen = false;

        $this->resetValidation();

        if (
            $this->isEditableStatus(
                $framework->status
            )
        ) {
            $this->fillForm(
                $framework
            );

            return;
        }

        $this->clearForm();
    }

    public function saveDraft(): void
    {
        $this->saveFramework(
            false
        );
    }

    public function submitForReview(): void
    {
        $this->saveFramework(
            true
        );
    }

    private function saveFramework(
        bool $submit
    ): void {
        $internship = $this->currentInternship();

        if (! $internship) {
            throw ValidationException::withMessages([
                'title' =>
                    'Data magang belum tersedia.',
            ]);
        }

        $validated = $this->validate();

        DB::transaction(
            function () use (
                $internship,
                $validated,
                $submit
            ): void {
                $framework = null;

                if ($this->editingId) {
                    $framework =
                        FrameworkOfReference::query()
                            ->where(
                                'internship_id',
                                $internship->id
                            )
                            ->where(
                                'id',
                                $this->editingId
                            )
                            ->first();
                }

                if (! $framework) {
                    $latest =
                        $this->latestFramework(
                            $internship
                        );

                    if (
                        $latest
                        && ! $this->isEditableStatus(
                            $latest->status
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'title' =>
                                'Kerangka Acuan sedang ditinjau atau sudah disetujui.',
                        ]);
                    }

                    $framework =
                        $latest
                        ?? new FrameworkOfReference();

                    if (! $framework->exists) {
                        $latestVersion =
                            FrameworkOfReference::query()
                                ->where(
                                    'internship_id',
                                    $internship->id
                                )
                                ->max('version');

                        $framework->internship_id =
                            $internship->id;

                        $framework->version =
                            ((int) $latestVersion)
                            + 1;

                        $framework->status =
                            'Draft';
                    }
                }

                if (
                    $framework->exists
                    && ! $this->isEditableStatus(
                        $framework->status
                    )
                ) {
                    throw ValidationException::withMessages([
                        'title' =>
                            'Kerangka Acuan tidak dapat diubah pada status saat ini.',
                    ]);
                }

                $currentStatus =
                    $this->normalizeStatus(
                        $framework->status
                    );

                $framework->title =
                    $validated['title'];

                $framework->description =
                    $validated['description'];

                $framework->start_date =
                    $validated['startDate'];

                $framework->target_end_date =
                    $validated[
                        'targetEndDate'
                    ];

                $framework->work_plan =
                    $validated['workPlan'];

                $framework->ownership_clause =
                    $validated[
                        'ownershipClause'
                    ]
                    ?: null;

                $framework->confidentiality_clause =
                    $validated[
                        'confidentialityClause'
                    ]
                    ?: null;

                $framework->remuneration_clause =
                    $validated[
                        'remunerationClause'
                    ]
                    ?: null;

                $framework->status =
                    $submit
                        ? 'Menunggu_Review'
                        : (
                            str_contains(
                                $currentStatus,
                                'revisi'
                            )
                                ? 'Perlu_Revisi'
                                : 'Draft'
                        );

                $framework
                    ->field_supervisor_approved_at =
                        null;

                $framework
                    ->lecturer_approved_at =
                        null;

                $framework->save();

                $this->editingId =
                    $framework->id;
            }
        );

        $this->formOpen = ! $submit;

        $this->resetValidation();

        session()->flash(
            'success',
            $submit
                ? 'Kerangka Acuan berhasil diajukan untuk direview.'
                : 'Draft Kerangka Acuan berhasil disimpan.'
        );
    }

    public function requestNewVersion(): void
    {
        $internship = $this->currentInternship();

        if (! $internship) {
            session()->flash(
                'error',
                'Data magang belum tersedia.'
            );

            return;
        }

        $latest = $this->latestFramework(
            $internship
        );

        if (! $latest) {
            $this->startRevision();

            return;
        }

        if (
            ! $this->isApprovedStatus(
                $latest->status
            )
        ) {
            session()->flash(
                'error',
                'Versi baru hanya dapat dibuat setelah Kerangka Acuan disetujui.'
            );

            return;
        }

        $newFramework = DB::transaction(
            function () use (
                $latest,
                $internship
            ): FrameworkOfReference {
                $framework =
                    new FrameworkOfReference();

                $framework->internship_id =
                    $internship->id;

                $framework->version =
                    ((int) $latest->version)
                    + 1;

                $framework->title =
                    $latest->title;

                $framework->description =
                    $latest->description;

                $framework->start_date =
                    $latest->start_date;

                $framework->target_end_date =
                    $latest->target_end_date;

                $framework->work_plan =
                    $latest->work_plan;

                $framework->ownership_clause =
                    $latest->ownership_clause;

                $framework->confidentiality_clause =
                    $latest->confidentiality_clause;

                $framework->remuneration_clause =
                    $latest->remuneration_clause;

                $framework->status =
                    'Draft';

                $framework->previous_version_id =
                    $latest->id;

                $framework
                    ->field_supervisor_approved_at =
                        null;

                $framework
                    ->lecturer_approved_at =
                        null;

                $framework
                    ->field_supervisor_notes =
                        null;

                $framework->lecturer_notes =
                    null;

                $framework->save();

                return $framework;
            }
        );

        $this->fillForm(
            $newFramework
        );

        $this->formOpen = true;

        session()->flash(
            'success',
            'Versi baru berhasil dibuat. Silakan lakukan perubahan.'
        );
    }

    public function render()
    {
        $internship = $this->currentInternship();

        $currentFramework =
            $internship
                ? $this->latestFramework(
                    $internship
                )
                : null;

        $history =
            $internship
                ? FrameworkOfReference::query()
                    ->where(
                        'internship_id',
                        $internship->id
                    )
                    ->orderByDesc('version')
                    ->orderByDesc('id')
                    ->get()
                : collect();

        $statusKey =
            $this->normalizeStatus(
                $currentFramework?->status
            );

        $canEdit =
            ! $currentFramework
            || $this->isEditableStatus(
                $currentFramework->status
            );

        $canCreateNewVersion =
            $currentFramework
            && $this->isApprovedStatus(
                $currentFramework->status
            );

        return view(
            'livewire.student.framework-index',
            [
                'internship' =>
                    $internship,

                'currentFramework' =>
                    $currentFramework,

                'history' =>
                    $history,

                'statusKey' =>
                    $statusKey,

                'statusLabel' =>
                    $this->statusLabel(
                        $currentFramework?->status
                    ),

                'statusTone' =>
                    $this->statusTone(
                        $currentFramework?->status
                    ),

                'canEdit' =>
                    $canEdit,

                'canCreateNewVersion' =>
                    $canCreateNewVersion,
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    'Kerangka Acuan',
            ]
        );
    }
}