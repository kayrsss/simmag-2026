<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\FieldAssessmentResource\Pages;
use App\Models\FieldAssessment;
use App\Models\Internship;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FieldAssessmentResource extends Resource
{
    use HasSimmagResourceAccess;

    protected static array $navigationRoles = ['admin', 'pembimbing_lapangan'];

    protected static array $viewAnyRoles = ['admin', 'pembimbing_lapangan'];

    protected static array $createRoles = ['admin', 'pembimbing_lapangan'];

    protected static array $viewRoles = ['admin', 'pembimbing_lapangan'];

    protected static array $editRoles = ['admin', 'pembimbing_lapangan'];

    protected static array $deleteRoles = ['admin'];

    protected static bool $useRoleRecordScope = true;

    protected static ?string $model = FieldAssessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Penilaian Lapangan';

    protected static ?string $modelLabel = 'Penilaian Lapangan';

    protected static ?string $pluralModelLabel = 'Penilaian Lapangan';

    protected static ?string $navigationGroup = 'Penilaian';

    protected static ?int $navigationSort = 1;










    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Penilaian Lapangan')
                    ->schema([
                        Forms\Components\Select::make('internship_id')
                            ->label('Data Magang')
                            ->options(function () {
                                return Internship::query()
                                    ->with(['student', 'company'])
                                    ->latest()
                                    ->get()
                                    ->mapWithKeys(function ($internship) {
                                        $studentName = $internship->student?->name ?? 'Mahasiswa';
                                        $companyName = $internship->company?->name ?? 'Instansi';

                                        return [
                                            $internship->id => "{$studentName} - {$companyName}",
                                        ];
                                    });
                            })
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('evaluator_id')
                            ->label('Penilai / Pembimbing Lapangan')
                            ->options(User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('discipline_score')
                            ->label('Nilai Disiplin')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('initiative_score')
                            ->label('Nilai Inisiatif')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('teamwork_score')
                            ->label('Nilai Kerja Sama')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('communication_score')
                            ->label('Nilai Komunikasi')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('adaptability_score')
                            ->label('Nilai Adaptasi')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('diligence_score')
                            ->label('Nilai Ketekunan')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('appearance_score')
                            ->label('Nilai Penampilan')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('honesty_score')
                            ->label('Nilai Kejujuran')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('critical_thinking_score')
                            ->label('Nilai Berpikir Kritis')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('responsibility_score')
                            ->label('Nilai Tanggung Jawab')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('overall_score')
                            ->label('Nilai Rata-rata')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Nilai ini dihitung otomatis oleh sistem.'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Penilaian')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('assessed_at')
                            ->label('Waktu Penilaian')
                            ->default(now()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('internship.student.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('internship.student.nim')
                    ->label('NIM')
                    ->searchable(),

                Tables\Columns\TextColumn::make('internship.company.name')
                    ->label('Instansi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('evaluator.name')
                    ->label('Penilai')
                    ->searchable(),

                Tables\Columns\TextColumn::make('overall_score')
                    ->label('Rata-rata')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('assessed_at')
                    ->label('Dinilai Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
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
            'index' => Pages\ListFieldAssessments::route('/'),
            'create' => Pages\CreateFieldAssessment::route('/create'),
            'edit' => Pages\EditFieldAssessment::route('/{record}/edit'),
        ];
    }
}