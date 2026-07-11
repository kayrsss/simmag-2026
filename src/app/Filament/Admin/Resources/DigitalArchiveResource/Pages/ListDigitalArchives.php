<?php

namespace App\Filament\Admin\Resources\DigitalArchiveResource\Pages;

use App\Filament\Admin\Resources\DigitalArchiveResource;
use Filament\Resources\Pages\ListRecords;

class ListDigitalArchives extends ListRecords
{
    protected static string $resource = DigitalArchiveResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}