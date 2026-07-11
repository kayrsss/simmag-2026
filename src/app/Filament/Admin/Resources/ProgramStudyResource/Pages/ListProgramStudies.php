<?php

namespace App\Filament\Admin\Resources\ProgramStudyResource\Pages;

use App\Filament\Admin\Resources\ProgramStudyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramStudies extends ListRecords
{
    protected static string $resource = ProgramStudyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Program Studi'),
        ];
    }
}