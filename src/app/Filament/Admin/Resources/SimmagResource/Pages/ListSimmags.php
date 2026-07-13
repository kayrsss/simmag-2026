<?php

namespace App\Filament\Admin\Resources\SimmagResource\Pages;

use App\Filament\Admin\Resources\SimmagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSimmags extends ListRecords
{
    protected static string $resource = SimmagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
