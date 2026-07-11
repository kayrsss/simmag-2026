<?php

namespace App\Filament\Admin\Resources\ConsultationResource\Pages;

use App\Filament\Admin\Resources\ConsultationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsultation extends EditRecord
{
    protected static string $resource = ConsultationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
