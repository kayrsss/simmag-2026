<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\DigitalArchiveResource\Pages;
use App\Models\Internship;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class DigitalArchiveResource extends Resource
{
    use HasSimmagResourceAccess;

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

    protected static array $createRoles = [];

    protected static array $viewRoles = [
        'admin',
        'mahasiswa',
        'dosen_pembimbing',
        'pembimbing_lapangan',
    ];

    protected static array $editRoles = [];

    protected static array $deleteRoles = [];

    protected static bool $useRoleRecordScope = true;

    protected static ?string $model = Internship::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Arsip Digital';

    protected static ?string $modelLabel = 'Arsip Digital';

    protected static ?string $pluralModelLabel = 'Arsip Digital';

    protected static ?string $navigationGroup = 'Magang';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'student',
                'supervisorLecturer',
                'period',
                'company',
                'frameworksOfReference',
                'logbooks',
                'consultations',
                'finalReports',
                'fieldAssessments.evaluator',
                'lecturerAssessments.evaluator',
            ])
            ->withCount([
                'frameworksOfReference',
                'logbooks',
                'consultations',
                'finalReports',
                'fieldAssessments',
                'lecturerAssessments',
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.nim')
                    ->label('NIM')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('company.name')
                    ->label('Instansi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period.academic_year')
                    ->label('Tahun Akademik')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status Magang')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string => match (strtolower($state ?? '')) {
                            'menunggu_ka' => 'Menunggu Kerangka Acuan',
                            'selesai' => 'Magang Selesai',
                            'aktif' => 'Aktif',
                            'draft' => 'Draft',
                            'batal' => 'Batal',
                            default => $state ?? '-',
                        }
                    )
                    ->color(
                        fn (?string $state): string => match (strtolower($state ?? '')) {
                            'draft' => 'gray',
                            'menunggu_ka' => 'warning',
                            'aktif' => 'success',
                            'selesai' => 'info',
                            'batal' => 'danger',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('frameworks_of_reference_count')
                    ->label('KA')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('logbooks_count')
                    ->label('Logbook')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('consultations_count')
                    ->label('Bimbingan')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('final_reports_count')
                    ->label('Laporan')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('field_assessments_count')
                    ->label('Nilai PL')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lecturer_assessments_count')
                    ->label('Nilai Dosen')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status Magang')
                    ->options([
                        'draft' => 'Draft',
                        'menunggu_ka' => 'Menunggu Kerangka Acuan',
                        'aktif' => 'Aktif',
                        'selesai' => 'Magang Selesai',
                        'batal' => 'Batal',
                    ]),

                Tables\Filters\SelectFilter::make('period_id')
                    ->label('Periode')
                    ->relationship('period', 'academic_year'),

                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Instansi')
                    ->relationship('company', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Arsip')
                    ->icon('heroicon-m-eye'),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Ringkasan Mahasiswa Magang')
                    ->schema([
                        Infolists\Components\TextEntry::make('student.name')
                            ->label('Mahasiswa'),

                        Infolists\Components\TextEntry::make('student.nim')
                            ->label('NIM')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('student.email')
                            ->label('Email')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('supervisorLecturer.name')
                            ->label('Dosen Pembimbing')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('company.name')
                            ->label('Instansi Mitra')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('period.academic_year')
                            ->label('Tahun Akademik')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('period.semester')
                            ->label('Semester')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Status Magang')
                            ->badge()
                            ->formatStateUsing(
                                fn (?string $state): string => match (strtolower($state ?? '')) {
                                    'menunggu_ka' => 'Menunggu Kerangka Acuan',
                                    'selesai' => 'Magang Selesai',
                                    'aktif' => 'Aktif',
                                    'draft' => 'Draft',
                                    'batal' => 'Batal',
                                    default => $state ?? '-',
                                }
                            ),

                        Infolists\Components\TextEntry::make('started_at')
                            ->label('Tanggal Mulai')
                            ->date('d M Y')
                            ->placeholder('-'),

                        Infolists\Components\TextEntry::make('ended_at')
                            ->label('Tanggal Selesai')
                            ->date('d M Y')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Kerangka Acuan')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('frameworksOfReference')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Judul'),

                                Infolists\Components\TextEntry::make('version')
                                    ->label('Versi')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (?string $state): string => str_replace('_', ' ', $state ?? '-')
                                    ),

                                Infolists\Components\TextEntry::make('start_date')
                                    ->label('Mulai')
                                    ->date('d M Y')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('target_end_date')
                                    ->label('Target Selesai')
                                    ->date('d M Y')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('file_path')
                                    ->label('File')
                                    ->placeholder('-')
                                    ->url(
                                        fn (?string $state): ?string => $state
                                            ? Storage::disk('public')->url($state)
                                            : null
                                    )
                                    ->openUrlInNewTab(),
                            ])
                            ->columns(3),
                    ]),

                Infolists\Components\Section::make('Logbook Harian')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('logbooks')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('activity_date')
                                    ->label('Tanggal Kegiatan')
                                    ->date('d M Y'),

                                Infolists\Components\TextEntry::make('description')
                                    ->label('Uraian Aktivitas')
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (?string $state): string => str_replace('_', ' ', $state ?? '-')
                                    ),

                                Infolists\Components\TextEntry::make('validation_notes')
                                    ->label('Catatan Validasi')
                                    ->placeholder('-')
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('attachment_file')
                                    ->label('Bukti Pendukung')
                                    ->placeholder('-')
                                    ->url(
                                        fn (?string $state): ?string => $state
                                            ? Storage::disk('public')->url($state)
                                            : null
                                    )
                                    ->openUrlInNewTab(),
                            ])
                            ->columns(2),
                    ]),

                Infolists\Components\Section::make('Bimbingan')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('consultations')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('consultation_date')
                                    ->label('Tanggal Bimbingan')
                                    ->date('d M Y'),

                                Infolists\Components\TextEntry::make('topic')
                                    ->label('Topik')
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('meeting_link')
                                    ->label('Link Pertemuan')
                                    ->placeholder('-')
                                    ->url(fn (?string $state): ?string => $state)
                                    ->openUrlInNewTab(),

                                Infolists\Components\TextEntry::make('follow_up')
                                    ->label('Tindak Lanjut')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),

                Infolists\Components\Section::make('Laporan Akhir')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('finalReports')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(
                                        fn (?string $state): string => str_replace('_', ' ', $state ?? '-')
                                    ),

                                Infolists\Components\TextEntry::make('word_count')
                                    ->label('Jumlah Kata')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('approved_at')
                                    ->label('Disetujui Pada')
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('revision_notes')
                                    ->label('Catatan Revisi')
                                    ->placeholder('-')
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('file_path')
                                    ->label('File Laporan')
                                    ->placeholder('-')
                                    ->url(
                                        fn (?string $state): ?string => $state
                                            ? Storage::disk('public')->url($state)
                                            : null
                                    )
                                    ->openUrlInNewTab(),
                            ])
                            ->columns(2),
                    ]),

                Infolists\Components\Section::make('Penilaian Lapangan')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('fieldAssessments')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('evaluator.name')
                                    ->label('Penilai')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('overall_score')
                                    ->label('Nilai Rata-rata')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('assessed_at')
                                    ->label('Dinilai Pada')
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('notes')
                                    ->label('Catatan')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),

                Infolists\Components\Section::make('Penilaian Dosen')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('lecturerAssessments')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('evaluator.name')
                                    ->label('Penilai')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('overall_score')
                                    ->label('Nilai Rata-rata')
                                    ->badge(),

                                Infolists\Components\TextEntry::make('assessed_at')
                                    ->label('Dinilai Pada')
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('-'),

                                Infolists\Components\TextEntry::make('notes')
                                    ->label('Catatan')
                                    ->placeholder('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDigitalArchives::route('/'),
            'view' => Pages\ViewDigitalArchive::route('/{record}'),
        ];
    }
}