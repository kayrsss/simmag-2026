<?php

namespace App\Filament\Admin\Resources\AuditTrailResource\Pages;

use App\Filament\Admin\Resources\AuditTrailResource;
use Filament\Resources\Pages\ListRecords;

class ListAuditTrails extends ListRecords
{
    protected static string $resource = AuditTrailResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}