<?php

namespace App\Livewire\Shared;

use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementIndex extends Component
{
    use WithPagination;

    public string $search = '';

    protected array $queryString = [
        'search' => [
            'except' => '',
        ],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    private function firstExistingColumn(
        array $columns
    ): ?string {
        if (! Schema::hasTable('announcements')) {
            return null;
        }

        foreach ($columns as $column) {
            if (
                Schema::hasColumn(
                    'announcements',
                    $column
                )
            ) {
                return $column;
            }
        }

        return null;
    }

    private function emptyPaginator(): LengthAwarePaginator
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

    private function query(): ?Builder
    {
        if (! Schema::hasTable('announcements')) {
            return null;
        }

        $titleColumn =
            $this->firstExistingColumn([
                'title',
                'name',
                'subject',
            ]);

        $contentColumn =
            $this->firstExistingColumn([
                'content',
                'body',
                'message',
                'description',
            ]);

        $dateColumn =
            $this->firstExistingColumn([
                'published_at',
                'start_at',
                'created_at',
            ]);

        $audienceColumn =
            $this->firstExistingColumn([
                'target_role',
                'audience',
                'role',
            ]);

        $query = DB::table(
            'announcements'
        )->select('id');

        if ($titleColumn) {
            $query->addSelect(
                "{$titleColumn} as title"
            );
        } else {
            $query->selectRaw(
                "'Pengumuman SIMMAG' as title"
            );
        }

        if ($contentColumn) {
            $query->addSelect(
                "{$contentColumn} as content"
            );
        } else {
            $query->selectRaw(
                "'-' as content"
            );
        }

        if ($dateColumn) {
            $query->addSelect(
                "{$dateColumn} as published_at"
            );
        } else {
            $query->selectRaw(
                'NULL as published_at'
            );
        }

        if ($audienceColumn) {
            $query->addSelect(
                "{$audienceColumn} as audience"
            );
        } else {
            $query->selectRaw(
                "'Semua Pengguna' as audience"
            );
        }

        if (
            Schema::hasColumn(
                'announcements',
                'is_active'
            )
        ) {
            $query->where(
                'is_active',
                true
            );
        }

        if (trim($this->search) !== '') {
            $keyword =
                '%' . trim($this->search) . '%';

            $query->where(
                function (Builder $searchQuery) use (
                    $keyword,
                    $titleColumn,
                    $contentColumn
                ): void {
                    if ($titleColumn) {
                        $searchQuery->where(
                            $titleColumn,
                            'like',
                            $keyword
                        );
                    }

                    if ($contentColumn) {
                        $titleColumn
                            ? $searchQuery->orWhere(
                                $contentColumn,
                                'like',
                                $keyword
                            )
                            : $searchQuery->where(
                                $contentColumn,
                                'like',
                                $keyword
                            );
                    }
                }
            );
        }

        return $query;
    }

    public function render()
    {
        $query = $this->query();

        $announcements = $query
            ? $query
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->paginate(10)
            : $this->emptyPaginator();

        return view(
            'livewire.shared.announcement-index',
            [
                'announcements' =>
                    $announcements,
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    'Pengumuman',
            ]
        );
    }
}