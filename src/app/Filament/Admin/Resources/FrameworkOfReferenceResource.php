<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\FrameworkOfReferenceResource\Pages;
use App\Models\AuditTrail;
use App\Models\FrameworkOfReference;
use App\Models\Internship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FrameworkOfReferenceResource extends Resource
{
    use HasSimmagResourceAccess;

    protected static array $navigationRoles = ['admin', 'mahasiswa', 'dosen_pembimbing', 'pembimbing_lapangan'];

    protected static array $viewAnyRoles = ['admin', 'mahasiswa', 'dosen_pembimbing', 'pembimbing_lapangan'];

    protected static array $createRoles = ['admin', 'mahasiswa'];

    protected static array $viewRoles = ['admin', 'mahasiswa', 'dosen_pembimbing', 'pembimbing_lapangan'];

    protected static array $editRoles = ['admin', 'mahasiswa', 'dosen_pembimbing', 'pembimbing_lapangan'];

    protected static array $deleteRoles = ['admin', 'mahasiswa'];

    protected static bool $useRoleRecordScope = true;

    protected static ?string $model = FrameworkOfReference::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Kerangka Acuan';

    protected static ?string $modelLabel = 'Kerangka Acuan';

    protected static ?string $pluralModelLabel = 'Kerangka Acuan';

    protected static ?string $navigationGroup = 'Magang';

    protected static ?int $navigationSort = 2;










    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Kerangka Acuan')
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

                        Forms\Components\TextInput::make('version')
                            ->label('Versi')
                            ->numeric()
                            ->default(1)
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->label('Judul Kerangka Acuan')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Pekerjaan')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required(),

                        Forms\Components\DatePicker::make('target_end_date')
                            ->label('Target Selesai')
                            ->required(),

                        Forms\Components\Textarea::make('work_plan')
                            ->label('Rencana Kerja')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('ownership_clause')
                            ->label('Klausul Kepemilikan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('confidentiality_clause')
                            ->label('Klausul Kerahasiaan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('remuneration_clause')
                            ->label('Klausul Remunerasi')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Pendukung')
                            ->disk('public')
                            ->directory('internships/frameworks')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                            ])
                            ->maxSize(20480)
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Draft' => 'Draft',
                                'Menunggu_Review' => 'Menunggu Review',
                                'Disetujui_PL' => 'Disetujui Pembimbing Lapangan',
                                'Disetujui' => 'Disetujui Final',
                                'Perlu_Revisi' => 'Perlu Revisi',
                            ])
                            ->default('Draft')
                            ->required(),

                        Forms\Components\Textarea::make('field_supervisor_notes')
                            ->label('Catatan Pembimbing Lapangan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('lecturer_notes')
                            ->label('Catatan Dosen Pembimbing')
                            ->rows(3)
                            ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('internship.company.name')
                    ->label('Instansi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(35),

                Tables\Columns\TextColumn::make('version')
                    ->label('Versi')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => str_replace('_', ' ', $state ?? '-'))
                    ->color(fn (?string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Menunggu_Review' => 'warning',
                        'Disetujui_PL' => 'info',
                        'Disetujui' => 'success',
                        'Perlu_Revisi' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('target_end_date')
                    ->label('Target Selesai')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Draft' => 'Draft',
                        'Menunggu_Review' => 'Menunggu Review',
                        'Disetujui_PL' => 'Disetujui Pembimbing Lapangan',
                        'Disetujui' => 'Disetujui Final',
                        'Perlu_Revisi' => 'Perlu Revisi',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('kirim_review')
                    ->label('Kirim Review')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (FrameworkOfReference $record): bool => in_array($record->status, ['Draft', 'Perlu_Revisi']))
                    ->action(function (FrameworkOfReference $record): void {
                        static::changeStatus(
                            record: $record,
                            newStatus: 'Menunggu_Review',
                            notes: 'Kerangka Acuan dikirim untuk proses review.'
                        );

                        Notification::make()
                            ->title('Kerangka Acuan dikirim untuk review.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('setujui_pl')
                    ->label('Setujui PL')
                    ->icon('heroicon-m-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (FrameworkOfReference $record): bool => $record->status === 'Menunggu_Review')
                    ->action(function (FrameworkOfReference $record): void {
                        $record->field_supervisor_approved_at = now();

                        static::changeStatus(
                            record: $record,
                            newStatus: 'Disetujui_PL',
                            notes: 'Kerangka Acuan disetujui oleh Pembimbing Lapangan.'
                        );

                        Notification::make()
                            ->title('Kerangka Acuan disetujui Pembimbing Lapangan.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('revisi_pl')
                    ->label('Revisi PL')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('field_supervisor_notes')
                            ->label('Catatan Revisi Pembimbing Lapangan')
                            ->required()
                            ->rows(4),
                    ])
                    ->visible(fn (FrameworkOfReference $record): bool => $record->status === 'Menunggu_Review')
                    ->action(function (FrameworkOfReference $record, array $data): void {
                        $record->field_supervisor_notes = $data['field_supervisor_notes'];

                        static::changeStatus(
                            record: $record,
                            newStatus: 'Perlu_Revisi',
                            notes: $data['field_supervisor_notes']
                        );

                        Notification::make()
                            ->title('Kerangka Acuan dikembalikan untuk revisi.')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('setujui_dosen')
                    ->label('Setujui Dosen')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (FrameworkOfReference $record): bool => $record->status === 'Disetujui_PL')
                    ->action(function (FrameworkOfReference $record): void {
                        $record->lecturer_approved_at = now();

                        static::changeStatus(
                            record: $record,
                            newStatus: 'Disetujui',
                            notes: 'Kerangka Acuan disetujui final oleh Dosen Pembimbing.'
                        );

                        $record->internship?->update([
                            'status' => 'Aktif',
                        ]);

                        Notification::make()
                            ->title('Kerangka Acuan disetujui final.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('revisi_dosen')
                    ->label('Revisi Dosen')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('lecturer_notes')
                            ->label('Catatan Revisi Dosen Pembimbing')
                            ->required()
                            ->rows(4),
                    ])
                    ->visible(fn (FrameworkOfReference $record): bool => $record->status === 'Disetujui_PL')
                    ->action(function (FrameworkOfReference $record, array $data): void {
                        $record->lecturer_notes = $data['lecturer_notes'];

                        static::changeStatus(
                            record: $record,
                            newStatus: 'Perlu_Revisi',
                            notes: $data['lecturer_notes']
                        );

                        Notification::make()
                            ->title('Kerangka Acuan dikembalikan untuk revisi dosen.')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function changeStatus(FrameworkOfReference $record, string $newStatus, ?string $notes = null): void
    {
        $oldStatus = $record->status;

        $record->status = $newStatus;
        $record->save();

        AuditTrail::query()->create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_STATUS_KERANGKA_ACUAN',
            'entity_type' => FrameworkOfReference::class,
            'entity_id' => $record->id,
            'previous_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFrameworkOfReferences::route('/'),
            'create' => Pages\CreateFrameworkOfReference::route('/create'),
            'edit' => Pages\EditFrameworkOfReference::route('/{record}/edit'),
        ];
    }
}