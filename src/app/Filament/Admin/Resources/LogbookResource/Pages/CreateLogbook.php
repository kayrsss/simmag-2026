<?php

namespace App\Filament\Admin\Resources\LogbookResource\Pages;

use App\Filament\Admin\Resources\LogbookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLogbook extends CreateRecord
{
    protected static string $resource = LogbookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()?->hasRole('mahasiswa')) {
            $data['status'] = 'submitted';
            $data['submitted_at'] = now();
            $data['validated_at'] = null;
            $data['validation_notes'] = null;
        }

        if (auth()->user()?->hasRole('admin')) {
            $data['status'] = $data['status'] ?? 'submitted';

            if ($data['status'] === 'submitted') {
                $data['submitted_at'] = $data['submitted_at'] ?? now();
            }

            if ($data['status'] === 'validated') {
                $data['validated_at'] = $data['validated_at'] ?? now();
            }
        }

        return $data;
    }
}