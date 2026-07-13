<?php

namespace App\Filament\Admin\Resources\SimmagResource\Pages;

use App\Filament\Admin\Resources\SimmagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSimmag extends EditRecord
{
    protected static string $resource = SimmagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
