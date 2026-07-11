<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\DashboardMonitoringStats;
use App\Filament\Admin\Widgets\LatestAccessLogs;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard SIMMAG';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'lg' => 12,
        ];
    }

    public function getWidgets(): array
    {
        return [
            DashboardMonitoringStats::class,
            LatestAccessLogs::class,
        ];
    }
}