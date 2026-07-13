<?php

namespace App\Livewire\FieldSupervisor;

use App\Models\FieldAssessment;
use App\Models\FinalReport;
use App\Models\FrameworkOfReference;
use App\Models\Internship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

abstract class FieldSupervisorWorkflowPage extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public bool $modalOpen = false;

    public ?int $selectedId = null;

    public string $reviewNotes = '';

    public string $assessmentNotes = '';

    public array $scores = [
        'discipline_score' => 0,
        'initiative_score' => 0,
        'teamwork_score' => 0,
        'communication_score' => 0,
        'adaptability_score' => 0,
        'diligence_score' => 0,
        'appearance_score' => 0,
        'honesty_score' => 0,
        'critical_thinking_score' => 0,
        'responsibility_score' => 0,
    ];

    abstract protected function moduleKey(): string;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    protected function assignedInternshipsQuery(): Builder
    {
        $query = Internship::query()
            ->with([
                'student.programStudy',
                'company',
                'period',
                'supervisorLecturer',
                'fieldAssessment',
                'finalReports',
            ]);

        $user = Auth::user();

        if (
            Schema::hasColumn(
                'internships',
                'field_supervisor_id'
            )
        ) {
            $query->where(
                function (Builder $assignedQuery) use ($user): void {
                    $assignedQuery->where(
                        'field_supervisor_id',
                        $user->id
                    );

                    if (
                        filled($user->email)
                        && Schema::hasColumn(
                            'internships',
                            'field_supervisor_email'
                        )
                    ) {
                        $assignedQuery->orWhere(
                            'field_supervisor_email',
                            $user->email
                        );
                    }
                }
            );

            return $query;
        }

        if (
            filled($user->email)
            && Schema::hasColumn(
                'internships',
                'field_supervisor_email'
            )
        ) {
            return $query->where(
                'field_supervisor_email',
                $user->email
            );
        }

        return $query->whereRaw('1 = 0');
    }

    protected function applyInternshipSearch(
        Builder $query
    ): void {
        $keyword = trim($this->search);

        if ($keyword === '') {
            return;
        }

        $query->where(
            function (Builder $searchQuery) use ($keyword): void {
                $searchQuery
                    ->whereHas(
                        'student',
                        function (Builder $studentQuery) use ($keyword): void {
                            $studentQuery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$keyword}%"
                                )
                                ->orWhere(
                                    'nim',
                                    'like',
                                    "%{$keyword}%"
                                )
                                ->orWhere(
                                    'identifier',
                                    'like',
                                    "%{$keyword}%"
                                );
                        }
                    )
                    ->orWhereHas(
                        'company',
                        fn (Builder $companyQuery) =>
                            $companyQuery->where(
                                'name',
                                'like',
                                "%{$keyword}%"
                            )
                    );
            }
        );
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;

        $this->selectedId = null;

        $this->reviewNotes = '';

        $this->assessmentNotes = '';

        $this->scores = [
            'discipline_score' => 0,
            'initiative_score' => 0,
            'teamwork_score' => 0,
            'communication_score' => 0,
            'adaptability_score' => 0,
            'diligence_score' => 0,
            'appearance_score' => 0,
            'honesty_score' => 0,
            'critical_thinking_score' => 0,
            'responsibility_score' => 0,
        ];

        $this->resetValidation();
    }

    protected function assignedInternship(
        int $internshipId
    ): ?Internship {
        return $this
            ->assignedInternshipsQuery()
            ->where(
                'id',
                $internshipId
            )
            ->first();
    }

    protected function assignedFramework(
        int $frameworkId
    ): ?FrameworkOfReference {
        $internshipIds =
            $this
                ->assignedInternshipsQuery()
                ->select('id');

        return FrameworkOfReference::query()
            ->with([
                'internship.student',
                'internship.company',
            ])
            ->whereIn(
                'internship_id',
                $internshipIds
            )
            ->where(
                'id',
                $frameworkId
            )
            ->first();
    }

    public function openFramework(
        int $frameworkId
    ): void {
        $framework =
            $this->assignedFramework(
                $frameworkId
            );

        if (! $framework) {
            session()->flash(
                'error',
                'Kerangka Acuan tidak ditemukan.'
            );

            return;
        }

        $this->selectedId =
            $framework->id;

        $this->reviewNotes =
            $framework
                ->field_supervisor_notes
                ?? '';

        $this->modalOpen = true;

        $this->resetValidation();
    }

    public function approveFramework(): void
    {
        $framework =
            $this->selectedId
                ? $this->assignedFramework(
                    $this->selectedId
                )
                : null;

        if (! $framework) {
            $this->closeModal();

            session()->flash(
                'error',
                'Kerangka Acuan tidak ditemukan.'
            );

            return;
        }

        if (
            $framework->status
            !== FrameworkOfReference::STATUS_MENUNGGU_REVIEW
        ) {
            session()->flash(
                'error',
                'Kerangka Acuan ini tidak sedang menunggu review.'
            );

            return;
        }

        $this->validate([
            'reviewNotes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ]);

        $framework->update([
            'status' =>
                FrameworkOfReference::STATUS_DISETUJUI_PL,

            'field_supervisor_notes' =>
                filled($this->reviewNotes)
                    ? trim($this->reviewNotes)
                    : 'Kerangka Acuan disetujui oleh Pembimbing Lapangan.',

            'field_supervisor_approved_at' =>
                now(),
        ]);

        $this->closeModal();

        session()->flash(
            'success',
            'Kerangka Acuan berhasil disetujui dan diteruskan kepada Dosen Pembimbing.'
        );
    }

    public function requestFrameworkRevision(): void
    {
        $this->validate(
            [
                'reviewNotes' => [
                    'required',
                    'string',
                    'min:5',
                    'max:3000',
                ],
            ],
            [
                'reviewNotes.required' =>
                    'Catatan revisi wajib diisi.',

                'reviewNotes.min' =>
                    'Catatan revisi minimal 5 karakter.',
            ]
        );

        $framework =
            $this->selectedId
                ? $this->assignedFramework(
                    $this->selectedId
                )
                : null;

        if (! $framework) {
            $this->closeModal();

            session()->flash(
                'error',
                'Kerangka Acuan tidak ditemukan.'
            );

            return;
        }

        if (
            $framework->status
            !== FrameworkOfReference::STATUS_MENUNGGU_REVIEW
        ) {
            session()->flash(
                'error',
                'Kerangka Acuan ini tidak sedang menunggu review.'
            );

            return;
        }

        $framework->update([
            'status' =>
                FrameworkOfReference::STATUS_PERLU_REVISI,

            'field_supervisor_notes' =>
                trim($this->reviewNotes),

            'field_supervisor_approved_at' =>
                null,
        ]);

        $this->closeModal();

        session()->flash(
            'success',
            'Kerangka Acuan dikembalikan kepada mahasiswa untuk direvisi.'
        );
    }

    public function openAssessment(
        int $internshipId
    ): void {
        $internship =
            $this->assignedInternship(
                $internshipId
            );

        if (! $internship) {
            session()->flash(
                'error',
                'Data mahasiswa tidak ditemukan.'
            );

            return;
        }

        $assessment =
            FieldAssessment::query()
                ->where(
                    'internship_id',
                    $internship->id
                )
                ->first();

        $this->selectedId =
            $internship->id;

        if ($assessment) {
            foreach (
                array_keys($this->scores)
                as $column
            ) {
                $this->scores[$column] =
                    (int) $assessment->{$column};
            }

            $this->assessmentNotes =
                $assessment->notes
                ?? '';
        }

        $this->modalOpen = true;

        $this->resetValidation();
    }

    public function saveAssessment(): void
    {
        $internship =
            $this->selectedId
                ? $this->assignedInternship(
                    $this->selectedId
                )
                : null;

        if (! $internship) {
            $this->closeModal();

            session()->flash(
                'error',
                'Data mahasiswa tidak ditemukan.'
            );

            return;
        }

        $approvedReportExists =
            FinalReport::query()
                ->where(
                    'internship_id',
                    $internship->id
                )
                ->where(
                    'status',
                    FinalReport::STATUS_DISETUJUI
                )
                ->exists();

        if (! $approvedReportExists) {
            session()->flash(
                'error',
                'Penilaian Lapangan dapat disimpan setelah Laporan Akhir disetujui.'
            );

            return;
        }

        $scoreRules = [];

        foreach (
            array_keys($this->scores)
            as $column
        ) {
            $scoreRules[
                "scores.{$column}"
            ] = [
                'required',
                'integer',
                'min:0',
                'max:100',
            ];
        }

        $scoreRules[
            'assessmentNotes'
        ] = [
            'nullable',
            'string',
            'max:3000',
        ];

        $this->validate(
            $scoreRules,
            [
                'scores.*.required' =>
                    'Seluruh nilai wajib diisi.',

                'scores.*.integer' =>
                    'Nilai harus berupa angka.',

                'scores.*.min' =>
                    'Nilai minimal 0.',

                'scores.*.max' =>
                    'Nilai maksimal 100.',
            ]
        );

        FieldAssessment::query()
            ->updateOrCreate(
                [
                    'internship_id' =>
                        $internship->id,
                ],
                array_merge(
                    $this->scores,
                    [
                        'evaluator_id' =>
                            Auth::id(),

                        'notes' =>
                            filled(
                                $this->assessmentNotes
                            )
                                ? trim(
                                    $this->assessmentNotes
                                )
                                : null,

                        'assessed_at' =>
                            now(),
                    ]
                )
            );

        $this->closeModal();

        session()->flash(
            'success',
            'Penilaian Lapangan berhasil disimpan.'
        );
    }

    public function render()
    {
        $module =
            $this->moduleKey();

        $records = collect();

        if (
            in_array(
                $module,
                [
                    'students',
                    'assessments',
                ],
                true
            )
        ) {
            $query =
                $this
                    ->assignedInternshipsQuery();

            $this->applyInternshipSearch(
                $query
            );

            if (
                $this->statusFilter
                !== 'all'
            ) {
                $query->where(
                    'status',
                    $this->statusFilter
                );
            }

            $records = $query
                ->latest('id')
                ->paginate(10);
        }

        if ($module === 'frameworks') {
            $internshipIds =
                $this
                    ->assignedInternshipsQuery()
                    ->select('id');

            $query =
                FrameworkOfReference::query()
                    ->with([
                        'internship.student',
                        'internship.company',
                    ])
                    ->whereIn(
                        'internship_id',
                        $internshipIds
                    );

            if (
                trim($this->search)
                !== ''
            ) {
                $keyword =
                    trim($this->search);

                $query->where(
                    function (Builder $searchQuery) use ($keyword): void {
                        $searchQuery
                            ->where(
                                'title',
                                'like',
                                "%{$keyword}%"
                            )
                            ->orWhereHas(
                                'internship.student',
                                fn (Builder $studentQuery) =>
                                    $studentQuery->where(
                                        'name',
                                        'like',
                                        "%{$keyword}%"
                                    )
                            );
                    }
                );
            }

            if (
                $this->statusFilter
                !== 'all'
            ) {
                $query->where(
                    'status',
                    $this->statusFilter
                );
            }

            $records = $query
                ->latest('updated_at')
                ->paginate(10);
        }

        $selectedFramework =
            $module === 'frameworks'
            && $this->selectedId
                ? $this->assignedFramework(
                    $this->selectedId
                )
                : null;

        $selectedInternship =
            $module === 'assessments'
            && $this->selectedId
                ? $this->assignedInternship(
                    $this->selectedId
                )
                : null;

        return view(
            'livewire.field-supervisor.workflow-index',
            [
                'module' =>
                    $module,

                'records' =>
                    $records,

                'selectedFramework' =>
                    $selectedFramework,

                'selectedInternship' =>
                    $selectedInternship,
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' => match (
                    $module
                ) {
                    'students' =>
                        'Daftar Mahasiswa',

                    'frameworks' =>
                        'Review Kerangka Acuan',

                    'assessments' =>
                        'Penilaian Lapangan',

                    default =>
                        'Pembimbing Lapangan',
                },
            ]
        );
    }
}