<?php

namespace App\Livewire\Dashboard;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class DosenDashboard extends Component
{
    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function columnExists(
        string $table,
        string $column
    ): bool {
        return $this->tableExists($table)
            && Schema::hasColumn($table, $column);
    }

    private function firstExistingTable(
        array $tables
    ): ?string {
        foreach ($tables as $table) {
            if ($this->tableExists($table)) {
                return $table;
            }
        }

        return null;
    }

    private function firstExistingColumn(
        string $table,
        array $columns
    ): ?string {
        foreach ($columns as $column) {
            if ($this->columnExists($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    private function normalizedStatuses(
        array $statuses
    ): array {
        return collect($statuses)
            ->map(
                fn (string $status): string =>
                    strtolower(
                        str_replace(
                            [
                                ' ',
                                '-',
                            ],
                            '_',
                            trim($status)
                        )
                    )
            )
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function applyStatusFilter(
        Builder $query,
        string $column,
        array $statuses
    ): Builder {
        $normalizedStatuses =
            $this->normalizedStatuses($statuses);

        if ($normalizedStatuses === []) {
            return $query;
        }

        $placeholders = implode(
            ', ',
            array_fill(
                0,
                count($normalizedStatuses),
                '?'
            )
        );

        return $query->whereRaw(
            "
            LOWER(
                REPLACE(
                    REPLACE({$column}, ' ', '_'),
                    '-',
                    '_'
                )
            ) IN ({$placeholders})
            ",
            $normalizedStatuses
        );
    }

    private function assignedInternshipIds(): Collection
    {
        if (
            ! $this->tableExists('internships')
            || ! $this->columnExists(
                'internships',
                'supervisor_lecturer_id'
            )
            || ! Auth::check()
        ) {
            return collect();
        }

        return DB::table('internships')
            ->where(
                'supervisor_lecturer_id',
                Auth::id()
            )
            ->pluck('id')
            ->filter()
            ->values();
    }

    private function assignedStudentIds(
        Collection $internshipIds
    ): Collection {
        if (
            $internshipIds->isEmpty()
            || ! $this->columnExists(
                'internships',
                'student_id'
            )
        ) {
            return collect();
        }

        return DB::table('internships')
            ->whereIn(
                'id',
                $internshipIds
            )
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->values();
    }

    private function countStatuses(
        ?string $table,
        Collection $internshipIds,
        array $statuses
    ): int {
        if (
            ! $table
            || $internshipIds->isEmpty()
            || ! $this->columnExists(
                $table,
                'internship_id'
            )
            || ! $this->columnExists(
                $table,
                'status'
            )
        ) {
            return 0;
        }

        $query = DB::table($table)
            ->whereIn(
                'internship_id',
                $internshipIds
            );

        $this->applyStatusFilter(
            $query,
            "{$table}.status",
            $statuses
        );

        return $query->count();
    }

    private function academicAssessmentPendingCount(
        Collection $internshipIds
    ): int {
        if ($internshipIds->isEmpty()) {
            return 0;
        }

        $table = $this->firstExistingTable([
            'lecturer_assessments',
            'academic_assessments',
        ]);

        if (
            ! $table
            || ! $this->columnExists(
                $table,
                'internship_id'
            )
        ) {
            return 0;
        }

        $assessedInternshipCount = DB::table($table)
            ->whereIn(
                'internship_id',
                $internshipIds
            )
            ->distinct()
            ->count('internship_id');

        return max(
            0,
            $internshipIds->count()
                - $assessedInternshipCount
        );
    }

    private function recentStudents(
        Collection $internshipIds
    ): Collection {
        if (
            $internshipIds->isEmpty()
            || ! $this->tableExists('users')
            || ! $this->columnExists(
                'internships',
                'student_id'
            )
        ) {
            return collect();
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

        $query = DB::table('internships')
            ->leftJoin(
                'users',
                'users.id',
                '=',
                'internships.student_id'
            )
            ->whereIn(
                'internships.id',
                $internshipIds
            )
            ->select([
                'internships.id',
                'internships.student_id',
                'users.name as student_name',
            ]);

        if ($identifierColumn) {
            $query->addSelect(
                "users.{$identifierColumn} as student_identifier"
            );
        } else {
            $query->selectRaw(
                "'-' as student_identifier"
            );
        }

        if (
            $this->columnExists(
                'internships',
                'status'
            )
        ) {
            $query->addSelect(
                'internships.status as internship_status'
            );
        } else {
            $query->selectRaw(
                "'aktif' as internship_status"
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

        return $query
            ->orderByDesc('internships.id')
            ->limit(5)
            ->get();
    }

    private function pendingFrameworks(
        Collection $internshipIds
    ): Collection {
        $table = $this->firstExistingTable([
            'framework_of_references',
            'frameworks_of_reference',
        ]);

        if (
            ! $table
            || $internshipIds->isEmpty()
            || ! $this->columnExists(
                $table,
                'internship_id'
            )
            || ! $this->columnExists(
                $table,
                'status'
            )
        ) {
            return collect();
        }

        $titleColumn =
            $this->firstExistingColumn(
                $table,
                [
                    'title',
                    'activity_plan',
                    'document_title',
                    'name',
                ]
            );

        $versionColumn =
            $this->firstExistingColumn(
                $table,
                [
                    'version',
                    'version_number',
                    'revision_number',
                ]
            );

        $dateColumn =
            $this->firstExistingColumn(
                $table,
                [
                    'submitted_at',
                    'updated_at',
                    'created_at',
                ]
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

        $query = DB::table($table)
            ->join(
                'internships',
                'internships.id',
                '=',
                "{$table}.internship_id"
            )
            ->leftJoin(
                'users',
                'users.id',
                '=',
                'internships.student_id'
            )
            ->whereIn(
                "{$table}.internship_id",
                $internshipIds
            )
            ->select([
                "{$table}.id",
                "{$table}.internship_id",
                "{$table}.status as framework_status",
                'users.name as student_name',
            ]);

        $this->applyStatusFilter(
            $query,
            "{$table}.status",
            [
                'Menunggu Review Dosen',
                'Menunggu_Review_Dosen',
                'Menunggu Persetujuan Dosen',
                'Menunggu_Persetujuan_Dosen',
                'Disetujui PL',
                'Disetujui_PL',
                'Diajukan Ke Dosen',
                'Diajukan_Ke_Dosen',
            ]
        );

        if ($titleColumn) {
            $query->addSelect(
                "{$table}.{$titleColumn} as framework_title"
            );
        } else {
            $query->selectRaw(
                "'Kerangka Acuan Magang' as framework_title"
            );
        }

        if ($versionColumn) {
            $query->addSelect(
                "{$table}.{$versionColumn} as framework_version"
            );
        } else {
            $query->selectRaw(
                "NULL as framework_version"
            );
        }

        if ($dateColumn) {
            $query->addSelect(
                "{$table}.{$dateColumn} as submitted_at"
            );
        } else {
            $query->selectRaw(
                "NULL as submitted_at"
            );
        }

        if ($identifierColumn) {
            $query->addSelect(
                "users.{$identifierColumn} as student_identifier"
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

        if ($dateColumn) {
            $query->orderByDesc(
                "{$table}.{$dateColumn}"
            );
        } else {
            $query->orderByDesc(
                "{$table}.id"
            );
        }

        return $query
            ->limit(5)
            ->get();
    }

    private function recentLogbooks(
        Collection $internshipIds
    ): Collection {
        if (
            $internshipIds->isEmpty()
            || ! $this->tableExists('logbooks')
            || ! $this->columnExists(
                'logbooks',
                'internship_id'
            )
        ) {
            return collect();
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

        $query = DB::table('logbooks')
            ->join(
                'internships',
                'internships.id',
                '=',
                'logbooks.internship_id'
            )
            ->leftJoin(
                'users',
                'users.id',
                '=',
                'internships.student_id'
            )
            ->whereIn(
                'logbooks.internship_id',
                $internshipIds
            )
            ->select([
                'logbooks.id',
                'logbooks.internship_id',
                'users.name as student_name',
            ]);

        if (
            $this->columnExists(
                'logbooks',
                'activity_date'
            )
        ) {
            $query->addSelect(
                'logbooks.activity_date'
            );
        } else {
            $query->selectRaw(
                'NULL as activity_date'
            );
        }

        if (
            $this->columnExists(
                'logbooks',
                'activity'
            )
        ) {
            $query->addSelect(
                'logbooks.activity'
            );
        } else {
            $query->selectRaw(
                "'Aktivitas magang' as activity"
            );
        }

        if (
            $this->columnExists(
                'logbooks',
                'status'
            )
        ) {
            $query->addSelect(
                'logbooks.status'
            );
        } else {
            $query->selectRaw(
                "'draft' as status"
            );
        }

        if (
            $this->columnExists(
                'logbooks',
                'evidence_name'
            )
        ) {
            $query->addSelect(
                'logbooks.evidence_name'
            );
        } else {
            $query->selectRaw(
                'NULL as evidence_name'
            );
        }

        if ($identifierColumn) {
            $query->addSelect(
                "users.{$identifierColumn} as student_identifier"
            );
        } else {
            $query->selectRaw(
                "'-' as student_identifier"
            );
        }

        if (
            $this->columnExists(
                'logbooks',
                'activity_date'
            )
        ) {
            $query->orderByDesc(
                'logbooks.activity_date'
            );
        }

        return $query
            ->orderByDesc('logbooks.id')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        $internshipIds =
            $this->assignedInternshipIds();

        $studentIds =
            $this->assignedStudentIds(
                $internshipIds
            );

        $frameworkTable =
            $this->firstExistingTable([
                'framework_of_references',
                'frameworks_of_reference',
            ]);

        $consultationTable =
            $this->firstExistingTable([
                'consultations',
                'guidances',
            ]);

        $finalReportTable =
            $this->firstExistingTable([
                'final_reports',
                'internship_final_reports',
            ]);

        $statistics = [
            'students' =>
                $studentIds->count(),

            'frameworks_waiting' =>
                $this->countStatuses(
                    $frameworkTable,
                    $internshipIds,
                    [
                        'Menunggu Review Dosen',
                        'Menunggu_Review_Dosen',
                        'Menunggu Persetujuan Dosen',
                        'Menunggu_Persetujuan_Dosen',
                        'Disetujui PL',
                        'Disetujui_PL',
                        'Diajukan Ke Dosen',
                        'Diajukan_Ke_Dosen',
                    ]
                ),

            'consultations_waiting' =>
                $this->countStatuses(
                    $consultationTable,
                    $internshipIds,
                    [
                        'Menunggu',
                        'Menunggu Tanggapan',
                        'Menunggu_Tanggapan',
                        'Diajukan',
                        'Pending',
                    ]
                ),

            'final_reports_waiting' =>
                $this->countStatuses(
                    $finalReportTable,
                    $internshipIds,
                    [
                        'Menunggu Review',
                        'Menunggu_Review',
                        'Diajukan',
                        'Submitted',
                    ]
                ),

            'assessments_pending' =>
                $this->academicAssessmentPendingCount(
                    $internshipIds
                ),
        ];

        $user = Auth::user();

        return view(
            'livewire.dashboard.dosen-dashboard',
            [
                'lecturerName' =>
                    $user?->name
                    ?? 'Dosen Pembimbing',

                'lecturerIdentifier' =>
                    $user?->nidn
                    ?? $user?->nip
                    ?? $user?->identifier
                    ?? $user?->username
                    ?? '-',

                'statistics' =>
                    $statistics,

                'pendingFrameworks' =>
                    $this->pendingFrameworks(
                        $internshipIds
                    ),

                'recentLogbooks' =>
                    $this->recentLogbooks(
                        $internshipIds
                    ),

                'recentStudents' =>
                    $this->recentStudents(
                        $internshipIds
                    ),

                'assignmentAvailable' =>
                    $this->columnExists(
                        'internships',
                        'supervisor_lecturer_id'
                    ),

                'hasAssignments' =>
                    $internshipIds->isNotEmpty(),
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    'Beranda Dosen Pembimbing',
            ]
        );
    }
}