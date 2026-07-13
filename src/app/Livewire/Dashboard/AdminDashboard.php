<?php

namespace App\Livewire\Dashboard;

use App\Models\Internship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class AdminDashboard extends Component
{
    private function routeUrl(
        string $routeName,
        array $parameters = []
    ): ?string {
        if (! Route::has($routeName)) {
            return null;
        }

        return route(
            $routeName,
            $parameters
        );
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

    private function statusLabel(
        ?string $status
    ): string {
        if (blank($status)) {
            return 'Belum Ada';
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
            ->title()
            ->toString();
    }

    private function statusTone(
        ?string $status
    ): string {
        $normalized =
            $this->normalizeStatus(
                $status
            );

        if (
            str_contains(
                $normalized,
                'setuju'
            )
            || str_contains(
                $normalized,
                'valid'
            )
            || str_contains(
                $normalized,
                'selesai'
            )
            || str_contains(
                $normalized,
                'aktif'
            )
            || str_contains(
                $normalized,
                'berhasil'
            )
            || str_contains(
                $normalized,
                'success'
            )
        ) {
            return 'success';
        }

        if (
            str_contains(
                $normalized,
                'revisi'
            )
            || str_contains(
                $normalized,
                'gagal'
            )
            || str_contains(
                $normalized,
                'failed'
            )
            || str_contains(
                $normalized,
                'tolak'
            )
            || str_contains(
                $normalized,
                'batal'
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
                'pending'
            )
            || str_contains(
                $normalized,
                'diajukan'
            )
            || str_contains(
                $normalized,
                'terjadwal'
            )
        ) {
            return 'warning';
        }

        if (
            str_contains(
                $normalized,
                'draft'
            )
            || str_contains(
                $normalized,
                'belum'
            )
        ) {
            return 'neutral';
        }

        return 'primary';
    }

    private function initials(
        ?string $name
    ): string {
        $initials = collect(
            preg_split(
                '/\s+/',
                trim(
                    $name
                        ?: 'Pengguna'
                )
            )
        )
            ->filter()
            ->take(2)
            ->map(
                fn (string $word): string =>
                    mb_strtoupper(
                        mb_substr(
                            $word,
                            0,
                            1
                        )
                    )
            )
            ->implode('');

        return $initials !== ''
            ? $initials
            : 'PG';
    }

    private function formatDate(
        mixed $value,
        string $fallback = '-'
    ): string {
        if (blank($value)) {
            return $fallback;
        }

        try {
            return Carbon::parse(
                $value
            )->translatedFormat(
                'd F Y, H.i'
            );
        } catch (\Throwable) {
            return $fallback;
        }
    }

    private function firstExistingColumn(
        string $table,
        array $columns
    ): ?string {
        if (! Schema::hasTable($table)) {
            return null;
        }

        foreach ($columns as $column) {
            if (
                Schema::hasColumn(
                    $table,
                    $column
                )
            ) {
                return $column;
            }
        }

        return null;
    }

    private function rowValue(
        object $row,
        array $columns,
        mixed $fallback = null
    ): mixed {
        foreach ($columns as $column) {
            if (
                property_exists(
                    $row,
                    $column
                )
                && filled(
                    $row->{$column}
                )
            ) {
                return $row->{$column};
            }
        }

        return $fallback;
    }

    private function latestRows(
        string $table,
        int $limit
    ): Collection {
        if (! Schema::hasTable($table)) {
            return collect();
        }

        $orderColumn =
            $this->firstExistingColumn(
                $table,
                [
                    'created_at',
                    'published_at',
                    'performed_at',
                    'synced_at',
                    'started_at',
                    'id',
                ]
            );

        $query = DB::table($table);

        if ($orderColumn) {
            $query->orderByDesc(
                $orderColumn
            );
        }

        return $query
            ->limit($limit)
            ->get();
    }

    private function countUsersByRoles(
        array $roles
    ): int {
        if (
            Schema::hasTable('users')
            && Schema::hasTable('roles')
            && Schema::hasTable(
                'model_has_roles'
            )
        ) {
            return DB::table('users')
                ->join(
                    'model_has_roles',
                    function ($join): void {
                        $join->on(
                            'model_has_roles.model_id',
                            '=',
                            'users.id'
                        )->where(
                            'model_has_roles.model_type',
                            User::class
                        );
                    }
                )
                ->join(
                    'roles',
                    'roles.id',
                    '=',
                    'model_has_roles.role_id'
                )
                ->whereIn(
                    'roles.name',
                    $roles
                )
                ->distinct()
                ->count('users.id');
        }

        if (
            Schema::hasTable('users')
            && Schema::hasColumn(
                'users',
                'role'
            )
        ) {
            return DB::table('users')
                ->whereIn(
                    'role',
                    $roles
                )
                ->count();
        }

        return 0;
    }

    private function activeInternshipCount(): int
    {
        if (! Schema::hasTable('internships')) {
            return 0;
        }

        if (
            ! Schema::hasColumn(
                'internships',
                'status'
            )
        ) {
            return DB::table(
                'internships'
            )->count();
        }

        return DB::table('internships')
            ->whereIn(
                DB::raw(
                    "LOWER(REPLACE(REPLACE(status, ' ', '_'), '-', '_'))"
                ),
                [
                    'aktif',
                    'magang_aktif',
                    'pelaksanaan',
                    'laporan_akhir',
                    'in_progress',
                ]
            )
            ->count();
    }

    private function unassignedInternshipCount(): int
    {
        if (! Schema::hasTable('internships')) {
            return 0;
        }

        if (
            ! Schema::hasColumn(
                'internships',
                'supervisor_lecturer_id'
            )
        ) {
            return 0;
        }

        return DB::table('internships')
            ->whereNull(
                'supervisor_lecturer_id'
            )
            ->count();
    }

    private function attentionCount(): int
    {
        $internshipIds = collect();

        $tables = [
            'internships',
            'framework_of_references',
            'logbooks',
            'final_reports',
        ];

        foreach ($tables as $table) {
            if (
                ! Schema::hasTable($table)
                || ! Schema::hasColumn(
                    $table,
                    'status'
                )
            ) {
                continue;
            }

            $query = DB::table($table)
                ->whereRaw(
                    "LOWER(status) LIKE ?",
                    [
                        '%revisi%',
                    ]
                );

            if ($table === 'internships') {
                $internshipIds = $internshipIds
                    ->merge(
                        $query->pluck('id')
                    );

                continue;
            }

            if (
                Schema::hasColumn(
                    $table,
                    'internship_id'
                )
            ) {
                $internshipIds = $internshipIds
                    ->merge(
                        $query->pluck(
                            'internship_id'
                        )
                    );
            }
        }

        return $internshipIds
            ->filter()
            ->unique()
            ->count();
    }

    private function currentPeriod(): array
    {
        if (! Schema::hasTable('periods')) {
            return [
                'name' =>
                    'Periode belum tersedia',

                'description' =>
                    'Tambahkan periode melalui Panel Admin.',
            ];
        }

        $query = DB::table('periods');

        if (
            Schema::hasColumn(
                'periods',
                'is_active'
            )
        ) {
            $query->orderByDesc(
                'is_active'
            );
        }

        if (
            Schema::hasColumn(
                'periods',
                'status'
            )
        ) {
            $query->orderByRaw(
                "
                    CASE
                        WHEN LOWER(status) IN (
                            'aktif',
                            'active',
                            'berjalan'
                        ) THEN 0
                        ELSE 1
                    END
                "
            );
        }

        $period = $query
            ->latest('id')
            ->first();

        if (! $period) {
            return [
                'name' =>
                    'Periode belum tersedia',

                'description' =>
                    'Tambahkan periode melalui Panel Admin.',
            ];
        }

        $name = $this->rowValue(
            $period,
            [
                'name',
                'period_name',
                'title',
                'semester',
            ],
            'Periode Magang'
        );

        $academicYear =
            $this->rowValue(
                $period,
                [
                    'academic_year',
                    'year',
                    'tahun_akademik',
                ]
            );

        $descriptionParts =
            collect([
                $academicYear,

                $this->rowValue(
                    $period,
                    [
                        'status',
                    ]
                ),
            ])
                ->filter()
                ->map(
                    fn ($value): string =>
                        $this->statusLabel(
                            (string) $value
                        )
                )
                ->values();

        return [
            'name' => (string) $name,

            'description' =>
                $descriptionParts
                    ->isNotEmpty()
                        ? $descriptionParts
                            ->implode(' · ')
                        : 'Periode magang SIMMAG',
        ];
    }

    private function monitoringRows(): Collection
    {
        if (! Schema::hasTable('internships')) {
            return collect();
        }

        return Internship::query()
            ->with([
                'student.programStudy',
                'supervisorLecturer',
                'company',
                'period',
                'frameworksOfReference',
                'logbooks',
                'finalReports',
            ])
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(
                function (
                    Internship $internship
                ): array {
                    $studentName =
                        $internship
                            ->student
                            ?->name
                        ?? $internship
                            ->student_name
                        ?? 'Mahasiswa';

                    $studentIdentifier =
                        $internship
                            ->student
                            ?->nim
                        ?? $internship
                            ->student
                            ?->identifier
                        ?? $internship
                            ->student_nim
                        ?? '-';

                    $programStudy =
                        $internship
                            ->student
                            ?->programStudy
                            ?->name
                        ?? $internship
                            ->program_study_name
                        ?? '-';

                    $company =
                        $internship
                            ->company
                            ?->name
                        ?? $internship
                            ->company_name
                        ?? '-';

                    $lecturer =
                        $internship
                            ->supervisorLecturer
                            ?->name
                        ?? $internship
                            ->lecturer_name
                        ?? 'Belum Ditugaskan';

                    $fieldSupervisor =
                        $internship
                            ->field_supervisor_name
                        ?? 'Belum Ditugaskan';

                    $framework =
                        $internship
                            ->frameworksOfReference
                            ->sortByDesc('id')
                            ->first();

                    $totalLogbooks =
                        $internship
                            ->logbooks
                            ->count();

                    $waitingLogbooks =
                        $internship
                            ->logbooks
                            ->filter(
                                function ($logbook): bool {
                                    $status =
                                        $this->normalizeStatus(
                                            $logbook
                                                ->status
                                        );

                                    return in_array(
                                        $status,
                                        [
                                            'diajukan',
                                            'menunggu',
                                            'menunggu_validasi',
                                            'submitted',
                                            'pending',
                                        ],
                                        true
                                    );
                                }
                            )
                            ->count();

                    $validatedLogbooks =
                        $internship
                            ->logbooks
                            ->filter(
                                function ($logbook): bool {
                                    $status =
                                        $this->normalizeStatus(
                                            $logbook
                                                ->status
                                        );

                                    return in_array(
                                        $status,
                                        [
                                            'tervalidasi',
                                            'validated',
                                            'disetujui',
                                        ],
                                        true
                                    );
                                }
                            )
                            ->count();

                    $logbookStatus =
                        match (true) {
                            $waitingLogbooks > 0 =>
                                "{$waitingLogbooks} Menunggu",

                            $totalLogbooks > 0 =>
                                "{$validatedLogbooks}/{$totalLogbooks} Tervalidasi",

                            default =>
                                'Belum Ada',
                        };

                    $internshipRoute =
                        $this->routeUrl(
                            'filament.admin.resources.internships.view',
                            [
                                'record' =>
                                    $internship
                                        ->getRouteKey(),
                            ]
                        )
                        ?? $this->routeUrl(
                            'filament.admin.resources.internships.edit',
                            [
                                'record' =>
                                    $internship
                                        ->getRouteKey(),
                            ]
                        );

                    return [
                        'id' =>
                            $internship->id,

                        'initials' =>
                            $this->initials(
                                $studentName
                            ),

                        'student' =>
                            $studentName,

                        'identifier' =>
                            $studentIdentifier,

                        'program_study' =>
                            $programStudy,

                        'company' =>
                            $company,

                        'lecturer' =>
                            $lecturer,

                        'field_supervisor' =>
                            $fieldSupervisor,

                        'framework_status' =>
                            $this->statusLabel(
                                $framework?->status
                            ),

                        'framework_tone' =>
                            $this->statusTone(
                                $framework?->status
                            ),

                        'logbook_status' =>
                            $logbookStatus,

                        'logbook_tone' =>
                            $waitingLogbooks > 0
                                ? 'warning'
                                : (
                                    $totalLogbooks > 0
                                        ? 'success'
                                        : 'neutral'
                                ),

                        'internship_status' =>
                            $this->statusLabel(
                                $internship->status
                            ),

                        'internship_tone' =>
                            $this->statusTone(
                                $internship->status
                            ),

                        'detail_url' =>
                            $internshipRoute,

                        'audit_url' =>
                            $this->routeUrl(
                                'filament.admin.resources.audit-trails.index'
                            ),
                    ];
                }
            );
    }

    private function auditRows(): Collection
    {
        $rows = $this->latestRows(
            'audit_trails',
            6
        );

        if ($rows->isEmpty()) {
            return collect();
        }

        $actorColumn =
            $this->firstExistingColumn(
                'audit_trails',
                [
                    'user_id',
                    'actor_id',
                    'causer_id',
                ]
            );

        $users = collect();

        if ($actorColumn) {
            $userIds = $rows
                ->pluck($actorColumn)
                ->filter()
                ->unique()
                ->values();

            if ($userIds->isNotEmpty()) {
                $users = User::query()
                    ->with('roles')
                    ->whereIn(
                        'id',
                        $userIds
                    )
                    ->get()
                    ->keyBy('id');
            }
        }

        return $rows->map(
            function (
                object $row
            ) use (
                $actorColumn,
                $users
            ): array {
                $user = $actorColumn
                    ? $users->get(
                        $row->{$actorColumn}
                            ?? null
                    )
                    : null;

                $actorName =
                    $this->rowValue(
                        $row,
                        [
                            'actor_name',
                            'user_name',
                            'causer_name',
                            'actor',
                        ]
                    )
                    ?? $user?->name
                    ?? 'Sistem SIMMAG';

                $role =
                    $this->rowValue(
                        $row,
                        [
                            'actor_role',
                            'user_role',
                            'role',
                        ]
                    )
                    ?? $user
                        ?->roles
                        ?->first()
                        ?->name
                    ?? 'Sistem';

                $activity =
                    $this->rowValue(
                        $row,
                        [
                            'description',
                            'activity',
                            'action',
                            'event',
                        ],
                        'Aktivitas sistem tercatat.'
                    );

                $time =
                    $this->rowValue(
                        $row,
                        [
                            'created_at',
                            'performed_at',
                            'updated_at',
                        ]
                    );

                return [
                    'initials' =>
                        $this->initials(
                            (string) $actorName
                        ),

                    'actor' =>
                        (string) $actorName,

                    'role' =>
                        $this->statusLabel(
                            (string) $role
                        ),

                    'activity' =>
                        (string) $activity,

                    'time' =>
                        $this->formatDate(
                            $time
                        ),

                    'ip' =>
                        (string) $this->rowValue(
                            $row,
                            [
                                'ip_address',
                                'ip',
                            ],
                            '-'
                        ),
                ];
            }
        );
    }

    private function announcementRows(): Collection
    {
        return $this->latestRows(
            'announcements',
            5
        )->map(
            function (
                object $row
            ): array {
                $status =
                    $this->rowValue(
                        $row,
                        [
                            'status',
                        ]
                    );

                if (
                    $status === null
                    && property_exists(
                        $row,
                        'is_active'
                    )
                ) {
                    $status =
                        $row->is_active
                            ? 'Aktif'
                            : 'Tidak Aktif';
                }

                $status ??= 'Aktif';

                return [
                    'title' =>
                        (string) $this->rowValue(
                            $row,
                            [
                                'title',
                                'subject',
                                'name',
                            ],
                            'Pengumuman SIMMAG'
                        ),

                    'audience' =>
                        $this->statusLabel(
                            (string) $this->rowValue(
                                $row,
                                [
                                    'audience',
                                    'target_audience',
                                    'target_role',
                                    'recipient_role',
                                ],
                                'Semua Pengguna'
                            )
                        ),

                    'published_at' =>
                        $this->formatDate(
                            $this->rowValue(
                                $row,
                                [
                                    'published_at',
                                    'start_at',
                                    'created_at',
                                ]
                            )
                        ),

                    'status' =>
                        $this->statusLabel(
                            (string) $status
                        ),

                    'tone' =>
                        $this->statusTone(
                            (string) $status
                        ),
                ];
            }
        );
    }

    private function syncRows(): Collection
    {
        return $this->latestRows(
            'siakad_sync_logs',
            5
        )->map(
            function (
                object $row
            ): array {
                $total =
                    (int) $this->rowValue(
                        $row,
                        [
                            'total_records',
                            'total',
                            'records_total',
                        ],
                        0
                    );

                $success =
                    (int) $this->rowValue(
                        $row,
                        [
                            'success_count',
                            'successful_records',
                            'success',
                        ],
                        0
                    );

                $failed =
                    (int) $this->rowValue(
                        $row,
                        [
                            'failed_count',
                            'failed_records',
                            'failed',
                        ],
                        max(
                            0,
                            $total - $success
                        )
                    );

                $status =
                    (string) $this->rowValue(
                        $row,
                        [
                            'status',
                        ],
                        $failed > 0
                            ? 'Selesai dengan Error'
                            : 'Berhasil'
                    );

                return [
                    'source' =>
                        $this->statusLabel(
                            (string) $this->rowValue(
                                $row,
                                [
                                    'source',
                                    'sync_type',
                                    'data_type',
                                    'module',
                                ],
                                'Sinkronisasi SIAKAD'
                            )
                        ),

                    'total' =>
                        $total,

                    'success' =>
                        $success,

                    'failed' =>
                        $failed,

                    'status' =>
                        $this->statusLabel(
                            $status
                        ),

                    'tone' =>
                        $this->statusTone(
                            $status
                        ),

                    'time' =>
                        $this->formatDate(
                            $this->rowValue(
                                $row,
                                [
                                    'synced_at',
                                    'started_at',
                                    'created_at',
                                ]
                            )
                        ),
                ];
            }
        );
    }

    private function links(): array
    {
        return [
            'panel' =>
                url('/admin'),

            'users' =>
                $this->routeUrl(
                    'filament.admin.resources.users.index'
                ),

            'periods' =>
                $this->routeUrl(
                    'filament.admin.resources.periods.index'
                ),

            'internships' =>
                $this->routeUrl(
                    'filament.admin.resources.internships.index'
                ),

            'companies' =>
                $this->routeUrl(
                    'filament.admin.resources.company-profiles.index'
                ),

            'programStudies' =>
                $this->routeUrl(
                    'filament.admin.resources.program-studies.index'
                ),

            'announcements' =>
                $this->routeUrl(
                    'filament.admin.resources.announcements.index'
                ),

            'announcementCreate' =>
                $this->routeUrl(
                    'filament.admin.resources.announcements.create'
                ),

            'digitalArchives' =>
                $this->routeUrl(
                    'filament.admin.resources.digital-archives.index'
                ),

            'auditTrails' =>
                $this->routeUrl(
                    'filament.admin.resources.audit-trails.index'
                ),

            'activityLogs' =>
                $this->routeUrl(
                    'filament.admin.resources.activity-logs.index'
                ),

            'syncLogs' =>
                $this->routeUrl(
                    'filament.admin.resources.siakad-sync-logs.index'
                ),
        ];
    }

    public function render()
    {
        $links = $this->links();

        $statistics = [
            [
                'label' =>
                    'Total Mahasiswa',

                'value' =>
                    $this->countUsersByRoles(
                        [
                            'mahasiswa',
                            'student',
                        ]
                    ),

                'description' =>
                    'Akun mahasiswa terdaftar',

                'icon' =>
                    'school',

                'tone' =>
                    'blue',

                'url' =>
                    $links['users'],
            ],

            [
                'label' =>
                    'Total Dosen',

                'value' =>
                    $this->countUsersByRoles(
                        [
                            'dosen_pembimbing',
                            'dosen',
                            'lecturer',
                        ]
                    ),

                'description' =>
                    'Dosen Pembimbing terdaftar',

                'icon' =>
                    'groups',

                'tone' =>
                    'violet',

                'url' =>
                    $links['users'],
            ],

            [
                'label' =>
                    'Instansi Mitra',

                'value' =>
                    Schema::hasTable(
                        'company_profiles'
                    )
                        ? DB::table(
                            'company_profiles'
                        )->count()
                        : 0,

                'description' =>
                    'Instansi magang terdaftar',

                'icon' =>
                    'apartment',

                'tone' =>
                    'cyan',

                'url' =>
                    $links['companies'],
            ],

            [
                'label' =>
                    'Magang Aktif',

                'value' =>
                    $this
                        ->activeInternshipCount(),

                'description' =>
                    'Kegiatan magang berjalan',

                'icon' =>
                    'business_center',

                'tone' =>
                    'green',

                'url' =>
                    $links['internships'],
            ],

            [
                'label' =>
                    'Belum Ditugaskan',

                'value' =>
                    $this
                        ->unassignedInternshipCount(),

                'description' =>
                    'Belum memiliki dosen',

                'icon' =>
                    'assignment_ind',

                'tone' =>
                    'orange',

                'url' =>
                    $links['internships'],
            ],

            [
                'label' =>
                    'Perlu Perhatian',

                'value' =>
                    $this
                        ->attentionCount(),

                'description' =>
                    'Data berstatus revisi',

                'icon' =>
                    'error',

                'tone' =>
                    'red',

                'url' =>
                    $links['internships'],
            ],
        ];

        $quickActions = collect([
            [
                'title' =>
                    'Kelola Pengguna',

                'description' =>
                    'Mahasiswa, dosen, PL, dan admin',

                'icon' =>
                    'manage_accounts',

                'tone' =>
                    'blue',

                'url' =>
                    $links['users'],
            ],

            [
                'title' =>
                    'Kelola Periode',

                'description' =>
                    'Atur periode pelaksanaan magang',

                'icon' =>
                    'date_range',

                'tone' =>
                    'violet',

                'url' =>
                    $links['periods'],
            ],

            [
                'title' =>
                    'Penugasan Magang',

                'description' =>
                    'Atur dosen dan Pembimbing Lapangan',

                'icon' =>
                    'assignment_ind',

                'tone' =>
                    'orange',

                'url' =>
                    $links['internships'],
            ],

            [
                'title' =>
                    'Kelola Instansi',

                'description' =>
                    'Tambah dan ubah instansi mitra',

                'icon' =>
                    'apartment',

                'tone' =>
                    'cyan',

                'url' =>
                    $links['companies'],
            ],

            [
                'title' =>
                    'Arsip Digital',

                'description' =>
                    'Dokumen kegiatan magang selesai',

                'icon' =>
                    'folder_open',

                'tone' =>
                    'green',

                'url' =>
                    $links['digitalArchives'],
            ],

            [
                'title' =>
                    'Pengumuman',

                'description' =>
                    'Kelola informasi untuk pengguna',

                'icon' =>
                    'campaign',

                'tone' =>
                    'amber',

                'url' =>
                    $links['announcements'],
            ],

            [
                'title' =>
                    'Audit Trail',

                'description' =>
                    'Riwayat proses dan persetujuan',

                'icon' =>
                    'history',

                'tone' =>
                    'red',

                'url' =>
                    $links['auditTrails'],
            ],

            [
                'title' =>
                    'Sinkronisasi SIAKAD',

                'description' =>
                    'Buka riwayat sinkronisasi data',

                'icon' =>
                    'sync',

                'tone' =>
                    'blue',

                'url' =>
                    $links['syncLogs'],
            ],
        ])->filter(
            fn (array $action): bool =>
                filled($action['url'])
        )->values();

        return view(
            'livewire.dashboard.admin-dashboard',
            [
                'adminName' =>
                    auth()->user()?->name
                    ?? 'Administrator SIMMAG',

                'adminIdentifier' =>
                    auth()->user()?->username
                    ?? auth()->user()?->email
                    ?? 'admin.simmag',

                'period' =>
                    $this->currentPeriod(),

                'statistics' =>
                    $statistics,

                'quickActions' =>
                    $quickActions,

                'monitoringRows' =>
                    $this->monitoringRows(),

                'auditRows' =>
                    $this->auditRows(),

                'announcementRows' =>
                    $this->announcementRows(),

                'syncRows' =>
                    $this->syncRows(),

                'links' =>
                    $links,
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    'Dashboard Admin Fakultas',

                'role' =>
                    'admin_fakultas',
            ]
        );
    }
}