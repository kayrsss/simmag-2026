<?php

namespace App\Services\Siakad;

use App\Models\CompanyProfile;
use App\Models\Period;
use App\Models\ProgramStudy;
use App\Models\SiakadSyncLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class SiakadMockSyncService
{
    public function handle(): array
    {
        return $this->sync();
    }

    public function run(): array
    {
        return $this->sync();
    }

    public function execute(): array
    {
        return $this->sync();
    }

    public function syncAll(): array
    {
        return $this->sync();
    }

    public function sync(): array
    {
        return DB::transaction(function () {
            $startedAt = now();

            $this->ensureRoles();

            $programStudies = $this->syncProgramStudies();
            $periods = $this->syncPeriods();
            $companies = $this->syncCompanies();
            $lecturers = $this->syncLecturers();
            $students = $this->syncStudents();
            $fieldSupervisor = $this->syncFieldSupervisor();

            $totalRecords = count($programStudies)
                + count($periods)
                + count($companies)
                + count($lecturers)
                + count($students)
                + count($fieldSupervisor);

            $log = SiakadSyncLog::create([
                'sync_type' => 'dummy',
                'status' => 'success',
                'total_inserted' => $totalRecords,
                'total_updated' => 0,
                'total_failed' => 0,
                'message' => 'Sinkronisasi mock berhasil. Data referensi siap digunakan untuk Data Magang.',
                'executed_by' => auth()->id(),
                'started_at' => $startedAt,
                'finished_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Sinkronisasi SIAKAD berhasil.',
                'log_id' => $log->id,
                'summary' => [
                    'program_studies' => count($programStudies),
                    'periods' => count($periods),
                    'companies' => count($companies),
                    'lecturers' => count($lecturers),
                    'students' => count($students),
                    'field_supervisors' => count($fieldSupervisor),
                    'internships_created' => 0,
                ],
            ];
        });
    }

    protected function ensureRoles(): void
    {
        foreach ([
            'admin',
            'mahasiswa',
            'dosen_pembimbing',
            'pembimbing_lapangan',
        ] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    protected function syncProgramStudies(): array
    {
        $items = [
            ['code' => 'TI', 'name' => 'Teknik Informatika', 'is_active' => true],
            ['code' => 'SI', 'name' => 'Sistem Informasi', 'is_active' => true],
        ];

        foreach ($items as $item) {
            ProgramStudy::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }

        return $items;
    }

    protected function syncPeriods(): array
    {
        $items = [
            [
                'academic_year' => '2025/2026',
                'semester' => 'Genap',
                'start_date' => '2026-02-01',
                'end_date' => '2026-06-30',
                'status' => 'active',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            Period::updateOrCreate(
                [
                    'academic_year' => $item['academic_year'],
                    'semester' => $item['semester'],
                ],
                $item
            );
        }

        return $items;
    }

    protected function syncCompanies(): array
    {
        $items = [
            [
                'name' => 'PT Teknologi Nusantara',
                'address' => 'Jakarta Barat',
                'phone' => '021111111',
                'email' => 'hr@teknologinusantara.test',
                'pic_name' => 'Karina Wnr',
                'pic_position' => 'HR Internship Supervisor',
                'pic_phone' => '083333333333',
                'is_active' => true,
            ],
            [
                'name' => 'PT Digital Kreatif Indonesia',
                'address' => 'Jakarta Selatan',
                'phone' => '021222222',
                'email' => 'hr@digitalkreatif.test',
                'pic_name' => 'Rama Putra',
                'pic_position' => 'IT Supervisor',
                'pic_phone' => '083333333334',
                'is_active' => true,
            ],
            [
                'name' => 'PT Solusi Data Mandiri',
                'address' => 'Tangerang',
                'phone' => '021333333',
                'email' => 'hr@solusidata.test',
                'pic_name' => 'Nadia Kirana',
                'pic_position' => 'Data Analyst Lead',
                'pic_phone' => '083333333335',
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            CompanyProfile::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }

        return $items;
    }

    protected function syncLecturers(): array
    {
        $ti = ProgramStudy::where('code', 'TI')->first();
        $si = ProgramStudy::where('code', 'SI')->first();

        $items = [
            [
                'name' => 'Dr. Andi Pratama',
                'email' => 'dospem@simmag.com',
                'nidn' => '0410019001',
                'nip' => '198901012020121001',
                'phone' => '081111111111',
                'program_study_id' => $ti?->id,
            ],
            [
                'name' => 'Dr. Budi Santoso',
                'email' => 'dospem2@simmag.com',
                'nidn' => '0410019002',
                'nip' => '198902022020121002',
                'phone' => '081111111112',
                'program_study_id' => $si?->id,
            ],
            [
                'name' => 'Ibu Sari Wulandari',
                'email' => 'dospem3@simmag.com',
                'nidn' => '0410019003',
                'nip' => '198903032020121003',
                'phone' => '081111111113',
                'program_study_id' => $ti?->id,
            ],
            [
                'name' => 'Pak Raka Firmansyah',
                'email' => 'dospem4@simmag.com',
                'nidn' => '0410019004',
                'nip' => '198904042020121004',
                'phone' => '081111111114',
                'program_study_id' => $si?->id,
            ],
        ];

        foreach ($items as $item) {
            $user = User::updateOrCreate(
    ['nidn' => $item['nidn']],
    $this->userPayload(array_merge($item, [
        'role' => 'dosen_pembimbing',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
        'is_active' => true,
    ]))
);

            $user->syncRoles(['dosen_pembimbing']);
        }

        return $items;
    }

    protected function syncStudents(): array
    {
        $ti = ProgramStudy::where('code', 'TI')->first();
        $si = ProgramStudy::where('code', 'SI')->first();

        $items = [
            [
                'name' => 'Kayers Abigail',
                'email' => 'mahasiswa@simmag.com',
                'nim' => '20230802111',
                'phone' => '082111111111',
                'program_study_id' => $ti?->id,
            ],
            [
                'name' => 'Alya Putri Ramadhani',
                'email' => 'alya@simmag.com',
                'nim' => '20230802112',
                'phone' => '082111111112',
                'program_study_id' => $si?->id,
            ],
            [
                'name' => 'Dimas Arya Saputra',
                'email' => 'dimas@simmag.com',
                'nim' => '20230802113',
                'phone' => '082111111113',
                'program_study_id' => $ti?->id,
            ],
            [
                'name' => 'Nabila Putri Lestari',
                'email' => 'nabila@simmag.com',
                'nim' => '20230802114',
                'phone' => '082111111114',
                'program_study_id' => $si?->id,
            ],
            [
                'name' => 'Rafi Maulana Hakim',
                'email' => 'rafi@simmag.com',
                'nim' => '20230802115',
                'phone' => '082111111115',
                'program_study_id' => $ti?->id,
            ],
        ];

        foreach ($items as $item) {
            $user = User::updateOrCreate(
                ['nim' => $item['nim']],
                $this->userPayload(array_merge($item, [
                    'role' => 'mahasiswa',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]))
            );

            $user->syncRoles(['mahasiswa']);
        }

        return $items;
    }

    protected function syncFieldSupervisor(): array
    {
        $items = [
            [
                'name' => 'Karina Wnr',
                'email' => 'pl@simmag.com',
                'phone' => '083333333333',
            ],
        ];

        foreach ($items as $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                $this->userPayload(array_merge($item, [
                    'role' => 'pembimbing_lapangan',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]))
            );

            $user->syncRoles(['pembimbing_lapangan']);
        }

        return $items;
    }

    protected function userPayload(array $data): array
    {
        $payload = [];

        foreach ($data as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $payload[$column] = $value;
            }
        }

        return $payload;
    }
}
