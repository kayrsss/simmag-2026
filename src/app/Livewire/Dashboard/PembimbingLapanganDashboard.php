<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class PembimbingLapanganDashboard extends Component
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

    private function assignmentColumn(): ?string
    {
        return $this->firstExistingColumn(
            'internships',
            [
                'field_supervisor_id',
                'pembimbing_lapangan_id',
                'supervisor_id',
                'mentor_id',
            ]
        );
    }

    private function assignedInternships(): Collection
    {
        if (! $this->tableExists('internships')) {
            return collect();
        }

        $assignmentColumn = $this->assignmentColumn();

        if ($assignmentColumn) {
            return DB::table('internships')
                ->where(
                    $assignmentColumn,
                    Auth::id()
                )
                ->orderByDesc('id')
                ->get();
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
                $this->columnExists('users', $column)
                && $this->columnExists('internships', $column)
                && filled(data_get($user, $column))
            ) {
                return DB::table('internships')
                    ->where(
                        $column,
                        data_get($user, $column)
                    )
                    ->orderByDesc('id')
                    ->get();
            }
        }

        return collect();
    }

    private function countStatuses(
        string $table,
        Collection $internshipIds,
        array $statuses
    ): int {
        if (
            $internshipIds->isEmpty()
            || ! $this->tableExists($table)
            || ! $this->columnExists($table, 'internship_id')
            || ! $this->columnExists($table, 'status')
        ) {
            return 0;
        }

        $normalizedStatuses = collect($statuses)
            ->map(
                fn (string $status): string => str($status)
                    ->lower()
                    ->replace([
                        ' ',
                        '-',
                    ], '_')
                    ->toString()
            )
            ->values();

        $placeholders = implode(
            ', ',
            array_fill(
                0,
                $normalizedStatuses->count(),
                '?'
            )
        );

        return DB::table($table)
            ->whereIn(
                'internship_id',
                $internshipIds
            )
            ->whereRaw(
                "
                LOWER(
                    REPLACE(
                        REPLACE(status, ' ', '_'),
                        '-',
                        '_'
                    )
                ) IN ({$placeholders})
                ",
                $normalizedStatuses->all()
            )
            ->count();
    }

    private function pendingAssessmentCount(
        Collection $internshipIds
    ): int {
        if ($internshipIds->isEmpty()) {
            return 0;
        }

        $assessmentTable = collect([
            'field_assessments',
            'field_supervisor_assessments',
            'assessments',
        ])->first(
            fn (string $table): bool =>
                $this->tableExists($table)
                && $this->columnExists(
                    $table,
                    'internship_id'
                )
        );

        if (! $assessmentTable) {
            return 0;
        }

        $assessedInternships = DB::table(
            $assessmentTable
        )
            ->whereIn(
                'internship_id',
                $internshipIds
            )
            ->distinct()
            ->count('internship_id');

        return max(
            0,
            $internshipIds->count()
                - $assessedInternships
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

        $identifierColumn = $this->firstExistingColumn(
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
                "'Aktif' as internship_status"
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

        return $query
            ->orderByDesc('internships.id')
            ->limit(5)
            ->get();
    }

    private function pendingLogbooks(
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

        $identifierColumn = $this->firstExistingColumn(
            'users',
            [
                'nim',
                'identifier',
                'username',
                'email',
            ]
        );

        $query = DB::table('logbooks')
            ->leftJoin(
                'users',
                'users.id',
                '=',
                'logbooks.student_id'
            )
            ->whereIn(
                'logbooks.internship_id',
                $internshipIds
            )
            ->whereRaw(
                "
                LOWER(
                    REPLACE(
                        REPLACE(logbooks.status, ' ', '_'),
                        '-',
                        '_'
                    )
                ) = ?
                ",
                [
                    'menunggu_validasi',
                ]
            )
            ->select([
                'logbooks.id',
                'logbooks.activity_date',
                'logbooks.activity',
                'logbooks.status',
                'logbooks.evidence_name',
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

        return $query
            ->orderByDesc('logbooks.activity_date')
            ->orderByDesc('logbooks.id')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        $assignedInternships =
            $this->assignedInternships();

        $internshipIds = $assignedInternships
            ->pluck('id')
            ->filter()
            ->values();

        $studentIds = $assignedInternships
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->values();

        $statistics = [
            'students' => $studentIds->count(),

            'frameworks_waiting' =>
                $this->countStatuses(
                    'framework_of_references',
                    $internshipIds,
                    [
                        'Menunggu Review',
                        'Menunggu_Review',
                        'Menunggu Review PL',
                        'Menunggu_Review_PL',
                        'Diajukan',
                    ]
                ),

            'logbooks_waiting' =>
                $this->countStatuses(
                    'logbooks',
                    $internshipIds,
                    [
                        'Menunggu Validasi',
                        'Menunggu_Validasi',
                    ]
                ),

            'assessments_pending' =>
                $this->pendingAssessmentCount(
                    $internshipIds
                ),
        ];

        return view(
            'livewire.dashboard.pembimbing-lapangan-dashboard',
            [
                'statistics' => $statistics,

                'recentStudents' =>
                    $this->recentStudents(
                        $internshipIds
                    ),

                'pendingLogbooks' =>
                    $this->pendingLogbooks(
                        $internshipIds
                    ),

                'assignmentAvailable' =>
                    filled(
                        $this->assignmentColumn()
                    ),

                'supervisorName' =>
                    Auth::user()?->name
                        ?? 'Pembimbing Lapangan',
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    'Beranda Pembimbing Lapangan',
            ]
        );
    }
}