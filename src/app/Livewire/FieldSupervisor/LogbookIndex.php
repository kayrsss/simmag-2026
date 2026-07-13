<?php

namespace App\Livewire\FieldSupervisor;

use App\Models\Internship;
use App\Models\Logbook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class LogbookIndex extends Component
{
    public string $search = '';

    public string $statusFilter =
        Logbook::STATUS_WAITING_VALIDATION;

    public bool $reviewOpen = false;

    public ?int $selectedId = null;

    public string $reviewNote = '';

    public function mount(): void
    {
        $reviewId = request()->integer('review');

        if ($reviewId > 0) {
            $this->openReview($reviewId);
        }
    }

    public function openReview(int $id): void
    {
        $logbook = $this->findAssignedLogbook($id);

        if (! $logbook) {
            session()->flash(
                'error',
                'Logbook tidak ditemukan atau bukan bagian dari penugasan Anda.'
            );

            return;
        }

        $this->selectedId = $logbook->id;

        $this->reviewNote =
            $logbook->review_note ?? '';

        $this->reviewOpen = true;

        $this->resetValidation();
    }

    public function closeReview(): void
    {
        $this->reviewOpen = false;

        $this->selectedId = null;

        $this->reviewNote = '';

        $this->resetValidation();
    }

    public function approve(): void
    {
        $this->validate(
            [
                'reviewNote' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ],
            [
                'reviewNote.max' =>
                    'Catatan maksimal 2.000 karakter.',
            ]
        );

        $logbook = $this->selectedId
            ? $this->findAssignedLogbook(
                $this->selectedId
            )
            : null;

        if (! $logbook) {
            $this->closeReview();

            session()->flash(
                'error',
                'Logbook tidak ditemukan.'
            );

            return;
        }

        if (
            $logbook->status !==
            Logbook::STATUS_WAITING_VALIDATION
        ) {
            $this->closeReview();

            session()->flash(
                'error',
                'Logbook ini sudah diproses.'
            );

            return;
        }

        DB::transaction(
            function () use ($logbook): void {
                $logbook->update([
                    'status' =>
                        Logbook::STATUS_VALIDATED,

                    'review_note' =>
                        filled($this->reviewNote)
                            ? trim($this->reviewNote)
                            : 'Aktivitas dan bukti pendukung telah sesuai.',

                    'validated_at' =>
                        now(),

                    'validated_by' =>
                        Auth::id(),
                ]);
            }
        );

        $this->closeReview();

        session()->flash(
            'success',
            'Logbook berhasil divalidasi.'
        );
    }

    public function requestRevision(): void
    {
        $this->validate(
            [
                'reviewNote' => [
                    'required',
                    'string',
                    'min:5',
                    'max:2000',
                ],
            ],
            [
                'reviewNote.required' =>
                    'Catatan revisi wajib diisi.',

                'reviewNote.min' =>
                    'Catatan revisi minimal 5 karakter.',

                'reviewNote.max' =>
                    'Catatan revisi maksimal 2.000 karakter.',
            ]
        );

        $logbook = $this->selectedId
            ? $this->findAssignedLogbook(
                $this->selectedId
            )
            : null;

        if (! $logbook) {
            $this->closeReview();

            session()->flash(
                'error',
                'Logbook tidak ditemukan.'
            );

            return;
        }

        if (
            $logbook->status !==
            Logbook::STATUS_WAITING_VALIDATION
        ) {
            $this->closeReview();

            session()->flash(
                'error',
                'Logbook ini sudah diproses.'
            );

            return;
        }

        DB::transaction(
            function () use ($logbook): void {
                $logbook->update([
                    'status' =>
                        Logbook::STATUS_REVISION_REQUIRED,

                    'review_note' =>
                        trim($this->reviewNote),

                    'validated_at' =>
                        now(),

                    'validated_by' =>
                        Auth::id(),
                ]);
            }
        );

        $this->closeReview();

        session()->flash(
            'success',
            'Logbook dikembalikan kepada mahasiswa untuk diperbaiki.'
        );
    }

    private function assignmentColumn(): ?string
    {
        if (! Schema::hasTable('internships')) {
            return null;
        }

        $columns = [
            'field_supervisor_id',
            'pembimbing_lapangan_id',
            'supervisor_id',
            'mentor_id',
        ];

        foreach ($columns as $column) {
            if (
                Schema::hasColumn(
                    'internships',
                    $column
                )
            ) {
                return $column;
            }
        }

        return null;
    }

    private function assignedInternshipIds(): Collection
    {
        if (! Schema::hasTable('internships')) {
            return collect();
        }

        $assignmentColumn =
            $this->assignmentColumn();

        if ($assignmentColumn) {
            return Internship::query()
                ->where(
                    $assignmentColumn,
                    Auth::id()
                )
                ->pluck('id');
        }

        $user = Auth::user();

        if (! $user) {
            return collect();
        }

        $companyColumns = [
            'company_profile_id',
            'company_id',
            'institution_id',
        ];

        foreach ($companyColumns as $column) {
            if (
                Schema::hasColumn(
                    'users',
                    $column
                )
                && Schema::hasColumn(
                    'internships',
                    $column
                )
                && filled(
                    data_get($user, $column)
                )
            ) {
                return Internship::query()
                    ->where(
                        $column,
                        data_get($user, $column)
                    )
                    ->pluck('id');
            }
        }

        return collect();
    }

    private function logbookQuery(): Builder
    {
        return Logbook::query()
            ->with('student')
            ->whereIn(
                'internship_id',
                $this->assignedInternshipIds()
            );
    }

    private function findAssignedLogbook(
        int $id
    ): ?Logbook {
        return $this->logbookQuery()
            ->find($id);
    }

    public function render()
    {
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
                function (Builder $builder) use (
                    $keyword
                ): void {
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
                        )
                        ->orWhereHas(
                            'student',
                            function (
                                Builder $studentQuery
                            ) use ($keyword): void {
                                $studentQuery->where(
                                    'name',
                                    'like',
                                    $keyword
                                );

                                if (
                                    Schema::hasColumn(
                                        'users',
                                        'username'
                                    )
                                ) {
                                    $studentQuery->orWhere(
                                        'username',
                                        'like',
                                        $keyword
                                    );
                                }

                                if (
                                    Schema::hasColumn(
                                        'users',
                                        'nim'
                                    )
                                ) {
                                    $studentQuery->orWhere(
                                        'nim',
                                        'like',
                                        $keyword
                                    );
                                }

                                if (
                                    Schema::hasColumn(
                                        'users',
                                        'identifier'
                                    )
                                ) {
                                    $studentQuery->orWhere(
                                        'identifier',
                                        'like',
                                        $keyword
                                    );
                                }
                            }
                        );
                }
            );
        }

        $logbooks = $query
            ->orderByDesc('activity_date')
            ->orderByDesc('id')
            ->get();

        $baseQuery =
            $this->logbookQuery();

        $statistics = [
            'total' =>
                (clone $baseQuery)->count(),

            'waiting' =>
                (clone $baseQuery)
                    ->where(
                        'status',
                        Logbook::STATUS_WAITING_VALIDATION
                    )
                    ->count(),

            'validated' =>
                (clone $baseQuery)
                    ->where(
                        'status',
                        Logbook::STATUS_VALIDATED
                    )
                    ->count(),

            'revision' =>
                (clone $baseQuery)
                    ->where(
                        'status',
                        Logbook::STATUS_REVISION_REQUIRED
                    )
                    ->count(),
        ];

        $selectedLogbook =
            $this->selectedId
                ? $this->findAssignedLogbook(
                    $this->selectedId
                )
                : null;

        return view(
            'livewire.field-supervisor.logbook-index',
            [
                'logbooks' =>
                    $logbooks,

                'statistics' =>
                    $statistics,

                'selectedLogbook' =>
                    $selectedLogbook,

                'assignmentColumn' =>
                    $this->assignmentColumn(),
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    'Validasi Logbook',
            ]
        );
    }
}