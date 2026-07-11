<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\CompanyProfileResource\Pages;
use App\Models\CompanyProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyProfileResource extends Resource
{
    use HasSimmagResourceAccess;

    protected static array $navigationRoles = ['admin'];

    protected static array $viewAnyRoles = ['admin'];

    protected static array $createRoles = ['admin'];

    protected static array $viewRoles = ['admin'];

    protected static array $editRoles = ['admin'];

    protected static array $deleteRoles = ['admin'];

    protected static bool $useRoleRecordScope = false;

    protected static ?string $model = CompanyProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'Instansi Mitra';

    protected static ?string $modelLabel = 'Instansi Mitra';

    protected static ?string $pluralModelLabel = 'Instansi Mitra';

    protected static ?int $navigationSort = 3;










    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Instansi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Instansi')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Instansi')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon Instansi')
                            ->tel()
                            ->maxLength(50),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Penanggung Jawab Instansi')
                    ->schema([
                        Forms\Components\TextInput::make('pic_name')
                            ->label('Nama PIC')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('pic_position')
                            ->label('Jabatan PIC')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('pic_phone')
                            ->label('Telepon PIC')
                            ->tel()
                            ->maxLength(50),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Instansi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pic_name')
                    ->label('PIC')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pic_position')
                    ->label('Jabatan PIC')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyProfiles::route('/'),
            'create' => Pages\CreateCompanyProfile::route('/create'),
            'edit' => Pages\EditCompanyProfile::route('/{record}/edit'),
        ];
    }
}