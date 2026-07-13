<?php

namespace App\Filament\Admin\Widgets;

use App\Models\FieldAssessment;
use App\Models\FinalReport;
use App\Models\FrameworkOfReference;
use App\Models\Internship;
use App\Models\LecturerAssessment;
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
            return $this->getAdminStats();
        }

        if ($user?->hasRole('mahasiswa')) {
            return $this->getMahasiswaStats($user->id);
        }

        if ($user?->hasRole('dosen_pembimbing')) {
            return $this->getDosenPembimbingStats($user->id);
        }

        if ($user?->hasRole('pembimbing_lapangan')) {
            return $this->getPembimbingLapanganStats($user->id);
        }

        return [];
    }

    private function getAdminStats(): array
    {
        return [
            Stat::make('Mahasiswa Magang Aktif', Internship::where('status', 'aktif')->count())
                ->description('Total mahasiswa magang aktif')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),

            Stat::make('Kerangka Acuan Review', FrameworkOfReference::where('status', 'Menunggu_Review')->count())
                ->description('Menunggu peninjauan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Logbook Menunggu Validasi', Logbook::where('status', 'Menunggu_Validasi')->count())
                ->description('Perlu validasi Pembimbing Lapangan')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make('Laporan Akhir Review', FinalReport::where('status', 'Menunggu_Review')->count())
                ->description('Menunggu review Dosen Pembimbing')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('danger'),
        ];
    }

    private function getMahasiswaStats(int $userId): array
    {
        $internshipIds = Internship::where('student_id', $userId)->pluck('id');

        return [
            Stat::make('Data Magang Saya', $internshipIds->count())
                ->description('Magang yang terdaftar')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),

            Stat::make('Kerangka Acuan Saya', FrameworkOfReference::whereIn('internship_id', $internshipIds)->count())
                ->description('Dokumen Kerangka Acuan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Logbook Saya', Logbook::whereIn('internship_id', $internshipIds)->count())
                ->description('Catatan aktivitas harian')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),

            Stat::make('Laporan Akhir Saya', FinalReport::whereIn('internship_id', $internshipIds)->count())
                ->description('Dokumen laporan akhir')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('danger'),
        ];
    }

    private function getDosenPembimbingStats(int $userId): array
    {
        $internshipIds = Internship::where('supervisor_lecturer_id', $userId)->pluck('id');

        return [
            Stat::make('Mahasiswa Bimbingan', $internshipIds->count())
                ->description('Mahasiswa yang dibimbing')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Kerangka Acuan Review', FrameworkOfReference::whereIn('internship_id', $internshipIds)
                ->whereIn('status', ['Menunggu_Review', 'Disetujui_PL'])
                ->count())
                ->description('Perlu review dosen')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Laporan Akhir Review', FinalReport::whereIn('internship_id', $internshipIds)
                ->where('status', 'Menunggu_Review')
                ->count())
                ->description('Perlu review laporan')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('danger'),

            Stat::make('Penilaian Dosen', LecturerAssessment::whereIn('internship_id', $internshipIds)->count())
                ->description('Penilaian akademik')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }

    private function getPembimbingLapanganStats(int $userId): array
    {
        $internshipIds = Internship::where('submitted_by', $userId)->pluck('id');

        return [
            Stat::make('Mahasiswa Magang', $internshipIds->count())
                ->description('Mahasiswa yang didampingi')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Logbook Menunggu Validasi', Logbook::whereIn('internship_id', $internshipIds)
                ->where('status', 'Menunggu_Validasi')
                ->count())
                ->description('Perlu validasi')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make('Penilaian Lapangan', FieldAssessment::whereIn('internship_id', $internshipIds)->count())
                ->description('Penilaian kinerja')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Logbook Tervalidasi', Logbook::whereIn('internship_id', $internshipIds)
                ->where('status', 'Tervalidasi')
                ->count())
                ->description('Sudah divalidasi')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}