<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\InternshipResource\Pages;
use App\Models\CompanyProfile;
use App\Models\Internship;
use App\Models\Period;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InternshipResource extends Resource
{
    use HasSimmagResourceAccess;

    protected static ?string $model = Internship::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Magang';

    protected static ?string $navigationLabel = 'Data Magang';

    protected static ?string $modelLabel = 'Data Magang';

    protected static ?string $pluralModelLabel = 'Data Magang';

    protected static ?int $navigationSort = 1;

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
    ];

    protected static array $viewRoles = [
        'admin',
        'mahasiswa',
        'dosen_pembimbing',
        'pembimbing_lapangan',
    ];

    protected static array $editRoles = [
        'admin',
    ];

    protected static array $deleteRoles = [
        'admin',
    ];

    protected static bool $useRoleRecordScope = true;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Akademik')
                    ->description(
                        'Pilih mahasiswa, periode, dosen pembimbing, dan instansi mitra.'
                    )
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Mahasiswa')
                            ->options(
                                fn (): array => static::studentOptions()
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('period_id')
                            ->label('Periode Magang')
                            ->options(
                                fn (): array => static::periodOptions()
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make(
                            'supervisor_lecturer_id'
                        )
                            ->label('Dosen Pembimbing')
                            ->options(
                                fn (): array => static::lecturerOptions()
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('company_id')
                            ->label('Instansi Mitra')
                            ->options(
                                fn (): array => CompanyProfile::query()
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(
                    'Pembimbing Lapangan'
                )
                    ->description(
                        'Pilih akun Pembimbing Lapangan yang bertanggung jawab terhadap mahasiswa.'
                    )
                    ->schema([
                        Forms\Components\Select::make(
                            'field_supervisor_id'
                        )
                            ->label('Akun Pembimbing Lapangan')
                            ->options(
                                fn (): array =>
                                    static::fieldSupervisorOptions()
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->helperText(
                                'Akun harus memiliki role Pembimbing Lapangan.'
                            )
                            ->afterStateUpdated(
                                function (
                                    mixed $state,
                                    Forms\Set $set
                                ): void {
                                    $fieldSupervisor = filled($state)
                                        ? User::query()->find($state)
                                        : null;

                                    $set(
                                        'field_supervisor_name',
                                        $fieldSupervisor?->name
                                    );

                                    $set(
                                        'field_supervisor_position',
                                        $fieldSupervisor?->position
                                            ?? $fieldSupervisor?->job_title
                                            ?? 'Pembimbing Lapangan'
                                    );

                                    $set(
                                        'field_supervisor_phone',
                                        $fieldSupervisor?->phone
                                            ?? $fieldSupervisor?->phone_number
                                            ?? $fieldSupervisor?->whatsapp
                                    );

                                    $set(
                                        'field_supervisor_email',
                                        $fieldSupervisor?->email
                                    );
                                }
                            )
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make(
                            'field_supervisor_name'
                        )
                            ->label('Nama Pembimbing Lapangan')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(
                                'Terisi otomatis dari akun'
                            ),

                        Forms\Components\TextInput::make(
                            'field_supervisor_position'
                        )
                            ->label('Jabatan')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(
                                'Terisi otomatis dari akun'
                            ),

                        Forms\Components\TextInput::make(
                            'field_supervisor_phone'
                        )
                            ->label('Nomor HP')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(
                                'Belum tersedia pada profil akun'
                            ),

                        Forms\Components\TextInput::make(
                            'field_supervisor_email'
                        )
                            ->label('Email')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(
                                'Terisi otomatis dari akun'
                            ),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(
                    'Pelaksanaan Magang'
                )
                    ->schema([
                        Forms\Components\DatePicker::make(
                            'started_at'
                        )
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->required(),

                        Forms\Components\DatePicker::make(
                            'ended_at'
                        )
                            ->label('Tanggal Selesai')
                            ->native(false)
                            ->afterOrEqual('started_at')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(
                                static::statusOptions()
                            )
                            ->default('menunggu_ka')
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make(
                    'student.avatar_url'
                )
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(
                        url('/images/default-avatar.png')
                    ),

                Tables\Columns\TextColumn::make(
                    'student.name'
                )
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(
                        fn (Internship $record): ?string =>
                            $record->student?->email
                    ),

                Tables\Columns\TextColumn::make(
                    'student.nim'
                )
                    ->label('NIM')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make(
                    'company.name'
                )
                    ->label('Instansi')
                    ->searchable()
                    ->sortable()
                    ->icon(
                        'heroicon-m-building-office'
                    )
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make(
                    'supervisorLecturer.name'
                )
                    ->label('Dosen Pembimbing')
                    ->searchable()
                    ->sortable()
                    ->icon(
                        'heroicon-m-academic-cap'
                    )
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make(
                    'fieldSupervisor.name'
                )
                    ->label('Pembimbing Lapangan')
                    ->searchable()
                    ->sortable()
                    ->icon(
                        'heroicon-m-user-circle'
                    )
                    ->description(
                        fn (Internship $record): ?string =>
                            $record->fieldSupervisor?->email
                                ?? $record->field_supervisor_email
                    )
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            static::statusOptions()[$state]
                                ?? str((string) $state)
                                    ->replace([
                                        '_',
                                        '-',
                                    ], ' ')
                                    ->title()
                                    ->toString()
                    )
                    ->color(
                        fn (?string $state): string =>
                            match ($state) {
                                'aktif' => 'success',
                                'selesai' => 'info',
                                'menunggu_ka' => 'warning',
                                'batal' => 'danger',
                                default => 'gray',
                            }
                    ),

                Tables\Columns\TextColumn::make(
                    'started_at'
                )
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make(
                    'ended_at'
                )
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make(
                    'created_at'
                )
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make(
                    'status'
                )
                    ->label('Status Magang')
                    ->options(
                        static::statusOptions()
                    ),

                Tables\Filters\SelectFilter::make(
                    'period_id'
                )
                    ->label('Periode')
                    ->options(
                        fn (): array =>
                            static::periodOptions()
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make(
                    'company_id'
                )
                    ->label('Instansi')
                    ->relationship(
                        'company',
                        'name'
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make(
                    'supervisor_lecturer_id'
                )
                    ->label('Dosen Pembimbing')
                    ->options(
                        fn (): array =>
                            static::lecturerOptions()
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make(
                    'field_supervisor_id'
                )
                    ->label('Pembimbing Lapangan')
                    ->options(
                        fn (): array =>
                            static::fieldSupervisorOptions()
                    )
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),

                Tables\Actions\EditAction::make()
                    ->label('Ubah')
                    ->visible(
                        fn (Internship $record): bool =>
                            static::canEdit($record)
                    ),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->visible(
                        fn (Internship $record): bool =>
                            static::canDelete($record)
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(
                            fn (): bool =>
                                auth()->user()?->hasRole(
                                    'admin'
                                ) ?? false
                        ),
                ]),
            ])
            ->emptyStateHeading(
                'Belum ada data magang'
            )
            ->emptyStateDescription(
                'Tambahkan data mahasiswa yang sedang menjalani magang.'
            )
            ->emptyStateIcon(
                'heroicon-o-briefcase'
            );
    }

    public static function getPages(): array
    {
        return [
            'index' =>
                Pages\ListInternships::route('/'),

            'create' =>
                Pages\CreateInternship::route(
                    '/create'
                ),

            'view' =>
                Pages\ViewInternship::route(
                    '/{record}'
                ),

            'edit' =>
                Pages\EditInternship::route(
                    '/{record}/edit'
                ),
        ];
    }

    protected static function statusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'menunggu_ka' =>
                'Menunggu Kerangka Acuan',
            'aktif' => 'Aktif',
            'selesai' => 'Selesai',
            'batal' => 'Batal',
        ];
    }

    protected static function studentOptions(): array
    {
        return User::query()
            ->where('is_active', true)
            ->where(
                function ($query): void {
                    $query
                        ->where(
                            'role',
                            'mahasiswa'
                        )
                        ->orWhereHas(
                            'roles',
                            fn ($roleQuery) =>
                                $roleQuery->where(
                                    'name',
                                    'mahasiswa'
                                )
                        );
                }
            )
            ->orderBy('name')
            ->get()
            ->mapWithKeys(
                fn (User $user): array => [
                    $user->id =>
                        $user->name
                        . ' - '
                        . (
                            $user->nim
                            ?? $user->identifier
                            ?? '-'
                        ),
                ]
            )
            ->toArray();
    }

    protected static function lecturerOptions(): array
    {
        return User::query()
            ->where('is_active', true)
            ->where(
                function ($query): void {
                    $query
                        ->where(
                            'role',
                            'dosen_pembimbing'
                        )
                        ->orWhereHas(
                            'roles',
                            fn ($roleQuery) =>
                                $roleQuery->where(
                                    'name',
                                    'dosen_pembimbing'
                                )
                        );
                }
            )
            ->orderBy('name')
            ->get()
            ->mapWithKeys(
                fn (User $user): array => [
                    $user->id =>
                        $user->name
                        . ' - '
                        . (
                            $user->nidn
                            ?? $user->nip
                            ?? $user->identifier
                            ?? '-'
                        ),
                ]
            )
            ->toArray();
    }

    protected static function fieldSupervisorOptions(): array
    {
        return User::query()
            ->where('is_active', true)
            ->where(
                function ($query): void {
                    $query
                        ->where(
                            'role',
                            'pembimbing_lapangan'
                        )
                        ->orWhereHas(
                            'roles',
                            fn ($roleQuery) =>
                                $roleQuery->where(
                                    'name',
                                    'pembimbing_lapangan'
                                )
                        );
                }
            )
            ->orderBy('name')
            ->get()
            ->mapWithKeys(
                fn (User $user): array => [
                    $user->id =>
                        $user->name
                        . ' - '
                        . (
                            $user->email
                            ?? $user->identifier
                            ?? '-'
                        ),
                ]
            )
            ->toArray();
    }

    protected static function periodOptions(): array
    {
        return Period::query()
            ->orderByDesc('start_date')
            ->get()
            ->mapWithKeys(
                fn (Period $period): array => [
                    $period->id =>
                        $period->academic_year
                        . ' - '
                        . $period->semester,
                ]
            )
            ->toArray();
    }
}