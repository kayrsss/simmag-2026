<?php

namespace App\Filament\Admin\Resources\DigitalArchiveResource\Pages;

use App\Filament\Admin\Resources\DigitalArchiveResource;
use Filament\Resources\Pages\ViewRecord;

class ViewDigitalArchive extends ViewRecord
{
    protected static string $resource = DigitalArchiveResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}