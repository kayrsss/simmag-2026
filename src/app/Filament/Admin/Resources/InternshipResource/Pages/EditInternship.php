<?php

namespace App\Filament\Admin\Resources\InternshipResource\Pages;

use App\Filament\Admin\Resources\InternshipResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInternship extends EditRecord
{
    protected static string $resource = InternshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Lihat'),

            Actions\DeleteAction::make()
                ->label('Hapus')
                ->visible(
                    fn (): bool => InternshipResource::canDelete($this->record)
                ),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data magang berhasil diperbarui';
    }
}