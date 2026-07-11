<?php

namespace App\Filament\Admin\Resources\ProgramStudyResource\Pages;

use App\Filament\Admin\Resources\ProgramStudyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProgramStudy extends EditRecord
{
    protected static string $resource = ProgramStudyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}