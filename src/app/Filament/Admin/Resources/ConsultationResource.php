<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\ConsultationResource\Pages;
use App\Models\AuditTrail;
use App\Models\Consultation;
use App\Models\Internship;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ConsultationResource extends Resource
{
    use HasSimmagResourceAccess;

    protected static array $navigationRoles = ['admin', 'mahasiswa', 'dosen_pembimbing'];

    protected static array $viewAnyRoles = ['admin', 'mahasiswa', 'dosen_pembimbing'];

    protected static array $createRoles = ['admin', 'mahasiswa', 'dosen_pembimbing'];

    protected static array $viewRoles = ['admin', 'mahasiswa', 'dosen_pembimbing'];

    protected static array $editRoles = ['admin', 'mahasiswa', 'dosen_pembimbing'];

    protected static array $deleteRoles = ['admin'];

    protected static bool $useRoleRecordScope = true;

    protected static ?string $model = Consultation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Bimbingan';

    protected static ?string $modelLabel = 'Bimbingan';

    protected static ?string $pluralModelLabel = 'Bimbingan';

    protected static ?string $navigationGroup = 'Magang';

    protected static ?int $navigationSort = 4;












    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Bimbingan')
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

                        Forms\Components\Select::make('student_id')
                            ->label('Mahasiswa')
                            ->options(function () {
                                return User::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('lecturer_id')
                            ->label('Dosen Pembimbing')
                            ->options(function () {
                                return User::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),

                        Forms\Components\DatePicker::make('consultation_date')
                            ->label('Tanggal Bimbingan')
                            ->required(),

                        Forms\Components\TextInput::make('topic')
                            ->label('Topik Bimbingan')
                            ->maxLength(255)
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Mahasiswa / Catatan Tambahan')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('follow_up')
                            ->label('Tindak Lanjut dari Dosen')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Link Pertemuan')
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Diajukan' => 'Diajukan',
                                'Dijadwalkan' => 'Dijadwalkan',
                                'Selesai' => 'Selesai',
                                'Dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('Diajukan')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lecturer.name')
                    ->label('Dosen Pembimbing')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('internship.company.name')
                    ->label('Instansi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('consultation_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('topic')
                    ->label('Topik')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Diajukan' => 'warning',
                        'Dijadwalkan' => 'info',
                        'Selesai' => 'success',
                        'Dibatalkan' => 'danger',
                        default => 'gray',
                    })
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
                        'Diajukan' => 'Diajukan',
                        'Dijadwalkan' => 'Dijadwalkan',
                        'Selesai' => 'Selesai',
                        'Dibatalkan' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('jadwalkan')
                    ->label('Jadwalkan')
                    ->icon('heroicon-m-calendar-days')
                    ->color('info')
                    ->visible(fn (Consultation $record): bool => $record->status === 'Diajukan')
                    ->form([
                        Forms\Components\DatePicker::make('consultation_date')
                            ->label('Tanggal Bimbingan')
                            ->required(),

                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Link Pertemuan')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->action(function (Consultation $record, array $data): void {
                        $record->consultation_date = $data['consultation_date'];
                        $record->meeting_link = $data['meeting_link'] ?? null;

                        static::changeStatus(
                            record: $record,
                            newStatus: 'Dijadwalkan',
                            notes: 'Bimbingan telah dijadwalkan.'
                        );

                        Notification::make()
                            ->title('Bimbingan berhasil dijadwalkan.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('selesaikan')
                    ->label('Catat Hasil')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Consultation $record): bool => in_array($record->status, ['Diajukan', 'Dijadwalkan']))
                    ->form([
                        Forms\Components\Textarea::make('follow_up')
                            ->label('Tindak Lanjut / Hasil Bimbingan')
                            ->required()
                            ->rows(5),
                    ])
                    ->action(function (Consultation $record, array $data): void {
                        $record->follow_up = $data['follow_up'];

                        static::changeStatus(
                            record: $record,
                            newStatus: 'Selesai',
                            notes: $data['follow_up']
                        );

                        Notification::make()
                            ->title('Hasil bimbingan berhasil dicatat.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('batalkan')
                    ->label('Batalkan')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Consultation $record): bool => $record->status !== 'Selesai')
                    ->action(function (Consultation $record): void {
                        static::changeStatus(
                            record: $record,
                            newStatus: 'Dibatalkan',
                            notes: 'Permohonan bimbingan dibatalkan.'
                        );

                        Notification::make()
                            ->title('Bimbingan berhasil dibatalkan.')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->visible(fn (Consultation $record): bool => $record->status !== 'Selesai'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Terpilih'),
            ]);
    }

    private static function changeStatus(Consultation $record, string $newStatus, ?string $notes = null): void
    {
        $oldStatus = $record->status;

        $record->status = $newStatus;
        $record->save();

        AuditTrail::query()->create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_STATUS_BIMBINGAN',
            'entity_type' => Consultation::class,
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
            'index' => Pages\ListConsultations::route('/'),
            'create' => Pages\CreateConsultation::route('/create'),
            'edit' => Pages\EditConsultation::route('/{record}/edit'),
        ];
    }
}