<?php

namespace App\Filament\Admin\Resources\AuditTrailResource\Pages;

use App\Filament\Admin\Resources\AuditTrailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAuditTrail extends CreateRecord
{
    protected static string $resource = AuditTrailResource::class;
}
