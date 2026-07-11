<?php

namespace App\Filament\Admin\Resources\FrameworkOfReferenceResource\Pages;

use App\Filament\Admin\Resources\FrameworkOfReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFrameworkOfReferences extends ListRecords
{
    protected static string $resource = FrameworkOfReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kerangka Acuan'),
        ];
    }
}