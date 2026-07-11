<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\AuditTrailResource\Pages;
use App\Models\AuditTrail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AuditTrailResource extends Resource
{
    use HasSimmagResourceAccess;

    protected static array $navigationRoles = ['admin'];

    protected static array $viewAnyRoles = ['admin'];

    protected static array $createRoles = [];

    protected static array $viewRoles = ['admin'];

    protected static array $editRoles = [];

    protected static array $deleteRoles = [];

    protected static bool $useRoleRecordScope = false;

    protected static ?string $model = AuditTrail::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Audit Trail';

    protected static ?string $modelLabel = 'Audit Trail';

    protected static ?string $pluralModelLabel = 'Audit Trail';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 2;













    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Audit Trail')
                    ->schema([
                        Forms\Components\TextInput::make('user.name')
                            ->label('Pengguna')
                            ->disabled(),

                        Forms\Components\TextInput::make('action')
                            ->label('Aksi')
                            ->disabled(),

                        Forms\Components\TextInput::make('entity_type')
                            ->label('Jenis Entitas')
                            ->disabled(),

                        Forms\Components\TextInput::make('entity_id')
                            ->label('ID Entitas')
                            ->disabled(),

                        Forms\Components\TextInput::make('previous_status')
                            ->label('Status Sebelumnya')
                            ->disabled(),

                        Forms\Components\TextInput::make('new_status')
                            ->label('Status Baru')
                            ->disabled(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(4)
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('ip_address')
                            ->label('Alamat IP')
                            ->disabled(),

                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Entitas')
                    ->formatStateUsing(function (?string $state): string {
                        if (! $state) {
                            return '-';
                        }

                        return class_basename($state);
                    })
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('entity_id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('previous_status')
                    ->label('Status Sebelumnya')
                    ->badge()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('new_status')
                    ->label('Status Baru')
                    ->badge()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(45)
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable()
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->label('Filter Aksi')
                    ->options([
                        'UPDATE_STATUS_KERANGKA_ACUAN' => 'Status Kerangka Acuan',
                        'UPDATE_STATUS_LOGBOOK' => 'Status Logbook',
                        'UPDATE_STATUS_LAPORAN_AKHIR' => 'Status Laporan Akhir',
                        'UPDATE_STATUS_BIMBINGAN' => 'Status Bimbingan',
                        'MAGANG_SELESAI_OTOMATIS' => 'Magang Selesai Otomatis',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditTrails::route('/'),
            'view' => Pages\ViewAuditTrail::route('/{record}'),
        ];
    }
}   