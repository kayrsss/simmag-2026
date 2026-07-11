<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\LecturerAssessmentResource\Pages;
use App\Models\Internship;
use App\Models\LecturerAssessment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LecturerAssessmentResource extends Resource
{
    use HasSimmagResourceAccess;

    protected static array $navigationRoles = ['admin', 'dosen_pembimbing'];

    protected static array $viewAnyRoles = ['admin', 'dosen_pembimbing'];

    protected static array $createRoles = ['admin', 'dosen_pembimbing'];

    protected static array $viewRoles = ['admin', 'dosen_pembimbing'];

    protected static array $editRoles = ['admin', 'dosen_pembimbing'];

    protected static array $deleteRoles = ['admin'];

    protected static bool $useRoleRecordScope = true;

    protected static ?string $model = LecturerAssessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Penilaian Dosen';

    protected static ?string $modelLabel = 'Penilaian Dosen';

    protected static ?string $pluralModelLabel = 'Penilaian Dosen';

    protected static ?string $navigationGroup = 'Penilaian';

    protected static ?int $navigationSort = 2;










    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Penilaian Dosen')
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
                            ->label('Penilai / Dosen Pembimbing')
                            ->options(User::query()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('consistency_score')
                            ->label('Nilai Konsistensi')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('logbook_completeness_score')
                            ->label('Nilai Kelengkapan Logbook')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('neatness_score')
                            ->label('Nilai Kerapian')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('content_completeness_score')
                            ->label('Nilai Kelengkapan Konten')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('writing_flow_score')
                            ->label('Nilai Alur Penulisan')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),

                        Forms\Components\TextInput::make('grammar_score')
                            ->label('Nilai Tata Bahasa')
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
            'index' => Pages\ListLecturerAssessments::route('/'),
            'create' => Pages\CreateLecturerAssessment::route('/create'),
            'edit' => Pages\EditLecturerAssessment::route('/{record}/edit'),
        ];
    }
}