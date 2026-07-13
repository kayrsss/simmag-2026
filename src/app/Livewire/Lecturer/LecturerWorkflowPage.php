<?php

namespace App\Livewire\Lecturer;

use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

abstract class LecturerWorkflowPage extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public bool $detailOpen = false;

    public ?int $selectedId = null;

    public string $reviewNote = '';

    public ?float $score = null;

    protected array $queryString = [
        'search' => [
            'except' => '',
        ],

        'statusFilter' => [
            'except' => 'all',
            'as' => 'status',
        ],
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

    protected function moduleConfig(): array
    {
        return match ($this->moduleKey()) {
            'logbooks' => [
                'page_title' => 'Monitoring Logbook',
                'description' => 'Pantau aktivitas dan hasil validasi logbook mahasiswa bimbingan.',
                'table' => 'logbooks',
                'icon' => 'edit_note',
                'empty_title' => 'Belum ada logbook',
                'empty_description' => 'Logbook akan tampil setelah mahasiswa mengisi aktivitas magang.',
                'title_columns' => [],
                'description_columns' => [
                    'activity',
                    'description',
                ],
                'status_columns' => [
                    'status',
                ],
                'date_columns' => [
                    'activity_date',
                    'created_at',
                ],
                'file_columns' => [
                    'evidence_path',
                    'file_path',
                ],
                'file_name_columns' => [
                    'evidence_name',
                    'file_name',
                ],
                'note_columns' => [
                    'review_note',
                ],
                'score_columns' => [],
                'waiting_statuses' => [
                    'Draft',
                    'Menunggu_Validasi',
                ],
                'completed_statuses' => [
                    'Tervalidasi',
                    'Disetujui',
                ],
                'revision_statuses' => [
                    'Perlu_Revisi',
                ],
            ],

            'consultations' => [
                'page_title' => 'Bimbingan',
                'description' => 'Berikan tanggapan dan catatan atas pengajuan bimbingan mahasiswa.',
                'table' => 'consultations',
                'icon' => 'forum',
                'empty_title' => 'Belum ada pengajuan bimbingan',
                'empty_description' => 'Pengajuan mahasiswa akan tampil pada halaman ini.',
                'title_columns' => [
                    'topic',
                    'title',
                    'subject',
                ],
                'description_columns' => [
                    'notes',
                    'description',
                    'student_notes',
                    'problem',
                ],
                'status_columns' => [
                    'status',
                ],
                'date_columns' => [
                    'consultation_date',
                    'requested_at',
                    'scheduled_at',
                    'created_at',
                ],
                'file_columns' => [
                    'attachment_path',
                    'file_path',
                ],
                'file_name_columns' => [
                    'attachment_name',
                    'file_name',
                ],
                'note_columns' => [
                    'lecturer_notes',
                    'lecturer_response',
                    'response',
                    'review_note',
                ],
                'score_columns' => [],
                'waiting_statuses' => [
                    'Menunggu',
                    'Menunggu_Tanggapan',
                    'Diajukan',
                    'Pending',
                ],
                'completed_statuses' => [
                    'Selesai',
                    'Ditanggapi',
                    'Disetujui',
                ],
                'revision_statuses' => [
                    'Perlu_Revisi',
                ],
            ],

            'final_reports' => [
                'page_title' => 'Review Laporan Akhir',
                'description' => 'Periksa laporan akhir mahasiswa dan berikan persetujuan atau revisi.',
                'table' => 'final_reports',
                'icon' => 'draft',
                'empty_title' => 'Belum ada Laporan Akhir',
                'empty_description' => 'Laporan akan tampil setelah mahasiswa melakukan pengajuan.',
                'title_columns' => [
                    'title',
                    'document_title',
                    'file_name',
                ],
                'description_columns' => [
                    'description',
                    'summary',
                    'notes',
                ],
                'status_columns' => [
                    'status',
                ],
                'date_columns' => [
                    'submitted_at',
                    'created_at',
                ],
                'file_columns' => [
                    'file_path',
                    'document_path',
                ],
                'file_name_columns' => [
                    'file_name',
                    'document_name',
                ],
                'note_columns' => [
                    'lecturer_notes',
                    'review_note',
                    'review_notes',
                ],
                'score_columns' => [],
                'waiting_statuses' => [
                    'Menunggu_Review',
                    'Diajukan',
                    'Submitted',
                ],
                'completed_statuses' => [
                    'Disetujui',
                    'Approved',
                ],
                'revision_statuses' => [
                    'Perlu_Revisi',
                    'Revision',
                ],
            ],

            'assessments' => [
                'page_title' => 'Penilaian Akademik',
                'description' => 'Berikan nilai akademik kepada mahasiswa bimbingan.',
                'table' => 'lecturer_assessments',
                'icon' => 'grading',
                'empty_title' => 'Belum ada mahasiswa',
                'empty_description' => 'Mahasiswa muncul setelah ditugaskan kepada Dosen Pembimbing.',
                'title_columns' => [],
                'description_columns' => [],
                'status_columns' => [
                    'status',
                ],
                'date_columns' => [
                    'assessed_at',
                    'submitted_at',
                    'updated_at',
                ],
                'file_columns' => [],
                'file_name_columns' => [],
                'note_columns' => [
                    'lecturer_notes',
                    'notes',
                    'review_note',
                ],
                'score_columns' => [
                    'final_score',
                    'total_score',
                    'score',
                    'grade',
                ],
                'waiting_statuses' => [
                    'Belum_Dinilai',
                    'Draft',
                ],
                'completed_statuses' => [
                    'Selesai',
                    'Disetujui',
                    'Submitted',
                ],
                'revision_statuses' => [],
            ],

            default => throw new \RuntimeException(
                'Modul Dosen Pembimbing tidak dikenali.'
            ),
        };
    }

    protected function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    protected function columnExists(
        string $table,
        string $column
    ): bool {
        return $this->tableExists($table)
            && Schema::hasColumn(
                $table,
                $column
            );
    }

    protected function firstExistingColumn(
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

    protected function normalizeStatus(
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

    protected function normalizedStatuses(
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

    protected function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            [],
            0,
            10,
            1,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    protected function addAliasedColumn(
        Builder $query,
        string $tableAlias,
        ?string $column,
        string $alias,
        mixed $fallback = null
    ): void {
        if ($column) {
            $query->addSelect(
                "{$tableAlias}.{$column} as {$alias}"
            );

            return;
        }

        if ($fallback === null) {
            $query->selectRaw(
                "NULL as {$alias}"
            );

            return;
        }

        $query->selectRaw(
            "? as {$alias}",
            [
                $fallback,
            ]
        );
    }

    protected function baseQuery(): ?Builder
    {
        $config = $this->moduleConfig();

        $table = $config['table'];

        if (
            ! $this->tableExists('internships')
            || ! $this->tableExists('users')
            || ! $this->columnExists(
                'internships',
                'supervisor_lecturer_id'
            )
        ) {
            return null;
        }

        if (
            $this->moduleKey() !== 'assessments'
            && ! $this->tableExists($table)
        ) {
            return null;
        }

        if ($this->moduleKey() === 'assessments') {
            $query = DB::table('internships')
                ->leftJoin(
                    "{$table} as records",
                    'records.internship_id',
                    '=',
                    'internships.id'
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
                    'internships.id as row_id',
                    'records.id as record_id',
                    'internships.id as internship_id',
                    'internships.student_id',
                    'students.name as student_name',
                ]);
        } else {
            $query = DB::table(
                "{$table} as records"
            )
                ->join(
                    'internships',
                    'internships.id',
                    '=',
                    'records.internship_id'
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
                    'records.id as row_id',
                    'records.id as record_id',
                    'records.internship_id',
                    'internships.student_id',
                    'students.name as student_name',
                ]);
        }

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
            $this->tableExists('company_profiles')
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

        $titleColumn =
            $this->firstExistingColumn(
                $table,
                $config['title_columns']
            );

        $descriptionColumn =
            $this->firstExistingColumn(
                $table,
                $config['description_columns']
            );

        $statusColumn =
            $this->firstExistingColumn(
                $table,
                $config['status_columns']
            );

        $dateColumn =
            $this->firstExistingColumn(
                $table,
                $config['date_columns']
            );

        $fileColumn =
            $this->firstExistingColumn(
                $table,
                $config['file_columns']
            );

        $fileNameColumn =
            $this->firstExistingColumn(
                $table,
                $config['file_name_columns']
            );

        $noteColumn =
            $this->firstExistingColumn(
                $table,
                $config['note_columns']
            );

        $scoreColumn =
            $this->firstExistingColumn(
                $table,
                $config['score_columns']
            );

        $this->addAliasedColumn(
            $query,
            'records',
            $titleColumn,
            'title',
            $config['page_title']
        );

        $this->addAliasedColumn(
            $query,
            'records',
            $descriptionColumn,
            'description'
        );

        if ($this->moduleKey() === 'assessments') {
            if ($statusColumn) {
                $query->selectRaw(
                    "
                    CASE
                        WHEN records.id IS NULL
                            THEN 'Belum_Dinilai'
                        ELSE COALESCE(
                            records.{$statusColumn},
                            'Selesai'
                        )
                    END as status
                    "
                );
            } else {
                $query->selectRaw(
                    "
                    CASE
                        WHEN records.id IS NULL
                            THEN 'Belum_Dinilai'
                        ELSE 'Selesai'
                    END as status
                    "
                );
            }
        } else {
            $this->addAliasedColumn(
                $query,
                'records',
                $statusColumn,
                'status',
                'Draft'
            );
        }

        $this->addAliasedColumn(
            $query,
            'records',
            $dateColumn,
            'event_date'
        );

        $this->addAliasedColumn(
            $query,
            'records',
            $fileColumn,
            'file_path'
        );

        $this->addAliasedColumn(
            $query,
            'records',
            $fileNameColumn,
            'file_name'
        );

        $this->addAliasedColumn(
            $query,
            'records',
            $noteColumn,
            'review_note'
        );

        $this->addAliasedColumn(
            $query,
            'records',
            $scoreColumn,
            'score'
        );

        return $query;
    }

    protected function applySearch(
        Builder $query
    ): void {
        $keyword = trim(
            $this->search
        );

        if ($keyword === '') {
            return;
        }

        $config = $this->moduleConfig();

        $table = $config['table'];

        $searchValue =
            '%' . $keyword . '%';

        $titleColumn =
            $this->firstExistingColumn(
                $table,
                $config['title_columns']
            );

        $descriptionColumn =
            $this->firstExistingColumn(
                $table,
                $config['description_columns']
            );

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

        $hasCompany =
            $this->tableExists('company_profiles')
            && $this->columnExists(
                'internships',
                'company_id'
            );

        $query->where(
            function (Builder $searchQuery) use (
                $searchValue,
                $titleColumn,
                $descriptionColumn,
                $identifierColumn,
                $hasCompany
            ): void {
                $searchQuery->where(
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

                if ($titleColumn) {
                    $searchQuery->orWhere(
                        "records.{$titleColumn}",
                        'like',
                        $searchValue
                    );
                }

                if ($descriptionColumn) {
                    $searchQuery->orWhere(
                        "records.{$descriptionColumn}",
                        'like',
                        $searchValue
                    );
                }

                if ($hasCompany) {
                    $searchQuery->orWhere(
                        'company_profiles.name',
                        'like',
                        $searchValue
                    );
                }
            }
        );
    }

    protected function applyStatusFilter(
        Builder $query
    ): void {
        if ($this->statusFilter === 'all') {
            return;
        }

        $config = $this->moduleConfig();

        $statuses = match ($this->statusFilter) {
            'waiting' =>
                $config['waiting_statuses'],

            'completed' =>
                $config['completed_statuses'],

            'revision' =>
                $config['revision_statuses'],

            default => [],
        };

        if ($this->moduleKey() === 'assessments') {
            if ($this->statusFilter === 'waiting') {
                $query->whereNull(
                    'records.id'
                );
            }

            if ($this->statusFilter === 'completed') {
                $query->whereNotNull(
                    'records.id'
                );
            }

            if ($this->statusFilter === 'revision') {
                $query->whereRaw('1 = 0');
            }

            return;
        }

        $statusColumn =
            $this->firstExistingColumn(
                $config['table'],
                $config['status_columns']
            );

        if (
            ! $statusColumn
            || $statuses === []
        ) {
            return;
        }

        $normalizedStatuses =
            $this->normalizedStatuses(
                $statuses
            );

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
                        records.{$statusColumn},
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

    protected function records(): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        if (! $query) {
            return $this->emptyPaginator();
        }

        $this->applySearch(
            $query
        );

        $this->applyStatusFilter(
            $query
        );

        return $query
            ->orderByDesc('event_date')
            ->orderByDesc('row_id')
            ->paginate(10);
    }

    protected function allRecords(): Collection
    {
        $query = $this->baseQuery();

        return $query
            ? $query->get()
            : collect();
    }

    protected function statistics(): array
    {
        $config = $this->moduleConfig();

        $records = $this->allRecords();

        $waiting =
            $this->normalizedStatuses(
                $config['waiting_statuses']
            );

        $completed =
            $this->normalizedStatuses(
                $config['completed_statuses']
            );

        $revision =
            $this->normalizedStatuses(
                $config['revision_statuses']
            );

        return [
            'total' =>
                $records->count(),

            'waiting' =>
                $records->filter(
                    fn (object $record): bool =>
                        in_array(
                            $this->normalizeStatus(
                                $record->status
                            ),
                            $waiting,
                            true
                        )
                )->count(),

            'completed' =>
                $records->filter(
                    fn (object $record): bool =>
                        in_array(
                            $this->normalizeStatus(
                                $record->status
                            ),
                            $completed,
                            true
                        )
                )->count(),

            'revision' =>
                $records->filter(
                    fn (object $record): bool =>
                        in_array(
                            $this->normalizeStatus(
                                $record->status
                            ),
                            $revision,
                            true
                        )
                )->count(),
        ];
    }

    protected function findSelectedRecord(
        int $id
    ): ?object {
        $query = $this->baseQuery();

        if (! $query) {
            return null;
        }

        return $query
            ->where(
                $this->moduleKey() === 'assessments'
                    ? 'internships.id'
                    : 'records.id',
                $id
            )
            ->first();
    }

    public function openDetail(int $id): void
    {
        $record =
            $this->findSelectedRecord(
                $id
            );

        if (! $record) {
            session()->flash(
                'error',
                'Data tidak ditemukan atau bukan bagian dari mahasiswa bimbingan Anda.'
            );

            return;
        }

        $this->selectedId = $id;

        $this->reviewNote =
            $record->review_note
            ?? '';

        $this->score = filled(
            $record->score
            ?? null
        )
            ? (float) $record->score
            : null;

        $this->detailOpen = true;

        $this->resetValidation();
    }

    public function closeDetail(): void
    {
        $this->detailOpen = false;

        $this->selectedId = null;

        $this->reviewNote = '';

        $this->score = null;

        $this->resetValidation();
    }

    public function submitConsultationResponse(): void
    {
        if ($this->moduleKey() !== 'consultations') {
            return;
        }

        $this->validate(
            [
                'reviewNote' => [
                    'required',
                    'string',
                    'min:5',
                    'max:3000',
                ],
            ],
            [
                'reviewNote.required' =>
                    'Tanggapan bimbingan wajib diisi.',

                'reviewNote.min' =>
                    'Tanggapan minimal 5 karakter.',

                'reviewNote.max' =>
                    'Tanggapan maksimal 3.000 karakter.',
            ]
        );

        $record = $this->selectedId
            ? $this->findSelectedRecord(
                $this->selectedId
            )
            : null;

        if (! $record) {
            $this->closeDetail();

            session()->flash(
                'error',
                'Data bimbingan tidak ditemukan.'
            );

            return;
        }

        $config = $this->moduleConfig();

        $table = $config['table'];

        $noteColumn =
            $this->firstExistingColumn(
                $table,
                $config['note_columns']
            );

        if (! $noteColumn) {
            session()->flash(
                'error',
                'Kolom tanggapan dosen belum tersedia pada tabel bimbingan.'
            );

            return;
        }

        $payload = [
            $noteColumn =>
                trim($this->reviewNote),
        ];

        $statusColumn =
            $this->firstExistingColumn(
                $table,
                $config['status_columns']
            );

        if ($statusColumn) {
            $payload[$statusColumn] =
                'Selesai';
        }

        if (
            $this->columnExists(
                $table,
                'responded_at'
            )
        ) {
            $payload['responded_at'] =
                now();
        }

        if (
            $this->columnExists(
                $table,
                'lecturer_id'
            )
        ) {
            $payload['lecturer_id'] =
                Auth::id();
        }

        if (
            $this->columnExists(
                $table,
                'updated_at'
            )
        ) {
            $payload['updated_at'] =
                now();
        }

        DB::table($table)
            ->where(
                'id',
                $record->record_id
            )
            ->update($payload);

        $this->closeDetail();

        session()->flash(
            'success',
            'Tanggapan bimbingan berhasil disimpan.'
        );
    }

    public function approveFinalReport(): void
    {
        $this->processFinalReport(
            'Disetujui',
            false
        );
    }

    public function requestFinalReportRevision(): void
    {
        $this->processFinalReport(
            'Perlu_Revisi',
            true
        );
    }

    protected function processFinalReport(
        string $status,
        bool $noteRequired
    ): void {
        if ($this->moduleKey() !== 'final_reports') {
            return;
        }

        $rules = [
            'reviewNote' => [
                $noteRequired
                    ? 'required'
                    : 'nullable',
                'string',
                'max:3000',
            ],
        ];

        if ($noteRequired) {
            $rules['reviewNote'][] =
                'min:5';
        }

        $this->validate(
            $rules,
            [
                'reviewNote.required' =>
                    'Catatan revisi wajib diisi.',

                'reviewNote.min' =>
                    'Catatan revisi minimal 5 karakter.',

                'reviewNote.max' =>
                    'Catatan maksimal 3.000 karakter.',
            ]
        );

        $record = $this->selectedId
            ? $this->findSelectedRecord(
                $this->selectedId
            )
            : null;

        if (! $record) {
            $this->closeDetail();

            session()->flash(
                'error',
                'Laporan Akhir tidak ditemukan.'
            );

            return;
        }

        $config = $this->moduleConfig();

        $table = $config['table'];

        $payload = [];

        $statusColumn =
            $this->firstExistingColumn(
                $table,
                $config['status_columns']
            );

        if ($statusColumn) {
            $payload[$statusColumn] =
                $status;
        }

        $noteColumn =
            $this->firstExistingColumn(
                $table,
                $config['note_columns']
            );

        if ($noteColumn) {
            $payload[$noteColumn] =
                filled($this->reviewNote)
                    ? trim($this->reviewNote)
                    : 'Laporan Akhir telah disetujui oleh Dosen Pembimbing.';
        }

        foreach (
            [
                'reviewed_at',
                'lecturer_reviewed_at',
            ] as $dateColumn
        ) {
            if (
                $this->columnExists(
                    $table,
                    $dateColumn
                )
            ) {
                $payload[$dateColumn] =
                    now();

                break;
            }
        }

        foreach (
            [
                'reviewed_by',
                'lecturer_id',
                'lecturer_reviewed_by',
            ] as $userColumn
        ) {
            if (
                $this->columnExists(
                    $table,
                    $userColumn
                )
            ) {
                $payload[$userColumn] =
                    Auth::id();

                break;
            }
        }

        if (
            $this->columnExists(
                $table,
                'updated_at'
            )
        ) {
            $payload['updated_at'] =
                now();
        }

        DB::table($table)
            ->where(
                'id',
                $record->record_id
            )
            ->update($payload);

        $this->closeDetail();

        session()->flash(
            'success',
            $status === 'Disetujui'
                ? 'Laporan Akhir berhasil disetujui.'
                : 'Laporan Akhir dikembalikan kepada mahasiswa untuk direvisi.'
        );
    }

    public function saveAssessment(): void
    {
        if ($this->moduleKey() !== 'assessments') {
            return;
        }

        $this->validate(
            [
                'score' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:100',
                ],

                'reviewNote' => [
                    'nullable',
                    'string',
                    'max:3000',
                ],
            ],
            [
                'score.required' =>
                    'Nilai akademik wajib diisi.',

                'score.numeric' =>
                    'Nilai harus berupa angka.',

                'score.min' =>
                    'Nilai minimal 0.',

                'score.max' =>
                    'Nilai maksimal 100.',

                'reviewNote.max' =>
                    'Catatan maksimal 3.000 karakter.',
            ]
        );

        $record = $this->selectedId
            ? $this->findSelectedRecord(
                $this->selectedId
            )
            : null;

        if (! $record) {
            $this->closeDetail();

            session()->flash(
                'error',
                'Data mahasiswa tidak ditemukan.'
            );

            return;
        }

        $config = $this->moduleConfig();

        $table = $config['table'];

        if (! $this->tableExists($table)) {
            session()->flash(
                'error',
                'Tabel penilaian akademik belum tersedia.'
            );

            return;
        }

        $scoreColumn =
            $this->firstExistingColumn(
                $table,
                $config['score_columns']
            );

        if (! $scoreColumn) {
            session()->flash(
                'error',
                'Kolom nilai akademik belum tersedia.'
            );

            return;
        }

        $payload = [
            $scoreColumn =>
                $this->score,

            'updated_at' =>
                now(),
        ];

        $noteColumn =
            $this->firstExistingColumn(
                $table,
                $config['note_columns']
            );

        if ($noteColumn) {
            $payload[$noteColumn] =
                filled($this->reviewNote)
                    ? trim($this->reviewNote)
                    : null;
        }

        $statusColumn =
            $this->firstExistingColumn(
                $table,
                $config['status_columns']
            );

        if ($statusColumn) {
            $payload[$statusColumn] =
                'Selesai';
        }

        foreach (
            [
                'lecturer_id',
                'assessor_id',
                'supervisor_lecturer_id',
            ] as $lecturerColumn
        ) {
            if (
                $this->columnExists(
                    $table,
                    $lecturerColumn
                )
            ) {
                $payload[$lecturerColumn] =
                    Auth::id();

                break;
            }
        }

        foreach (
            [
                'assessed_at',
                'submitted_at',
            ] as $dateColumn
        ) {
            if (
                $this->columnExists(
                    $table,
                    $dateColumn
                )
            ) {
                $payload[$dateColumn] =
                    now();

                break;
            }
        }

        if (
            $this->columnExists(
                $table,
                'created_at'
            )
        ) {
            $payload['created_at'] =
                now();
        }

        DB::table($table)
            ->updateOrInsert(
                [
                    'internship_id' =>
                        $record->internship_id,
                ],
                $payload
            );

        $this->closeDetail();

        session()->flash(
            'success',
            'Penilaian akademik berhasil disimpan.'
        );
    }

    public function render()
    {
        $config =
            $this->moduleConfig();

        $selectedRecord =
            $this->selectedId
                ? $this->findSelectedRecord(
                    $this->selectedId
                )
                : null;

        return view(
            'livewire.lecturer.workflow-index',
            [
                'module' =>
                    $this->moduleKey(),

                'config' =>
                    $config,

                'records' =>
                    $this->records(),

                'statistics' =>
                    $this->statistics(),

                'selectedRecord' =>
                    $selectedRecord,
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    $config['page_title'],
            ]
        );
    }
}