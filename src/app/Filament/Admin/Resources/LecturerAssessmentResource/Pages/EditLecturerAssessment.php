<?php

namespace App\Filament\Admin\Resources\LecturerAssessmentResource\Pages;

use App\Filament\Admin\Resources\LecturerAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLecturerAssessment extends EditRecord
{
    protected static string $resource = LecturerAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
