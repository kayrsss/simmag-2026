<?php

namespace App\Livewire\Student;

use App\Models\Consultation;
use App\Models\FinalReport;
use App\Models\Internship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

abstract class StudentWorkflowPage extends Component
{
    use WithFileUploads;

    public bool $formOpen = false;

    public string $consultationDate = '';

    public string $topic = '';

    public string $notes = '';

    public string $meetingLink = '';

    public $reportFile = null;

    abstract protected function moduleKey(): string;

    public function mount(): void
    {
        $this->consultationDate = now()
            ->format('Y-m-d');
    }

    protected function currentInternship(): ?Internship
    {
        return Internship::query()
            ->with([
                'student',
                'supervisorLecturer',
                'company',
                'period',
                'fieldAssessment',
                'lecturerAssessment',
            ])
            ->where(
                'student_id',
                Auth::id()
            )
            ->latest('id')
            ->first();
    }

    public function openForm(): void
    {
        $this->formOpen = true;

        $this->resetValidation();
    }

    public function closeForm(): void
    {
        $this->formOpen = false;

        $this->reset(
            'topic',
            'notes',
            'meetingLink',
            'reportFile'
        );

        $this->consultationDate = now()
            ->format('Y-m-d');

        $this->resetValidation();
    }

    public function submitConsultation(): void
    {
        if (
            $this->moduleKey()
            !== 'consultations'
        ) {
            return;
        }

        $internship =
            $this->currentInternship();

        if (! $internship) {
            session()->flash(
                'error',
                'Data magang Anda belum tersedia.'
            );

            return;
        }

        if (
            ! $internship
                ->supervisor_lecturer_id
        ) {
            session()->flash(
                'error',
                'Dosen Pembimbing belum ditentukan.'
            );

            return;
        }

        $validated = $this->validate(
            [
                'consultationDate' => [
                    'required',
                    'date',
                ],

                'topic' => [
                    'required',
                    'string',
                    'min:5',
                    'max:255',
                ],

                'notes' => [
                    'required',
                    'string',
                    'min:10',
                    'max:5000',
                ],

                'meetingLink' => [
                    'nullable',
                    'url',
                    'max:255',
                ],
            ],
            [
                'consultationDate.required' =>
                    'Tanggal bimbingan wajib diisi.',

                'consultationDate.date' =>
                    'Tanggal bimbingan tidak valid.',

                'topic.required' =>
                    'Topik bimbingan wajib diisi.',

                'topic.min' =>
                    'Topik minimal 5 karakter.',

                'topic.max' =>
                    'Topik maksimal 255 karakter.',

                'notes.required' =>
                    'Catatan bimbingan wajib diisi.',

                'notes.min' =>
                    'Catatan minimal 10 karakter.',

                'notes.max' =>
                    'Catatan maksimal 5.000 karakter.',

                'meetingLink.url' =>
                    'Format tautan pertemuan tidak valid.',

                'meetingLink.max' =>
                    'Tautan pertemuan maksimal 255 karakter.',
            ]
        );

        Consultation::query()->create([
            'internship_id' =>
                $internship->id,

            'lecturer_id' =>
                $internship
                    ->supervisor_lecturer_id,

            'student_id' =>
                Auth::id(),

            'consultation_date' =>
                $validated[
                    'consultationDate'
                ],

            'topic' =>
                trim(
                    $validated['topic']
                ),

            'notes' =>
                trim(
                    $validated['notes']
                ),

            'follow_up' =>
                null,

            'meeting_link' =>
                filled(
                    $validated[
                        'meetingLink'
                    ]
                )
                    ? trim(
                        $validated[
                            'meetingLink'
                        ]
                    )
                    : null,

            'status' =>
                'Diajukan',
        ]);

        $this->closeForm();

        session()->flash(
            'success',
            'Pengajuan bimbingan berhasil dikirim kepada Dosen Pembimbing.'
        );
    }

    public function deleteConsultation(
        int $consultationId
    ): void {
        if (
            $this->moduleKey()
            !== 'consultations'
        ) {
            return;
        }

        $consultation =
            Consultation::query()
                ->where(
                    'id',
                    $consultationId
                )
                ->where(
                    'student_id',
                    Auth::id()
                )
                ->first();

        if (! $consultation) {
            session()->flash(
                'error',
                'Data bimbingan tidak ditemukan.'
            );

            return;
        }

        if (
            $consultation->status
            !== 'Diajukan'
        ) {
            session()->flash(
                'error',
                'Bimbingan yang sudah diproses tidak dapat dihapus.'
            );

            return;
        }

        $consultation->delete();

        session()->flash(
            'success',
            'Pengajuan bimbingan berhasil dihapus.'
        );
    }

