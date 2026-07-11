<?php

namespace App\Filament\Admin\Resources\FieldAssessmentResource\Pages;

use App\Filament\Admin\Resources\FieldAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFieldAssessment extends EditRecord
{
    protected static string $resource = FieldAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
