<?php

namespace App\Filament\Admin\Resources\LogbookResource\Pages;

use App\Filament\Admin\Resources\LogbookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogbook extends EditRecord
{
    protected static string $resource = LogbookResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? null) === 'submitted' && empty($data['submitted_at'])) {
            $data['submitted_at'] = now();
        }

        if (($data['status'] ?? null) === 'validated' && empty($data['validated_at'])) {
            $data['validated_at'] = now();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn (): bool => LogbookResource::canDelete($this->record)),
        ];
    }
}