<?php

namespace App\Filament\Admin\Resources\FrameworkOfReferenceResource\Pages;

use App\Filament\Admin\Resources\FrameworkOfReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFrameworkOfReference extends EditRecord
{
    protected static string $resource = FrameworkOfReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
