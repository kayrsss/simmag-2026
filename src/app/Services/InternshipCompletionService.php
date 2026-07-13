<?php

namespace App\Services;

use App\Models\Internship;
use App\Support\SimmagStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InternshipCompletionService
{
    public function __construct(
        private readonly AuditTrailService $auditTrailService
    ) {
    }

    public function refresh(
        Internship $internship
    ): bool {
        if (
            ! Schema::hasTable(
                'field_assessments'
            )
            || ! Schema::hasTable(
                'lecturer_assessments'
            )
        ) {
            return false;
        }

        $hasFieldAssessment = DB::table(
            'field_assessments'
        )
            ->where(
                'internship_id',
                $internship->getKey()
            )
            ->exists();

        $hasLecturerAssessment = DB::table(
            'lecturer_assessments'
        )
            ->where(
                'internship_id',
                $internship->getKey()
            )
            ->exists();

        if (
            ! $hasFieldAssessment
            || ! $hasLecturerAssessment
        ) {
            return false;
        }

        if (
            $internship->status
            === SimmagStatus::INTERNSHIP_COMPLETED
        ) {
            return true;
        }

        $previousStatus = (string) $internship->status;

        DB::transaction(
            function () use (
                $internship,
                $previousStatus
            ): void {
                $internship->forceFill([
                    'status' =>
                        SimmagStatus::INTERNSHIP_COMPLETED,

                    'updated_at' =>
                        now(),
                ])->save();

                $this->auditTrailService
                    ->statusChanged(
                        entity: $internship,
                        previousStatus: $previousStatus,
                        newStatus:
                            SimmagStatus::INTERNSHIP_COMPLETED,
                        notes:
                            'Status magang otomatis menjadi Selesai karena Penilaian Lapangan dan Penilaian Akademik telah tersedia.'
                    );
            }
        );

        return true;
    }
}