<?php

namespace App\Filament\Admin\Resources\LogbookResource\Pages;

use App\Filament\Admin\Resources\LogbookResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLogbook extends ViewRecord
{
    protected static string $resource = LogbookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (): bool => LogbookResource::canEdit($this->record)),
        ];
    }
}