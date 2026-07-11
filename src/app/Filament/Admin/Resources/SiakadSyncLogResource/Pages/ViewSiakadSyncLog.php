<?php

namespace App\Filament\Admin\Resources\SiakadSyncLogResource\Pages;

use App\Filament\Admin\Resources\SiakadSyncLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSiakadSyncLog extends ViewRecord
{
    protected static string $resource = SiakadSyncLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}