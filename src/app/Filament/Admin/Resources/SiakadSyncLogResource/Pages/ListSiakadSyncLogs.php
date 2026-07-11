<?php

namespace App\Filament\Admin\Resources\SiakadSyncLogResource\Pages;

use App\Filament\Admin\Resources\SiakadSyncLogResource;
use Filament\Resources\Pages\ListRecords;

class ListSiakadSyncLogs extends ListRecords
{
    protected static string $resource = SiakadSyncLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}