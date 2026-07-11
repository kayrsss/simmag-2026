<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\DashboardMonitoringStats;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard Monitoring';

    protected static ?string $title = 'Dashboard Monitoring SIMMAG';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getWidgets(): array
    {
        return [
            DashboardMonitoringStats::class,
        ];
    }
}