<?php

namespace App\Filament\Admin\Resources\FinalReportResource\Pages;

use App\Filament\Admin\Resources\FinalReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinalReport extends EditRecord
{
    protected static string $resource = FinalReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
