<?php

namespace App\Livewire\Lecturer;

use App\Models\Internship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class StudentIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    protected array $queryString = [
        'search' => [
            'except' => '',
        ],
        'statusFilter' => [
            'except' => 'all',
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

    private function baseQuery(): Builder
    {
        return Internship::query()
            ->with([
                'student',
                'company',
                'period',
                'fieldSupervisor',
            ])
            ->where(
                'supervisor_lecturer_id',
                Auth::id()
            );
    }

    private function applySearch(
        Builder $query
    ): void {
        $keyword = trim($this->search);

        if ($keyword === '') {
            return;
        }

        $searchValue = '%' . $keyword . '%';

        $query->where(
            function (Builder $internshipQuery) use (
                $searchValue
            ): void {
                $internshipQuery->whereHas(
                    'student',
                    function (Builder $studentQuery) use (
                        $searchValue
                    ): void {
                        $studentQuery->where(
                            'name',
                            'like',
                            $searchValue
                        );

                        if (
                            $this->columnExists(
                                'users',
                                'email'
                            )
                        ) {
                            $studentQuery->orWhere(
                                'email',
                                'like',
                                $searchValue
                            );
                        }

                        if (
                            $this->columnExists(
                                'users',
                                'nim'
                            )
                        ) {
                            $studentQuery->orWhere(
                                'nim',
                                'like',
                                $searchValue
                            );
                        }

                        if (
                            $this->columnExists(
                                'users',
                                'identifier'
                            )
                        ) {
                            $studentQuery->orWhere(
                                'identifier',
                                'like',
                                $searchValue
                            );
                        }
                    }
                );

                $internshipQuery->orWhereHas(
                    'company',
                    fn (Builder $companyQuery) =>
                        $companyQuery->where(
                            'name',
                            'like',
                            $searchValue
                        )
                );

                if (
                    $this->columnExists(
                        'internships',
                        'field_supervisor_name'
                    )
                ) {
                    $internshipQuery->orWhere(
                        'field_supervisor_name',
                        'like',
                        $searchValue
                    );
                }

                if (
                    $this->columnExists(
                        'internships',
                        'company_name'
                    )
                ) {
                    $internshipQuery->orWhere(
                        'company_name',
                        'like',
                        $searchValue
                    );
                }
            }
        );
    }

    private function statistics(): array
    {
        $baseQuery = $this->baseQuery();

        return [
            'total' => (clone $baseQuery)
                ->distinct()
                ->count('student_id'),

            'waiting' => (clone $baseQuery)
                ->whereIn(
                    'status',
                    [
                        'draft',
                        'menunggu_ka',
                    ]
                )
                ->count(),

            'active' => (clone $baseQuery)
                ->where(
                    'status',
                    'aktif'
                )
                ->count(),

            'completed' => (clone $baseQuery)
                ->where(
                    'status',
                    'selesai'
                )
                ->count(),
        ];
    }

    public function render()
    {
        $query = $this->baseQuery();

        $this->applySearch($query);

        if ($this->statusFilter !== 'all') {
            $query->where(
                'status',
                $this->statusFilter
            );
        }

        $internships = $query
            ->orderByDesc('created_at')
            ->paginate(10);

        return view(
            'livewire.lecturer.student-index',
            [
                'internships' => $internships,

                'statistics' =>
                    $this->statistics(),
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    'Mahasiswa Bimbingan',
            ]
        );
    }
}