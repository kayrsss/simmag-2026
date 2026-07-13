<?php

namespace App\Support;

use InvalidArgumentException;

final class SimmagStatus
{
    public const INTERNSHIP_DRAFT = 'Draft';

    public const INTERNSHIP_WAITING_FRAMEWORK = 'Menunggu_KA';

    public const INTERNSHIP_ACTIVE = 'Aktif';

    public const INTERNSHIP_COMPLETED = 'Selesai';

    public const INTERNSHIP_CANCELLED = 'Batal';

    public const FRAMEWORK_DRAFT = 'Draft';

    public const FRAMEWORK_WAITING_FIELD_SUPERVISOR = 'Menunggu_Review_PL';

    public const FRAMEWORK_REVISION_FIELD_SUPERVISOR = 'Perlu_Revisi_PL';

    public const FRAMEWORK_WAITING_LECTURER = 'Menunggu_Review_Dosen';

    public const FRAMEWORK_REVISION_LECTURER = 'Perlu_Revisi_Dosen';

    public const FRAMEWORK_APPROVED = 'Disetujui';

    public const LOGBOOK_DRAFT = 'Draft';

    public const LOGBOOK_WAITING_VALIDATION = 'Menunggu_Validasi';

    public const LOGBOOK_VALIDATED = 'Tervalidasi';

    public const LOGBOOK_REVISION = 'Perlu_Revisi';

    public const CONSULTATION_REQUESTED = 'Diajukan';

    public const CONSULTATION_SCHEDULED = 'Dijadwalkan';

    public const CONSULTATION_COMPLETED = 'Selesai';

    public const CONSULTATION_CANCELLED = 'Dibatalkan';

    public const FINAL_REPORT_DRAFT = 'Draft';

    public const FINAL_REPORT_WAITING_REVIEW = 'Menunggu_Review';

    public const FINAL_REPORT_REVISION = 'Perlu_Revisi';

    public const FINAL_REPORT_APPROVED = 'Disetujui';

    public static function internshipStatuses(): array
    {
        return [
            self::INTERNSHIP_DRAFT,
            self::INTERNSHIP_WAITING_FRAMEWORK,
            self::INTERNSHIP_ACTIVE,
            self::INTERNSHIP_COMPLETED,
            self::INTERNSHIP_CANCELLED,
        ];
    }

    public static function frameworkStatuses(): array
    {
        return [
            self::FRAMEWORK_DRAFT,
            self::FRAMEWORK_WAITING_FIELD_SUPERVISOR,
            self::FRAMEWORK_REVISION_FIELD_SUPERVISOR,
            self::FRAMEWORK_WAITING_LECTURER,
            self::FRAMEWORK_REVISION_LECTURER,
            self::FRAMEWORK_APPROVED,
        ];
    }

    public static function logbookStatuses(): array
    {
        return [
            self::LOGBOOK_DRAFT,
            self::LOGBOOK_WAITING_VALIDATION,
            self::LOGBOOK_VALIDATED,
            self::LOGBOOK_REVISION,
        ];
    }

    public static function consultationStatuses(): array
    {
        return [
            self::CONSULTATION_REQUESTED,
            self::CONSULTATION_SCHEDULED,
            self::CONSULTATION_COMPLETED,
            self::CONSULTATION_CANCELLED,
        ];
    }

    public static function finalReportStatuses(): array
    {
        return [
            self::FINAL_REPORT_DRAFT,
            self::FINAL_REPORT_WAITING_REVIEW,
            self::FINAL_REPORT_REVISION,
            self::FINAL_REPORT_APPROVED,
        ];
    }

    public static function frameworkTransitions(): array
    {
        return [
            self::FRAMEWORK_DRAFT => [
                self::FRAMEWORK_WAITING_FIELD_SUPERVISOR,
            ],

            self::FRAMEWORK_WAITING_FIELD_SUPERVISOR => [
                self::FRAMEWORK_REVISION_FIELD_SUPERVISOR,
                self::FRAMEWORK_WAITING_LECTURER,
            ],

            self::FRAMEWORK_REVISION_FIELD_SUPERVISOR => [
                self::FRAMEWORK_WAITING_FIELD_SUPERVISOR,
            ],

            self::FRAMEWORK_WAITING_LECTURER => [
                self::FRAMEWORK_REVISION_LECTURER,
                self::FRAMEWORK_APPROVED,
            ],

            self::FRAMEWORK_REVISION_LECTURER => [
                self::FRAMEWORK_WAITING_FIELD_SUPERVISOR,
            ],

            self::FRAMEWORK_APPROVED => [],
        ];
    }

    public static function logbookTransitions(): array
    {
        return [
            self::LOGBOOK_DRAFT => [
                self::LOGBOOK_WAITING_VALIDATION,
            ],

            self::LOGBOOK_WAITING_VALIDATION => [
                self::LOGBOOK_VALIDATED,
                self::LOGBOOK_REVISION,
            ],

            self::LOGBOOK_REVISION => [
                self::LOGBOOK_WAITING_VALIDATION,
            ],

            self::LOGBOOK_VALIDATED => [],
        ];
    }

    public static function finalReportTransitions(): array
    {
        return [
            self::FINAL_REPORT_DRAFT => [
                self::FINAL_REPORT_WAITING_REVIEW,
            ],

            self::FINAL_REPORT_WAITING_REVIEW => [
                self::FINAL_REPORT_REVISION,
                self::FINAL_REPORT_APPROVED,
            ],

            self::FINAL_REPORT_REVISION => [
                self::FINAL_REPORT_WAITING_REVIEW,
            ],

            self::FINAL_REPORT_APPROVED => [],
        ];
    }

    public static function canTransition(
        string $module,
        string $from,
        string $to
    ): bool {
        $transitions = match ($module) {
            'framework' =>
                self::frameworkTransitions(),

            'logbook' =>
                self::logbookTransitions(),

            'final_report' =>
                self::finalReportTransitions(),

            default =>
                throw new InvalidArgumentException(
                    "Modul status {$module} tidak dikenali."
                ),
        };

        return in_array(
            $to,
            $transitions[$from] ?? [],
            true
        );
    }

    public static function assertTransition(
        string $module,
        string $from,
        string $to
    ): void {
        if (
            self::canTransition(
                $module,
                $from,
                $to
            )
        ) {
            return;
        }

        throw new InvalidArgumentException(
            "Perubahan status {$module} dari {$from} menjadi {$to} tidak diperbolehkan."
        );
    }

    public static function label(string $status): string
    {
        return str_replace(
            '_',
            ' ',
            $status
        );
    }
}