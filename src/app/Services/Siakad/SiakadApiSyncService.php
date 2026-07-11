<?php

namespace App\Services\Siakad;

use Illuminate\Support\Facades\Http;
use Throwable;

class SiakadApiSyncService extends SiakadMockSyncService
{
    public function sync(): array
    {
        $url = env('SIAKAD_API_BASE_URL') ?: env('SIAKAD_MOCK_API_URL');

        if (! $url) {
            return parent::sync();
        }

        try {
            $request = Http::timeout((int) env('SIAKAD_API_TIMEOUT', 30));

            if (env('SIAKAD_API_TOKEN')) {
                $request = $request->withToken(env('SIAKAD_API_TOKEN'));
            }

            $response = $request->get($url);

            if (! $response->successful()) {
                return parent::sync();
            }

            $payload = $response->json();

            if (! is_array($payload)) {
                return parent::sync();
            }

            return $this->syncPayload($this->normalizePayload($payload), 'api');
        } catch (Throwable) {
            return parent::sync();
        }
    }

    protected function normalizePayload(array $payload): array
    {
        return [
            'program_studies' => $payload['program_studies']
                ?? $payload['prodi']
                ?? $payload['programStudies']
                ?? [],

            'periods' => $payload['periods']
                ?? $payload['period']
                ?? [[
                    'academic_year' => '2025/2026',
                    'semester' => 'Genap',
                    'start_date' => '2026-02-01',
                    'end_date' => '2026-06-30',
                    'status' => 'aktif',
                    'is_active' => true,
                ]],

            'companies' => $payload['companies']
                ?? $payload['company_profiles']
                ?? $payload['companies_profiles']
                ?? [[
                    'name' => 'PT Teknologi Nusantara',
                    'address' => 'Jakarta',
                    'phone' => '021123456',
                    'email' => 'hrd@teknologi.test',
                    'pic_name' => 'Karina Wnr',
                    'pic_position' => 'HR Internship Supervisor',
                    'pic_phone' => '081234567890',
                    'is_active' => true,
                ]],

            'students' => $payload['students']
                ?? $payload['mahasiswa']
                ?? [],

            'lecturers' => $payload['lecturers']
                ?? $payload['dosen']
                ?? [],

            'field_supervisors' => $payload['field_supervisors']
                ?? $payload['pembimbing_lapangan']
                ?? [[
                    'code' => 'PL001',
                    'name' => 'Karina Wnr',
                    'email' => 'pl@simmag.com',
                    'phone' => '083333333333',
                ]],

            'internships' => $payload['internships']
                ?? $payload['supervisor_assignments']
                ?? $payload['assignments']
                ?? [],
        ];
    }
}