<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SiakadSyncLogResource\Pages;
use App\Models\SiakadSyncLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiakadSyncLogResource extends Resource
{
    protected static ?string $model = SiakadSyncLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Sinkronisasi SIAKAD';

    protected static ?string $modelLabel = 'Log Sinkronisasi SIAKAD';

    protected static ?string $pluralModelLabel = 'Log Sinkronisasi SIAKAD';

   protected static ?string $navigationGroup = 'Sistem';
   protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 1;
    public static function canAccess(): bool
{
    return auth()->user()?->hasRole('admin') ?? false;
}

public static function canViewAny(): bool
{
    return auth()->user()?->hasRole('admin') ?? false;
}

public static function shouldRegisterNavigation(): bool
{
    return auth()->user()?->hasRole('admin') ?? false;
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Sinkronisasi')
                    ->schema([
                        Forms\Components\TextInput::make('sync_type')
                            ->label('Jenis Sinkronisasi')
                            ->disabled(),

                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled(),

                        Forms\Components\TextInput::make('total_inserted')
                            ->label('Total')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('total_inserted')
                            ->label('Berhasil')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('total_failed')
                            ->label('Gagal')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('total_updated')
                            ->label('Diperbarui')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\Textarea::make('message')
                            ->label('Pesan')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('Mulai')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('finished_at')
                            ->label('Selesai')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sync_type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? '-'))
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? '-'))
                    ->color(fn (?string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'partial' => 'warning',
                        'processing' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_inserted')
                    ->label('Total')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_inserted')
                    ->label('Berhasil')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_failed')
                    ->label('Gagal')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_updated')
                    ->label('Update')
                    ->sortable(),

                Tables\Columns\TextColumn::make('started_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('finished_at')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sync_type')
                    ->label('Jenis')
                    ->options([
                        'dummy' => 'Dummy',
                        'mock' => 'Mock',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'success' => 'Berhasil',
                        'failed' => 'Gagal',
                        'partial' => 'Sebagian',
                        'processing' => 'Diproses',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('sync_siakad')
                    ->label('Sinkronisasi SIAKAD')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Sinkronisasi Data SIAKAD')
                    ->modalDescription('Sinkronisasi akan memperbarui data Mahasiswa, Dosen, Program Studi, Periode, dan Perusahaan dari data dummy.')
                    ->modalSubmitActionLabel('Sinkronisasi')
                    ->action(function (): void {
                        app(\App\Services\Siakad\SiakadMockSyncService::class)->sync();

                        Notification::make()
                            ->title('Sinkronisasi berhasil')
                            ->body('Data dummy SIAKAD berhasil diperbarui.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiakadSyncLogs::route('/'),
            'view' => Pages\ViewSiakadSyncLog::route('/{record}'),
        ];
    }
}