    public function submitFinalReport(): void
    {
        if (
            $this->moduleKey()
            !== 'final_reports'
        ) {
            return;
        }

        $internship =
            $this->currentInternship();

        if (! $internship) {
            session()->flash(
                'error',
                'Data magang Anda belum tersedia.'
            );

            return;
        }

        $existingReport =
            FinalReport::query()
                ->where(
                    'internship_id',
                    $internship->id
                )
                ->latest('id')
                ->first();

        if (
            $existingReport
            && $existingReport->status
                === FinalReport::STATUS_DISETUJUI
        ) {
            session()->flash(
                'error',
                'Laporan Akhir sudah disetujui dan tidak dapat diubah.'
            );

            return;
        }

        if (
            $existingReport
            && $existingReport->status
                === FinalReport::STATUS_MENUNGGU_REVIEW
        ) {
            session()->flash(
                'error',
                'Laporan Akhir sedang menunggu review Dosen Pembimbing.'
            );

            return;
        }

        $this->validate(
            [
                'reportFile' => [
                    'required',
                    'file',
                    'mimes:pdf',
                    'max:20480',
                ],
            ],
            [
                'reportFile.required' =>
                    'File Laporan Akhir wajib dipilih.',

                'reportFile.file' =>
                    'File Laporan Akhir tidak valid.',

                'reportFile.mimes' =>
                    'Laporan Akhir wajib berformat PDF.',

                'reportFile.max' =>
                    'Ukuran file maksimal 20 MB.',
            ]
        );

        $storedPath =
            $this->reportFile->store(
                'final-reports/'
                    . Auth::id(),
                'public'
            );

        if ($existingReport) {
            if (
                filled(
                    $existingReport->file_path
                )
                && Storage::disk('public')
                    ->exists(
                        $existingReport
                            ->file_path
                    )
            ) {
                Storage::disk('public')
                    ->delete(
                        $existingReport
                            ->file_path
                    );
            }

            $existingReport->update([
                'file_path' =>
                    $storedPath,

                'word_count' =>
                    null,

                'status' =>
                    FinalReport::STATUS_MENUNGGU_REVIEW,

                'revision_notes' =>
                    null,

                'approved_at' =>
                    null,
            ]);
        } else {
            FinalReport::query()->create([
                'internship_id' =>
                    $internship->id,

                'file_path' =>
                    $storedPath,

                'word_count' =>
                    null,

                'status' =>
                    FinalReport::STATUS_MENUNGGU_REVIEW,

                'revision_notes' =>
                    null,

                'approved_at' =>
                    null,
            ]);
        }

        $this->closeForm();

        session()->flash(
            'success',
            'Laporan Akhir berhasil dikirim kepada Dosen Pembimbing.'
        );
    }

    public function render()
    {
        $internship =
            $this->currentInternship();

        $consultations =
            $internship
                ? Consultation::query()
                    ->with('lecturer')
                    ->where(
                        'internship_id',
                        $internship->id
                    )
                    ->where(
                        'student_id',
                        Auth::id()
                    )
                    ->latest(
                        'consultation_date'
                    )
                    ->latest('id')
                    ->get()
                : collect();

        $finalReport =
            $internship
                ? FinalReport::query()
                    ->where(
                        'internship_id',
                        $internship->id
                    )
                    ->latest('id')
                    ->first()
                : null;

        return view(
            'livewire.student.workflow-index',
            [
                'module' =>
                    $this->moduleKey(),

                'internship' =>
                    $internship,

                'consultations' =>
                    $consultations,

                'finalReport' =>
                    $finalReport,

                'fieldAssessment' =>
                    $internship
                        ?->fieldAssessment,

                'lecturerAssessment' =>
                    $internship
                        ?->lecturerAssessment,
            ]
        )->layout(
            'layouts.simmag',
            [
                'title' => match (
                    $this->moduleKey()
                ) {
                    'consultations' =>
                        'Bimbingan',

                    'final_reports' =>
                        'Laporan Akhir',

                    'assessments' =>
                        'Hasil Penilaian',

                    default =>
                        'SIMMAG',
                },
            ]
        );
    }
}