<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\FinalReportResource\Pages;
use App\Models\AuditTrail;
use App\Models\FinalReport;
use App\Models\Internship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FinalReportResource extends Resource
{
    use HasSimmagResourceAccess;

    protected static array $navigationRoles = ['admin', 'mahasiswa', 'dosen_pembimbing'];

    protected static array $viewAnyRoles = ['admin', 'mahasiswa', 'dosen_pembimbing'];

    protected static array $createRoles = ['admin', 'mahasiswa'];

    protected static array $viewRoles = ['admin', 'mahasiswa', 'dosen_pembimbing'];

    protected static array $editRoles = ['admin', 'mahasiswa', 'dosen_pembimbing'];

    protected static array $deleteRoles = ['admin', 'mahasiswa'];

    protected static bool $useRoleRecordScope = true;

    protected static ?string $model = FinalReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Laporan Akhir';

    protected static ?string $modelLabel = 'Laporan Akhir';

    protected static ?string $pluralModelLabel = 'Laporan Akhir';

    protected static ?string $navigationGroup = 'Magang';

    protected static ?int $navigationSort = 5;












    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Laporan Akhir')
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

                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Laporan Akhir')
                            ->disk('public')
                            ->directory('internships/final-reports')
                            ->acceptedFileTypes([
                                'application/pdf',
                            ])
                            ->maxSize(20480)
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('word_count')
                            ->label('Jumlah Kata')
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Menunggu_Review' => 'Menunggu Review',
                                'Perlu_Revisi' => 'Perlu Revisi',
                                'Disetujui' => 'Disetujui',
                            ])
                            ->default('Menunggu_Review')
                            ->required(),

                        Forms\Components\Textarea::make('revision_notes')
                            ->label('Catatan Revisi')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Waktu Persetujuan'),
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

                Tables\Columns\TextColumn::make('word_count')
                    ->label('Jumlah Kata')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => str_replace('_', ' ', $state ?? '-'))
                    ->color(fn (?string $state): string => match ($state) {
                        'Menunggu_Review' => 'warning',
                        'Perlu_Revisi' => 'danger',
                        'Disetujui' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Disetujui Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Menunggu_Review' => 'Menunggu Review',
                        'Perlu_Revisi' => 'Perlu Revisi',
                        'Disetujui' => 'Disetujui',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('setujui_laporan')
                    ->label('Setujui')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (FinalReport $record): bool => $record->status === 'Menunggu_Review')
                    ->action(function (FinalReport $record): void {
                        $record->approved_at = now();

                        static::changeStatus(
                            record: $record,
                            newStatus: 'Disetujui',
                            notes: 'Laporan akhir disetujui oleh Dosen Pembimbing.'
                        );

                        Notification::make()
                            ->title('Laporan akhir berhasil disetujui.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('revisi_laporan')
                    ->label('Perlu Revisi')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('revision_notes')
                            ->label('Catatan Revisi')
                            ->required()
                            ->rows(4),
                    ])
                    ->visible(fn (FinalReport $record): bool => $record->status === 'Menunggu_Review')
                    ->action(function (FinalReport $record, array $data): void {
                        $record->revision_notes = $data['revision_notes'];
                        $record->approved_at = null;

                        static::changeStatus(
                            record: $record,
                            newStatus: 'Perlu_Revisi',
                            notes: $data['revision_notes']
                        );

                        Notification::make()
                            ->title('Laporan akhir dikembalikan untuk revisi.')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('kirim_ulang')
                    ->label('Kirim Ulang')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (FinalReport $record): bool => $record->status === 'Perlu_Revisi')
                    ->action(function (FinalReport $record): void {
                        $record->approved_at = null;

                        static::changeStatus(
                            record: $record,
                            newStatus: 'Menunggu_Review',
                            notes: 'Laporan akhir revisi dikirim ulang untuk review.'
                        );

                        Notification::make()
                            ->title('Laporan akhir berhasil dikirim ulang.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->visible(fn (FinalReport $record): bool => $record->status !== 'Disetujui'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function changeStatus(FinalReport $record, string $newStatus, ?string $notes = null): void
    {
        $oldStatus = $record->status;

        $record->status = $newStatus;
        $record->save();

        AuditTrail::query()->create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_STATUS_LAPORAN_AKHIR',
            'entity_type' => FinalReport::class,
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
            'index' => Pages\ListFinalReports::route('/'),
            'create' => Pages\CreateFinalReport::route('/create'),
            'edit' => Pages\EditFinalReport::route('/{record}/edit'),
        ];
    }
}