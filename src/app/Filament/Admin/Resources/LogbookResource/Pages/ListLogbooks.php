<?php

namespace App\Filament\Admin\Resources\LogbookResource\Pages;

use App\Filament\Admin\Resources\LogbookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogbooks extends ListRecords
{
    protected static string $resource = LogbookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn (): bool => LogbookResource::canCreate()),
        ];
    }
}