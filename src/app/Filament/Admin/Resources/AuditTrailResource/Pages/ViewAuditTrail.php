<?php

namespace App\Filament\Admin\Resources\AuditTrailResource\Pages;

use App\Filament\Admin\Resources\AuditTrailResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAuditTrail extends ViewRecord
{
    protected static string $resource = AuditTrailResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}