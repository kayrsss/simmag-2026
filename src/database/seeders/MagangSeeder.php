<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\Internship;
use App\Models\Period;
use App\Models\User;
use Illuminate\Database\Seeder;

class MagangSeeder extends Seeder
{
    public function run(): void
    {
        $period = Period::query()->first();
        $companies = CompanyProfile::query()->where('is_active', true)->get();

        $students = User::query()
            ->where('role', 'mahasiswa')
            ->limit(10)
            ->get();

        $lecturers = User::query()
            ->where('role', 'dosen_pembimbing')
            ->get();

        if (! $period || $companies->isEmpty() || $students->isEmpty() || $lecturers->isEmpty()) {
            return;
        }

        foreach ($students as $index => $student) {
            $company = $companies[$index % $companies->count()];
            $lecturer = $lecturers[$index % $lecturers->count()];

            Internship::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'period_id' => $period->id,
                ],
                [
                    'student_name' => $student->name,
                    'student_nim' => $student->nim ?? $student->identifier,
                    'student_email' => $student->email,
                    'program_study_name' => optional($student->programStudy)->name ?? 'Sistem Informasi',
                    'university_name' => $student->institution_name ?? 'Universitas Esa Unggul',

                    'supervisor_lecturer_id' => $lecturer->id,
                    'lecturer_name' => $lecturer->name,
                    'lecturer_identifier' => $lecturer->identifier,

                    'company_id' => $company->id,
                    'company_name' => $company->name,

                    'field_supervisor_name' => $company->pic_name,
                    'field_supervisor_position' => $company->pic_position,
                    'field_supervisor_phone' => $company->pic_phone,
                    'field_supervisor_email' => $company->email,

                    'status' => 'approved',
                    'submitted_by' => $student->id,
                    'submitted_at' => now(),
                    'started_at' => now()->startOfMonth(),
                    'ended_at' => now()->addMonths(3)->endOfMonth(),
                ]
            );
        }
    }
}