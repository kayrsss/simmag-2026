<?php

namespace App\Filament\Admin\Widgets;

use App\Models\FinalReport;
use App\Models\FrameworkOfReference;
use App\Models\Internship;
use App\Models\Logbook;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardMonitoringStats extends BaseWidget
{
    protected static ?int $sort = 1;


    protected function getColumns(): int
    {
        return 4;
    }


    protected function getStats(): array
    {
        $user = auth()->user();


        if ($user?->hasRole('admin')) {

            return $this->adminStats();

        }


        if ($user?->hasRole('mahasiswa')) {

            return $this->studentStats($user->id);

        }


        return [];

    }



    private function adminStats(): array
    {

        return [

            Stat::make(
                'Mahasiswa Aktif',
                Internship::where('status','aktif')->count()
            )

            ->description('Total mahasiswa yang sedang magang')

            ->descriptionIcon('heroicon-m-user-group')

            ->color('success'),



            Stat::make(
                'Kerangka Acuan',
                FrameworkOfReference::where(
                    'status',
                    FrameworkOfReference::STATUS_MENUNGGU_REVIEW
                )->count()
            )

            ->description('Menunggu review')

            ->descriptionIcon('heroicon-m-document-text')

            ->color('warning'),



            Stat::make(
                'Logbook Pending',
                Logbook::where(
                    'status',
                    'Menunggu_Validasi'
                )->count()
            )

            ->description('Perlu validasi pembimbing')

            ->descriptionIcon('heroicon-m-clipboard-document-check')

            ->color('info'),



            Stat::make(
                'Laporan Akhir',
                FinalReport::where(
                    'status',
                    FinalReport::STATUS_MENUNGGU_REVIEW
                )->count()
            )

            ->description('Menunggu pemeriksaan')

            ->descriptionIcon('heroicon-m-document-check')

            ->color('danger'),


        ];

    }





    private function studentStats(int $userId): array
    {

        $internship =
            Internship::where(
                'student_id',
                $userId
            )
            ->first();



        if(! $internship){

            return [];

        }



        return [

            Stat::make(
                'Status Magang',
                ucfirst($internship->status)
            )

            ->description('Status kegiatan saat ini')

            ->descriptionIcon('heroicon-m-briefcase')

            ->color('success'),



            Stat::make(
                'Kerangka Acuan',
                FrameworkOfReference::where(
                    'internship_id',
                    $internship->id
                )->count()
            )

            ->description('Dokumen magang')

            ->descriptionIcon('heroicon-m-document')

            ->color('warning'),



            Stat::make(
                'Logbook',
                Logbook::where(
                    'internship_id',
                    $internship->id
                )->count()
            )

            ->description('Aktivitas tercatat')

            ->descriptionIcon('heroicon-m-clipboard-document-list')

            ->color('info'),



            Stat::make(
                'Laporan',
                FinalReport::where(
                    'internship_id',
                    $internship->id
                )->count()
            )

            ->description('Dokumen akhir')

            ->descriptionIcon('heroicon-m-document-check')

            ->color('danger'),

        ];

    }
}