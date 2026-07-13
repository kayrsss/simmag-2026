<?php

namespace App\Livewire\Dashboard;

use App\Models\Internship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class MahasiswaDashboard extends Component
{
    private function routeUrl(
        string $routeName
    ): ?string {
        return Route::has($routeName)
            ? route($routeName)
            : null;
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
            return 'Belum tersedia';
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

    private function currentInternship(): ?Internship
    {
        if (
            ! Schema::hasTable(
                'internships'
            )
        ) {
            return null;
        }

        return Internship::query()
            ->with([
                'student.programStudy',
                'company',
                'period',
                'supervisorLecturer',
                'frameworksOfReference',
                'logbooks',
                'consultations',
                'finalReports',
                'fieldAssessment',
                'lecturerAssessment',
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

    private function latestAnnouncements(
        int $limit = 4
    ): Collection {
        if (
            ! Schema::hasTable(
                'announcements'
            )
        ) {
            return collect();
        }

        $query = DB::table(
            'announcements'
        );

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

        if (
            Schema::hasColumn(
                'announcements',
                'published_at'
            )
        ) {
            $query->orderByDesc(
                'published_at'
            );
        } elseif (
            Schema::hasColumn(
                'announcements',
                'created_at'
            )
        ) {
            $query->orderByDesc(
                'created_at'
            );
        } else {
            $query->orderByDesc('id');
        }

        return $query
            ->limit($limit)
            ->get()
            ->map(
                function (
                    object $announcement
                ): array {
                    $title =
                        $announcement->title
                        ?? $announcement->subject
                        ?? $announcement->name
                        ?? 'Pengumuman SIMMAG';

                    $content =
                        $announcement->summary
                        ?? $announcement->description
                        ?? $announcement->content
                        ?? null;

                    $date =
                        $announcement->published_at
                        ?? $announcement->created_at
                        ?? null;

                    return [
                        'title' =>
                            (string) $title,

                        'description' =>
                            filled($content)
                                ? Str::limit(
                                    strip_tags(
                                        (string) $content
                                    ),
                                    120
                                )
                                : null,

                        'date' =>
                            filled($date)
                                ? Carbon::parse(
                                    $date
                                )
                                : null,

                        'url' =>
                            $this->routeUrl(
                                'announcements.index'
                            ),
                    ];
                }
            );
    }

    private function administrator(): ?User
    {
        if (
            ! Schema::hasTable('users')
        ) {
            return null;
        }

        if (
            Schema::hasTable('roles')
            && Schema::hasTable(
                'model_has_roles'
            )
        ) {
            $adminId = DB::table('users')
                ->join(
                    'model_has_roles',
                    function ($join): void {
                        $join->on(
                            'users.id',
                            '=',
                            'model_has_roles.model_id'
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
                    [
                        'admin',
                        'administrator',
                        'admin_fakultas',
                        'super_admin',
                    ]
                )
                ->orderBy('users.id')
                ->value('users.id');

            if ($adminId) {
                return User::query()
                    ->find($adminId);
            }
        }

        if (
            Schema::hasColumn(
                'users',
                'role'
            )
        ) {
            return User::query()
                ->whereIn(
                    'role',
                    [
                        'admin',
                        'administrator',
                        'admin_fakultas',
                        'super_admin',
                    ]
                )
                ->oldest('id')
                ->first();
        }

        return null;
    }

    private function fieldSupervisor(
        ?Internship $internship
    ): ?User {
        if (! $internship) {
            return null;
        }

        if (
            Schema::hasColumn(
                'internships',
                'field_supervisor_id'
            )
            && filled(
                $internship
                    ->field_supervisor_id
            )
        ) {
            return User::query()
                ->find(
                    $internship
                        ->field_supervisor_id
                );
        }

        if (
            filled(
                $internship
                    ->field_supervisor_email
            )
        ) {
            return User::query()
                ->where(
                    'email',
                    $internship
                        ->field_supervisor_email
                )
                ->first();
        }

        return null;
    }

    private function contactFromUser(
        ?User $user,
        string $role,
        string $tone
    ): array {
        return [
            'role' => $role,

            'name' =>
                $user?->name
                ?? 'Belum ditentukan',

            'email' =>
                $user?->email,

            'phone' =>
                $user?->phone,

            'tone' =>
                $tone,
        ];
    }

    private function fieldSupervisorContact(
        ?Internship $internship,
        ?User $fieldSupervisor
    ): array {
        return [
            'role' =>
                'Pembimbing Lapangan',

            'name' =>
                $fieldSupervisor?->name
                ?? $internship
                    ?->field_supervisor_name
                ?? 'Belum ditentukan',

            'email' =>
                $fieldSupervisor?->email
                ?? $internship
                    ?->field_supervisor_email,

            'phone' =>
                $fieldSupervisor?->phone
                ?? $internship
                    ?->field_supervisor_phone,

            'tone' =>
                'violet',
        ];
    }

    private function phoneUrl(
        ?string $phone
    ): ?string {
        if (blank($phone)) {
            return null;
        }

        $cleanPhone = preg_replace(
            '/[^0-9+]/',
            '',
            $phone
        );

        return filled($cleanPhone)
            ? 'tel:' . $cleanPhone
            : null;
    }

    private function whatsappUrl(
        ?string $phone
    ): ?string {
        if (blank($phone)) {
            return null;
        }

        $cleanPhone = preg_replace(
            '/[^0-9]/',
            '',
            $phone
        );

        if (
            str_starts_with(
                $cleanPhone,
                '0'
            )
        ) {
            $cleanPhone =
                '62'
                . substr(
                    $cleanPhone,
                    1
                );
        }

        return filled($cleanPhone)
            ? 'https://wa.me/'
                . $cleanPhone
            : null;
    }

    private function logbookEvidence(
        object $logbook
    ): ?string {
        $path =
            $logbook->evidence_name
            ?? $logbook->evidence_path
            ?? $logbook->attachment_file
            ?? $logbook->file_path
            ?? null;

        if (blank($path)) {
            return null;
        }

        return basename(
            (string) $path
        );
    }

    private function tasks(
        ?Internship $internship,
        mixed $framework,
        mixed $finalReport,
        Collection $logbooks
    ): array {
        $tasks = [];

        $frameworkUrl =
            $this->routeUrl(
                'student.frameworks.index'
            );

        $logbookUrl =
            $this->routeUrl(
                'student.logbooks.index'
            );

        $consultationUrl =
            $this->routeUrl(
                'student.consultations.index'
            );

        $finalReportUrl =
            $this->routeUrl(
                'student.final-reports.index'
            );

        if (! $internship) {
            $tasks[] = [
                'title' =>
                    'Data magang belum tersedia',

                'description' =>
                    'Hubungi Administrator agar data magang, instansi, dan pembimbing dapat ditentukan.',

                'action' =>
                    null,

                'url' =>
                    null,

                'icon' =>
                    'work_off',

                'tone' =>
                    'warning',
            ];

            return $tasks;
        }

        if (! $framework) {
            $tasks[] = [
                'title' =>
                    'Kerangka Acuan belum dibuat',

                'description' =>
                    'Lengkapi Kerangka Acuan sebelum menjalankan kegiatan magang.',

                'action' =>
                    'Buat Kerangka Acuan',

                'url' =>
                    $frameworkUrl,

                'icon' =>
                    'description',

                'tone' =>
                    'warning',
            ];
        } else {
            $frameworkStatus =
                $this->normalizeStatus(
                    $framework->status
                );

            if (
                str_contains(
                    $frameworkStatus,
                    'revisi'
                )
            ) {
                $tasks[] = [
                    'title' =>
                        'Kerangka Acuan perlu direvisi',

                    'description' =>
                        'Terdapat catatan revisi dari pembimbing yang harus diperbaiki.',

                    'action' =>
                        'Lihat Revisi',

                    'url' =>
                        $frameworkUrl,

                    'icon' =>
                        'edit_document',

                    'tone' =>
                        'danger',
                ];
            } elseif (
                in_array(
                    $frameworkStatus,
                    [
                        'draft',
                        'konsep',
                    ],
                    true
                )
            ) {
                $tasks[] = [
                    'title' =>
                        'Kerangka Acuan masih draft',

                    'description' =>
                        'Periksa kembali lalu kirim Kerangka Acuan kepada Pembimbing Lapangan.',

                    'action' =>
                        'Lanjutkan',

                    'url' =>
                        $frameworkUrl,

                    'icon' =>
                        'description',

                    'tone' =>
                        'primary',
                ];
            }

            $frameworkApproved =
                in_array(
                    $frameworkStatus,
                    [
                        'disetujui',
                        'approved',
                    ],
                    true
                );

            if ($frameworkApproved) {
                $todayLogbookExists =
                    $logbooks->contains(
                        function (
                            object $logbook
                        ): bool {
                            if (
                                blank(
                                    $logbook
                                        ->activity_date
                                )
                            ) {
                                return false;
                            }

                            return Carbon::parse(
                                $logbook
                                    ->activity_date
                            )->isToday();
                        }
                    );

                if (! $todayLogbookExists) {
                    $tasks[] = [
                        'title' =>
                            'Logbook hari ini belum diisi',

                        'description' =>
                            'Catat aktivitas magang yang dikerjakan hari ini.',

                        'action' =>
                            'Isi Logbook',

                        'url' =>
                            $logbookUrl,

                        'icon' =>
                            'edit_note',

                        'tone' =>
                            'primary',
                    ];
                }
            }
        }

        if (
            $internship
                ->supervisor_lecturer_id
            && $internship
                ->consultations
                ->isEmpty()
        ) {
            $tasks[] = [
                'title' =>
                    'Belum ada riwayat bimbingan',

                'description' =>
                    'Ajukan bimbingan kepada Dosen Pembimbing untuk membahas progres magang.',

                'action' =>
                    'Ajukan Bimbingan',

                'url' =>
                    $consultationUrl,

                'icon' =>
                    'forum',

                'tone' =>
                    'primary',
            ];
        }

        if ($finalReport) {
            $finalReportStatus =
                $this->normalizeStatus(
                    $finalReport->status
                );

            if (
                str_contains(
                    $finalReportStatus,
                    'revisi'
                )
            ) {
                $tasks[] = [
                    'title' =>
                        'Laporan Akhir perlu direvisi',

                    'description' =>
                        'Dosen Pembimbing memberikan catatan revisi pada Laporan Akhir.',

                    'action' =>
                        'Unggah Revisi',

                    'url' =>
                        $finalReportUrl,

                    'icon' =>
                        'draft',

                    'tone' =>
                        'danger',
                ];
            }
        } elseif (
            filled(
                $internship->ended_at
            )
            && Carbon::parse(
                $internship->ended_at
            )->isPast()
        ) {
            $tasks[] = [
                'title' =>
                    'Laporan Akhir belum diunggah',

                'description' =>
                    'Periode magang telah selesai. Unggah Laporan Akhir untuk direview dosen.',

                'action' =>
                    'Unggah Laporan',

                'url' =>
                    $finalReportUrl,

                'icon' =>
                    'upload_file',

                'tone' =>
                    'warning',
            ];
        }

        return array_slice(
            $tasks,
            0,
            5
        );
    }

    public function render()
    {
        $student = Auth::user();

        $internship =
            $this->currentInternship();

        $framework =
            $internship
                ?->frameworksOfReference
                ->sortByDesc('id')
                ->first();

        $finalReport =
            $internship
                ?->finalReports
                ->sortByDesc('id')
                ->first();

        $logbooks =
            $internship
                ?->logbooks
                ?? collect();

        $consultations =
            $internship
                ?->consultations
                ?? collect();

        $validatedLogbooks =
            $logbooks->filter(
                function (
                    object $logbook
                ): bool {
                    $status =
                        $this->normalizeStatus(
                            $logbook->status
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
            )->count();

        $latestLogbooks =
            $logbooks
                ->sortByDesc(
                    function (
                        object $logbook
                    ): int {
                        $date =
                            $logbook
                                ->activity_date
                            ?? $logbook
                                ->created_at;

                        return filled($date)
                            ? Carbon::parse(
                                $date
                            )->timestamp
                            : 0;
                    }
                )
                ->take(5)
                ->values()
                ->map(
                    function (
                        object $logbook
                    ): array {
                        return [
                            'id' =>
                                $logbook->id,

                            'date' =>
                                filled(
                                    $logbook
                                        ->activity_date
                                )
                                    ? Carbon::parse(
                                        $logbook
                                            ->activity_date
                                    )
                                    : null,

                            'activity' =>
                                $logbook->activity
                                ?? $logbook->description
                                ?? '-',

                            'evidence' =>
                                $this->logbookEvidence(
                                    $logbook
                                ),

                            'status' =>
                                $this->statusLabel(
                                    $logbook->status
                                ),

                            'url' =>
                                $this->routeUrl(
                                    'student.logbooks.index'
                                ),
                        ];
                    }
                );

        $upcomingConsultations =
            $consultations
                ->filter(
                    function (
                        object $consultation
                    ): bool {
                        if (
                            blank(
                                $consultation
                                    ->consultation_date
                            )
                        ) {
                            return false;
                        }

                        return Carbon::parse(
                            $consultation
                                ->consultation_date
                        )
                            ->endOfDay()
                            ->isFuture();
                    }
                )
                ->sortBy(
                    fn (
                        object $consultation
                    ): int =>
                        Carbon::parse(
                            $consultation
                                ->consultation_date
                        )->timestamp
                )
                ->take(4)
                ->values()
                ->map(
                    fn (
                        object $consultation
                    ): array => [
                        'title' =>
                            $consultation->topic
                            ?? 'Bimbingan Magang',

                        'description' =>
                            $consultation->notes
                            ?? 'Bimbingan bersama Dosen Pembimbing.',

                        'status' =>
                            $this->statusLabel(
                                $consultation->status
                            ),

                        'date' =>
                            Carbon::parse(
                                $consultation
                                    ->consultation_date
                            ),

                        'url' =>
                            $this->routeUrl(
                                'student.consultations.index'
                            ),
                    ]
                );

        $agenda =
            $upcomingConsultations;

        if (
            $agenda->count() < 4
            && filled(
                $internship?->ended_at
            )
        ) {
            $agenda->push([
                'title' =>
                    'Batas Periode Magang',

                'description' =>
                    $internship
                        ?->period
                        ?->name
                    ?? 'Periode magang aktif',

                'status' =>
                    'Periode',

                'date' =>
                    Carbon::parse(
                        $internship->ended_at
                    ),

                'url' =>
                    $this->routeUrl(
                        'student.final-reports.index'
                    ),
            ]);
        }

        $lecturer =
            $internship
                ?->supervisorLecturer;

        $fieldSupervisor =
            $this->fieldSupervisor(
                $internship
            );

        $admin =
            $this->administrator();

        $contacts = collect([
            $this->contactFromUser(
                $lecturer,
                'Dosen Pembimbing',
                'green'
            ),

            $this->fieldSupervisorContact(
                $internship,
                $fieldSupervisor
            ),

            $this->contactFromUser(
                $admin,
                'Administrator',
                'blue'
            ),
        ])->map(
            function (
                array $contact
            ): array {
                $contact['phone_url'] =
                    $this->phoneUrl(
                        $contact['phone']
                    );

                $contact['whatsapp_url'] =
                    $this->whatsappUrl(
                        $contact['phone']
                    );

                return $contact;
            }
        );

        $fieldAssessment =
            $internship
                ?->fieldAssessment;

        $lecturerAssessment =
            $internship
                ?->lecturerAssessment;

        $assessmentScores =
            collect([
                $fieldAssessment
                    ?->assessed_at
                    ? (float) $fieldAssessment
                        ->overall_score
                    : null,

                $lecturerAssessment
                    ?->assessed_at
                    ? (float) $lecturerAssessment
                        ->overall_score
                    : null,
            ])->filter(
                fn ($score): bool =>
                    $score !== null
            );

        $finalScore =
            $assessmentScores
                ->count() === 2
                    ? round(
                        $assessmentScores
                            ->avg(),
                        2
                    )
                    : null;

        $frameworkStatus =
            $framework
                ? $this->statusLabel(
                    $framework->status
                )
                : 'Belum dibuat';

        $statistics = [
            [
                'label' =>
                    'Kerangka Acuan',

                'value' =>
                    $framework ? 1 : 0,

                'description' =>
                    $frameworkStatus,

                'icon' =>
                    'description',

                'tone' =>
                    str_contains(
                        strtolower(
                            $frameworkStatus
                        ),
                        'revisi'
                    )
                        ? 'warning'
                        : 'primary',

                'url' =>
                    $this->routeUrl(
                        'student.frameworks.index'
                    ),
            ],

            [
                'label' =>
                    'Total Logbook',

                'value' =>
                    $logbooks->count(),

                'description' =>
                    'Entri aktivitas magang',

                'icon' =>
                    'edit_note',

                'tone' =>
                    'orange',

                'url' =>
                    $this->routeUrl(
                        'student.logbooks.index'
                    ),
            ],

            [
                'label' =>
                    'Tervalidasi',

                'value' =>
                    $validatedLogbooks,

                'description' =>
                    'Logbook disetujui PL',

                'icon' =>
                    'task_alt',

                'tone' =>
                    'green',

                'url' =>
                    $this->routeUrl(
                        'student.logbooks.index'
                    ),
            ],

            [
                'label' =>
                    'Nilai Akhir',

                'value' =>
                    $finalScore !== null
                        ? number_format(
                            $finalScore,
                            2
                        )
                        : '-',

                'description' =>
                    $finalScore !== null
                        ? 'Penilaian telah lengkap'
                        : 'Penilaian belum lengkap',

                'icon' =>
                    'workspace_premium',

                'tone' =>
                    'violet',

                'url' =>
                    $this->routeUrl(
                        'student.assessments.index'
                    ),
            ],
        ];

        $quickActions = collect([
            [
                'label' =>
                    'Kerangka Acuan',

                'description' =>
                    'Buat dan kelola KA',

                'icon' =>
                    'description',

                'url' =>
                    $this->routeUrl(
                        'student.frameworks.index'
                    ),
            ],

            [
                'label' =>
                    'Isi Logbook',

                'description' =>
                    'Catat aktivitas harian',

                'icon' =>
                    'edit_note',

                'url' =>
                    $this->routeUrl(
                        'student.logbooks.index'
                    ),
            ],

            [
                'label' =>
                    'Ajukan Bimbingan',

                'description' =>
                    'Hubungi Dosen Pembimbing',

                'icon' =>
                    'forum',

                'url' =>
                    $this->routeUrl(
                        'student.consultations.index'
                    ),
            ],

            [
                'label' =>
                    'Laporan Akhir',

                'description' =>
                    'Unggah laporan PDF',

                'icon' =>
                    'upload_file',

                'url' =>
                    $this->routeUrl(
                        'student.final-reports.index'
                    ),
            ],

            [
                'label' =>
                    'Hasil Penilaian',

                'description' =>
                    'Lihat nilai magang',

                'icon' =>
                    'workspace_premium',

                'url' =>
                    $this->routeUrl(
                        'student.assessments.index'
                    ),
            ],

            [
                'label' =>
                    'Pengumuman',

                'description' =>
                    'Informasi terbaru',

                'icon' =>
                    'campaign',

                'url' =>
                    $this->routeUrl(
                        'announcements.index'
                    ),
            ],
        ])->filter(
            fn (array $action): bool =>
                filled($action['url'])
        )->values();

        return view(
            'livewire.dashboard.mahasiswa-dashboard',
            [
                'studentName' =>
                    $student?->name
                    ?? 'Mahasiswa',

                'internship' =>
                    $internship,

                'statistics' =>
                    $statistics,

                'quickActions' =>
                    $quickActions,

                'tasks' =>
                    $this->tasks(
                        $internship,
                        $framework,
                        $finalReport,
                        $logbooks
                    ),

                'latestLogbooks' =>
                    $latestLogbooks,

                'contacts' =>
                    $contacts,

                'agenda' =>
                    $agenda->take(4),

                'announcements' =>
                    $this->latestAnnouncements(),
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' =>
                    'Dashboard Mahasiswa',
            ]
        );
    }
}