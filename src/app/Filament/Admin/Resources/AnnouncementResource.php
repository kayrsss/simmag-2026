<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    use HasSimmagResourceAccess;

    protected static array $navigationRoles = ['admin', 'mahasiswa', 'dosen_pembimbing', 'pembimbing_lapangan'];

    protected static array $viewAnyRoles = ['admin', 'mahasiswa', 'dosen_pembimbing', 'pembimbing_lapangan'];

    protected static array $createRoles = ['admin'];

    protected static array $viewRoles = ['admin', 'mahasiswa', 'dosen_pembimbing', 'pembimbing_lapangan'];

    protected static array $editRoles = ['admin'];

    protected static array $deleteRoles = ['admin'];

    protected static bool $useRoleRecordScope = false;

    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Pengumuman';

    protected static ?string $modelLabel = 'Pengumuman';

    protected static ?string $pluralModelLabel = 'Pengumuman';

    protected static ?string $navigationGroup = 'Informasi';

    protected static ?int $navigationSort = 1;










    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pengumuman')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Pengumuman')
                            ->maxLength(255)
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('priority')
                            ->label('Prioritas')
                            ->options([
                                'Biasa' => 'Biasa',
                                'Penting' => 'Penting',
                            ])
                            ->default('Biasa')
                            ->required(),

                        Forms\Components\Hidden::make('created_by')
                            ->default(fn () => auth()->id()),

                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Pengumuman')
                            ->required()
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Penting' => 'danger',
                        'Biasa' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('priority')
                    ->label('Filter Prioritas')
                    ->options([
                        'Biasa' => 'Biasa',
                        'Penting' => 'Penting',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),

                Tables\Actions\EditAction::make()
                    ->label('Edit'),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Terpilih'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'view' => Pages\ViewAnnouncement::route('/{record}'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}