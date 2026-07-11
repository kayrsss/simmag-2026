<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\LogbookResource\Pages;
use App\Models\FrameworkOfReference;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LogbookResource extends Resource
{
    use HasSimmagResourceAccess;

    protected static ?string $model = Logbook::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Magang';

    protected static ?string $navigationLabel = 'Logbook Harian';

    protected static ?string $modelLabel = 'Logbook Harian';

    protected static ?string $pluralModelLabel = 'Logbook Harian';

    protected static ?int $navigationSort = 3;

    protected static array $navigationRoles = [
        'admin',
        'mahasiswa',
        'dosen_pembimbing',
        'pembimbing_lapangan',
    ];

    protected static array $viewAnyRoles = [
        'admin',
        'mahasiswa',
        'dosen_pembimbing',
        'pembimbing_lapangan',
    ];

    protected static array $createRoles = [
        'admin',
        'mahasiswa',
    ];

    protected static array $viewRoles = [
        'admin',
        'mahasiswa',
        'dosen_pembimbing',
        'pembimbing_lapangan',
    ];

    protected static array $editRoles = [
        'admin',
        'mahasiswa',
        'pembimbing_lapangan',
    ];

    protected static array $deleteRoles = [
        'admin',
        'mahasiswa',
    ];

    protected static bool $useRoleRecordScope = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Logbook')
                    ->schema([
                        Forms\Components\Select::make('internship_id')
                            ->label('Data Magang')
                            ->required()
                            ->options(fn (): array => static::internshipOptions())
                            ->searchable()
                            ->preload()
                            ->disabled(fn (string $operation): bool => $operation === 'edit' && ! static::userHasAny(['admin']))
                            ->dehydrated(),

                        Forms\Components\Select::make('framework_of_reference_id')
                            ->label('Kerangka Acuan')
                            ->options(fn (): array => static::frameworkOfReferenceOptions())
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->disabled(fn (): bool => static::userHasAny(['dosen_pembimbing', 'pembimbing_lapangan']))
                            ->dehydrated(),

                        Forms\Components\DatePicker::make('activity_date')
                            ->label('Tanggal Aktivitas')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->disabled(fn (): bool => static::userHasAny(['dosen_pembimbing', 'pembimbing_lapangan']))
                            ->dehydrated(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Aktivitas')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->disabled(fn (): bool => static::userHasAny(['dosen_pembimbing', 'pembimbing_lapangan']))
                            ->dehydrated(),

                        Forms\Components\FileUpload::make('attachment_file')
                            ->label('Lampiran')
                            ->directory('logbooks')
                            ->disk('public')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                            ])
                            ->maxSize(20480)
                            ->downloadable()
                            ->openable()
                            ->disabled(fn (): bool => static::userHasAny(['dosen_pembimbing', 'pembimbing_lapangan']))
                            ->dehydrated(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(static::statusOptions())
                            ->default('submitted')
                            ->required()
                            ->visible(fn (): bool => static::userHasAny(['admin', 'pembimbing_lapangan']))
                            ->disabled(fn (): bool => static::userHasAny(['mahasiswa', 'dosen_pembimbing']))
                            ->dehydrated(),

                        Forms\Components\Textarea::make('validation_notes')
                            ->label('Catatan Validasi')
                            ->rows(4)
                            ->columnSpanFull()
                            ->visible(fn (): bool => static::userHasAny(['admin', 'dosen_pembimbing', 'pembimbing_lapangan']))
                            ->disabled(fn (): bool => static::userHasAny(['dosen_pembimbing']))
                            ->dehydrated(),

                        Forms\Components\DateTimePicker::make('submitted_at')
                            ->label('Waktu Submit')
                            ->native(false)
                            ->seconds(false)
                            ->visible(fn (): bool => static::userHasAny(['admin']))
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\DateTimePicker::make('validated_at')
                            ->label('Waktu Validasi')
                            ->native(false)
                            ->seconds(false)
                            ->visible(fn (): bool => static::userHasAny(['admin', 'pembimbing_lapangan']))
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('internship_id')
                    ->label('Data Magang')
                    ->formatStateUsing(fn ($state): string => static::formatInternshipLabel($state))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('activity_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Aktivitas')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => static::statusOptions()[$state] ?? $state)
                    ->color(fn ($state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'validated' => 'success',
                        'revision' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submit')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('validated_at')
                    ->label('Validasi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(static::statusOptions()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn (Logbook $record): bool => static::canView($record)),

                Tables\Actions\EditAction::make()
                    ->visible(fn (Logbook $record): bool => static::canEdit($record)),

                Tables\Actions\Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Logbook $record): bool => static::userHasAny(['admin', 'mahasiswa'])
                        && in_array($record->status, ['draft', 'revision'], true)
                    )
                    ->action(function (Logbook $record): void {
                        $record->update([
                            'status' => 'submitted',
                            'submitted_at' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('validate_logbook')
                    ->label('Validasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Logbook $record): bool => static::userHasAny(['admin', 'pembimbing_lapangan'])
                        && $record->status === 'submitted'
                    )
                    ->form([
                        Forms\Components\Textarea::make('validation_notes')
                            ->label('Catatan Validasi')
                            ->rows(3)
                            ->nullable(),
                    ])
                    ->action(function (Logbook $record, array $data): void {
                        $record->update([
                            'status' => 'validated',
                            'validation_notes' => $data['validation_notes'] ?? null,
                            'validated_at' => now(),
                        ]);
                    }),

                Tables\Actions\Action::make('revision_logbook')
                    ->label('Perlu Revisi')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Logbook $record): bool => static::userHasAny(['admin', 'pembimbing_lapangan'])
                        && $record->status === 'submitted'
                    )
                    ->form([
                        Forms\Components\Textarea::make('validation_notes')
                            ->label('Catatan Revisi')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Logbook $record, array $data): void {
                        $record->update([
                            'status' => 'revision',
                            'validation_notes' => $data['validation_notes'],
                            'validated_at' => now(),
                        ]);
                    }),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Logbook $record): bool => static::canDelete($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => static::userHasAny(['admin'])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogbooks::route('/'),
            'create' => Pages\CreateLogbook::route('/create'),
            'view' => Pages\ViewLogbook::route('/{record}'),
            'edit' => Pages\EditLogbook::route('/{record}/edit'),
        ];
    }

    protected static function statusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Menunggu Validasi',
            'validated' => 'Tervalidasi',
            'revision' => 'Perlu Revisi',
        ];
    }

    protected static function userHasAny(array $roles): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        foreach ($roles as $role) {
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                return true;
            }

            if (($user->role ?? null) === $role) {
                return true;
            }
        }

        return false;
    }

    protected static function internshipOptions(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $query = Internship::query();

        if ($user->hasRole('mahasiswa')) {
            $query->where('student_id', $user->id);
        }

        if ($user->hasRole('dosen_pembimbing')) {
            $query->where('supervisor_lecturer_id', $user->id);
        }

        if ($user->hasRole('pembimbing_lapangan')) {
            $query->where('field_supervisor_email', $user->email);
        }

        return $query
            ->get()
            ->mapWithKeys(fn (Internship $internship): array => [
                $internship->id => static::formatInternshipLabel($internship->id),
            ])
            ->toArray();
    }

    protected static function frameworkOfReferenceOptions(): array
    {
        $internshipIds = array_keys(static::internshipOptions());

        if (empty($internshipIds)) {
            return [];
        }

        return FrameworkOfReference::query()
            ->whereIn('internship_id', $internshipIds)
            ->get()
            ->mapWithKeys(fn (FrameworkOfReference $record): array => [
                $record->id => 'KA #' . $record->id . ' - ' . ($record->title ?? 'Tanpa Judul'),
            ])
            ->toArray();
    }

    protected static function formatInternshipLabel($internshipId): string
    {
        $internship = Internship::query()->find($internshipId);

        if (! $internship) {
            return 'Data Magang #' . $internshipId;
        }

        $student = User::query()->find($internship->student_id);

        $studentName = $student?->name ?? 'Mahasiswa ID ' . $internship->student_id;

        return 'Magang #' . $internship->id . ' - ' . $studentName;
    }
}