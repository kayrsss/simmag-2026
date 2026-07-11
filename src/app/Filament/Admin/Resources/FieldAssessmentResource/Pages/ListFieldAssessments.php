<?php

namespace App\Filament\Admin\Resources\FieldAssessmentResource\Pages;

use App\Filament\Admin\Resources\FieldAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFieldAssessments extends ListRecords
{
    protected static string $resource = FieldAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Penilaian Lapangan'),
        ];
    }
}
