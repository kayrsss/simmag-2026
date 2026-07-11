<?php

namespace App\Filament\Admin\Resources\InternshipResource\Pages;

use App\Filament\Admin\Resources\InternshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInternship extends ViewRecord
{
    protected static string $resource = InternshipResource::class;

    public function getTitle(): string
    {
        return 'Detail Magang - ' . ($this->record->student?->name ?? 'Mahasiswa');
    }

    public function getSubheading(): ?string
    {
        $nim = $this->record->student?->nim
            ?? $this->record->student_nim
            ?? '-';

        $company = $this->record->company?->name
            ?? $this->record->company_name
            ?? '-';

        return "{$nim} • {$company}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Ubah Data')
                ->icon('heroicon-o-pencil-square')
                ->visible(fn (): bool => InternshipResource::canEdit($this->record)),

            Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->visible(fn (): bool => InternshipResource::canDelete($this->record)),
        ];
    }
}