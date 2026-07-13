<?php

namespace App\Livewire\Lecturer;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class FrameworkIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'waiting';

    public bool $reviewOpen = false;

    public ?int $selectedId = null;

    public string $lecturerNotes = '';

    protected array $queryString = [
        'search' => [
            'except' => '',
        ],

        'statusFilter' => [
            'except' => 'waiting',
            'as' => 'status',
        ],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    private function columnExists(
        string $table,
        string $column
    ): bool {
        return Schema::hasTable($table)
            && Schema::hasColumn(
                $table,
                $column
            );
    }

    private function firstExistingColumn(
        string $table,
        array $columns
    ): ?string {
        foreach ($columns as $column) {
            if (
                $this->columnExists(
                    $table,
                    $column
                )
            ) {
                return $column;
            }
        }

        return null;
    }

    private function normalizeStatus(
        ?string $status
    ): string {
        return strtolower(
            str_replace(
                [
                    ' ',
                    '-',
                ],
                '_',
                trim((string) $status)
            )
        );
    }

    private function statusGroups(): array
    {
        return [
            'waiting' => [
                'Disetujui_PL',
                'Menunggu_Review_Dosen',
                'Menunggu_Persetujuan_Dosen',
                'Diajukan_Ke_Dosen',
            ],

            'approved' => [
                'Disetujui',
                'Disetujui_Dosen',
            ],

            'revision' => [
                'Perlu_Revisi',
                'Revisi_Dosen',
            ],

            'draft' => [
                'Draft',
                'Menunggu_Review',
            ],
        ];
    }

    private function normalizedStatuses(
        array $statuses
    ): array {
        return collect($statuses)
            ->map(
                fn (string $status): string =>
                    $this->normalizeStatus(
                        $status
                    )
            )
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function applyStatuses(
        Builder $query,
        array $statuses
    ): void {
        $normalizedStatuses =
            $this->normalizedStatuses(
                $statuses
            );

        if ($normalizedStatuses === []) {
            return;
        }

        $placeholders = implode(
            ', ',
            array_fill(
                0,
                count($normalizedStatuses),
                '?'
            )
        );

        $query->whereRaw(
            "
            LOWER(
                REPLACE(
                    REPLACE(
                        frameworks.status,
                        ' ',
                        '_'
                    ),
                    '-',
                    '_'
                )
            ) IN ({$placeholders})
            ",
            $normalizedStatuses
        );
    }

    private function baseQuery(): Builder
    {
        $identifierColumn =
            $this->firstExistingColumn(
                'users',
                [
                    'nim',
                    'identifier',
                    'username',
                    'email',
                ]
            );

        $query = DB::table(
            'framework_of_references as frameworks'
        )
            ->join(
                'internships',
                'internships.id',
                '=',
                'frameworks.internship_id'
            )
            ->leftJoin(
                'users as students',
                'students.id',
                '=',
                'internships.student_id'
            )
            ->where(
                'internships.supervisor_lecturer_id',
                Auth::id()
            )
            ->select([
                'frameworks.id',
                'frameworks.internship_id',
                'frameworks.version',
                'frameworks.title',
                'frameworks.description',
                'frameworks.start_date',
                'frameworks.target_end_date',
                'frameworks.work_plan',
                'frameworks.ownership_clause',
                'frameworks.confidentiality_clause',
                'frameworks.remuneration_clause',
                'frameworks.file_path',
                'frameworks.status',
                'frameworks.field_supervisor_approved_at',
                'frameworks.lecturer_approved_at',
                'frameworks.field_supervisor_notes',
                'frameworks.lecturer_notes',
                'frameworks.previous_version_id',
                'frameworks.created_at',
                'frameworks.updated_at',
                'internships.student_id',
                'students.name as student_name',
            ]);

        if ($identifierColumn) {
            $query->addSelect(
                "students.{$identifierColumn} as student_identifier"
            );
        } else {
            $query->selectRaw(
                "'-' as student_identifier"
            );
        }

        if (
            Schema::hasTable('company_profiles')
            && $this->columnExists(
                'internships',
                'company_id'
            )
        ) {
            $query
                ->leftJoin(
                    'company_profiles',
                    'company_profiles.id',
                    '=',
                    'internships.company_id'
                )
                ->addSelect(
                    'company_profiles.name as company_name'
                );
        } else {
            $query->selectRaw(
                "'-' as company_name"
            );
        }

        return $query;
    }

    private function applySearch(
        Builder $query
    ): void {
        $keyword = trim(
            $this->search
        );

        if ($keyword === '') {
            return;
        }

        $searchValue =
            '%' . $keyword . '%';

        $identifierColumn =
            $this->firstExistingColumn(
                'users',
                [
                    'nim',
                    'identifier',
                    'username',
                    'email',
                ]
            );

        $hasCompanyJoin =
            Schema::hasTable('company_profiles')
            && $this->columnExists(
                'internships',
                'company_id'
            );

        $query->where(
            function (Builder $searchQuery) use (
                $searchValue,
                $identifierColumn,
                $hasCompanyJoin
            ): void {
                $searchQuery
                    ->where(
                        'frameworks.title',
                        'like',
                        $searchValue
                    )
                    ->orWhere(
                        'frameworks.description',
                        'like',
                        $searchValue
                    )
                    ->orWhere(
                        'students.name',
                        'like',
                        $searchValue
                    );

                if ($identifierColumn) {
                    $searchQuery->orWhere(
                        "students.{$identifierColumn}",
                        'like',
                        $searchValue
                    );
                }

                if ($hasCompanyJoin) {
                    $searchQuery->orWhere(
                        'company_profiles.name',
                        'like',
                        $searchValue
                    );
                }
            }
        );
    }

    private function countStatuses(
        array $statuses
    ): int {
        $query = $this->baseQuery();

        $this->applyStatuses(
            $query,
            $statuses
        );

        return $query->count();
    }

    private function findAssignedFramework(
        int $id
    ): ?object {
        return $this->baseQuery()
            ->where(
                'frameworks.id',
                $id
            )
            ->first();
    }

    private function isReviewableStatus(
        ?string $status
    ): bool {
        return in_array(
            $this->normalizeStatus($status),
            $this->normalizedStatuses(
                $this->statusGroups()['waiting']
            ),
            true
        );
    }

    public function openReview(
        int $id
    ): void {
        $framework =
            $this->findAssignedFramework(
                $id
            );

        if (! $framework) {
            session()->flash(
                'error',
                'Kerangka Acuan tidak ditemukan atau bukan mahasiswa bimbingan Anda.'
            );

            return;
        }

        $this->selectedId =
            $framework->id;

        $this->lecturerNotes =
            $framework->lecturer_notes
            ?? '';

        $this->reviewOpen = true;

        $this->resetValidation();
    }

    public function closeReview(): void
    {
        $this->reviewOpen = false;

        $this->selectedId = null;

        $this->lecturerNotes = '';

        $this->resetValidation();
    }

    public function approve(): void
    {
        $this->validate(
            [
                'lecturerNotes' => [
                    'nullable',
                    'string',
                    'max:3000',
                ],
            ],
            [
                'lecturerNotes.max' =>
                    'Catatan maksimal 3.000 karakter.',
            ]
        );

        $framework = $this->selectedId
            ? $this->findAssignedFramework(
                $this->selectedId
            )
            : null;

        if (! $framework) {
            $this->closeReview();

            session()->flash(
                'error',
                'Kerangka Acuan tidak ditemukan.'
            );

            return;
        }

        if (
            ! $this->isReviewableStatus(
                $framework->status
            )
        ) {
            $this->closeReview();

            session()->flash(
                'error',
                'Kerangka Acuan ini tidak sedang menunggu review Dosen Pembimbing.'
            );

            return;
        }

        DB::transaction(
            function () use (
                $framework
            ): void {
                DB::table(
                    'framework_of_references'
                )
                    ->where(
                        'id',
                        $framework->id
                    )
                    ->update([
                        'status' =>
                            'Disetujui',

                        'lecturer_notes' =>
                            filled(
                                $this->lecturerNotes
                            )
                                ? trim(
                                    $this->lecturerNotes
                                )
                                : 'Kerangka Acuan telah disetujui oleh Dosen Pembimbing.',

                        'lecturer_approved_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]);

                DB::table('internships')
                    ->where(
                        'id',
                        $framework->internship_id
                    )
                    ->whereNotIn(
                        'status',
                        [
                            'selesai',
                            'batal',
                        ]
                    )
                    ->update([
                        'status' => 'aktif',
                        'updated_at' => now(),
                    ]);
            }
        );

        $this->closeReview();

        session()->flash(
            'success',
            'Kerangka Acuan berhasil disetujui.'
        );
    }

    public function requestRevision(): void
    {
        $this->validate(
            [
                'lecturerNotes' => [
                    'required',
                    'string',
                    'min:5',
                    'max:3000',
                ],
            ],
            [
                'lecturerNotes.required' =>
                    'Catatan revisi wajib diisi.',

                'lecturerNotes.min' =>
                    'Catatan revisi minimal 5 karakter.',

                'lecturerNotes.max' =>
                    'Catatan revisi maksimal 3.000 karakter.',
            ]
        );

        $framework = $this->selectedId
            ? $this->findAssignedFramework(
                $this->selectedId
            )
            : null;

        if (! $framework) {
            $this->closeReview();

            session()->flash(
                'error',
                'Kerangka Acuan tidak ditemukan.'
            );

            return;
        }

        if (
            ! $this->isReviewableStatus(
                $framework->status
            )
        ) {
            $this->closeReview();

            session()->flash(
                'error',
                'Kerangka Acuan ini tidak sedang menunggu review Dosen Pembimbing.'
            );

            return;
        }

        DB::transaction(
            function () use (
                $framework
            ): void {
                DB::table(
                    'framework_of_references'
                )
                    ->where(
                        'id',
                        $framework->id
                    )
                    ->update([
                        'status' =>
                            'Perlu_Revisi',

                        'lecturer_notes' =>
                            trim(
                                $this->lecturerNotes
                            ),

                        'lecturer_approved_at' =>
                            null,

                        'updated_at' =>
                            now(),
                    ]);
            }
        );

        $this->closeReview();

        session()->flash(
            'success',
            'Kerangka Acuan dikembalikan kepada mahasiswa untuk direvisi.'
        );
    }

    public function render()
    {
        $query = $this->baseQuery();

        $this->applySearch(
            $query
        );

        if (
            $this->statusFilter !== 'all'
            && isset(
                $this->statusGroups()[
                    $this->statusFilter
                ]
            )
        ) {
            $this->applyStatuses(
                $query,
                $this->statusGroups()[
                    $this->statusFilter
                ]
            );
        }

        $frameworks = $query
            ->orderByDesc(
                'frameworks.updated_at'
            )
            ->orderByDesc(
                'frameworks.id'
            )
            ->paginate(10);

        $statistics = [
            'total' =>
                $this->baseQuery()
                    ->count(),

            'waiting' =>
                $this->countStatuses(
                    $this->statusGroups()[
                        'waiting'
                    ]
                ),

            'approved' =>
                $this->countStatuses(
                    $this->statusGroups()[
                        'approved'
                    ]
                ),

            'revision' =>
                $this->countStatuses(
                    $this->statusGroups()[
                        'revision'
                    ]
                ),
        ];

        $selectedFramework =
            $this->selectedId
                ? $this->findAssignedFramework(
                    $this->selectedId
                )
                : null;

        return view(
            'livewire.lecturer.framework-index',
            [
                'frameworks' =>
                    $frameworks,

                'statistics' =>
                    $statistics,

                'selectedFramework' =>
                    $selectedFramework,
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    'Review Kerangka Acuan',
            ]
        );
    }
}