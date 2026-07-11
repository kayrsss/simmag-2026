<?php

namespace App\Filament\Admin\Resources\SiakadSyncLogResource\Pages;

use App\Filament\Admin\Resources\SiakadSyncLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiakadSyncLog extends EditRecord
{
    protected static string $resource = SiakadSyncLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
