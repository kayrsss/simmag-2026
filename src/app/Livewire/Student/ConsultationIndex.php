<?php

namespace App\Livewire\Student;

use App\Models\Consultation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultationIndex extends StudentWorkflowPage
{
    /**
     * ID pengajuan terakhir yang berhasil disimpan.
     */
    public ?int $lastCreatedConsultationId = null;

    /**
     * Form pengajuan langsung terbuka ketika
     * mahasiswa memasuki halaman Bimbingan.
     */
    public function mount(): void
    {
        parent::mount();

        $this->formOpen = true;
    }

    /**
     * Membersihkan form tanpa menutupnya.
     */
    public function closeForm(): void
    {
        $this->reset(
            'topic',
            'notes',
            'meetingLink',
            'reportFile'
        );

        $this->consultationDate = now()
            ->format('Y-m-d');

        $this->formOpen = true;

        $this->resetValidation();
    }

    /**
     * Menyimpan pengajuan Bimbingan mahasiswa.
     */
    public function submitConsultation(): void
    {
        $internship = $this->currentInternship();

        if (! $internship) {
            session()->flash(
                'error',
                'Data magang Anda belum tersedia.'
            );

            $this->addError(
                'topic',
                'Data magang Anda belum tersedia.'
            );

            return;
        }

        if (
            blank(
                $internship->supervisor_lecturer_id
            )
        ) {
            session()->flash(
                'error',
                'Dosen Pembimbing belum ditentukan oleh Administrator.'
            );

            $this->addError(
                'topic',
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
                    'Tanggal Bimbingan wajib diisi.',

                'consultationDate.date' =>
                    'Tanggal Bimbingan tidak valid.',

                'topic.required' =>
                    'Topik Bimbingan wajib diisi.',

                'topic.min' =>
                    'Topik Bimbingan minimal 5 karakter.',

                'topic.max' =>
                    'Topik Bimbingan maksimal 255 karakter.',

                'notes.required' =>
                    'Catatan atau kendala wajib diisi.',

                'notes.min' =>
                    'Catatan atau kendala minimal 10 karakter.',

                'notes.max' =>
                    'Catatan atau kendala maksimal 5.000 karakter.',

                'meetingLink.url' =>
                    'Tautan pertemuan harus berupa URL yang valid.',

                'meetingLink.max' =>
                    'Tautan pertemuan maksimal 255 karakter.',
            ]
        );

        $consultation = DB::transaction(
            function () use (
                $internship,
                $validated
            ): Consultation {
                $consultation =
                    new Consultation();

                $consultation->internship_id =
                    $internship->id;

                $consultation->lecturer_id =
                    $internship
                        ->supervisor_lecturer_id;

                $consultation->student_id =
                    Auth::id();

                $consultation->consultation_date =
                    $validated[
                        'consultationDate'
                    ];

                $consultation->topic =
                    trim(
                        $validated['topic']
                    );

                $consultation->notes =
                    trim(
                        $validated['notes']
                    );

                $consultation->follow_up =
                    null;

                $consultation->meeting_link =
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
                        : null;

                $consultation->status =
                    'Diajukan';

                $consultation->save();

                return $consultation;
            }
        );

        $this->lastCreatedConsultationId =
            $consultation->id;

        $this->reset(
            'topic',
            'notes',
            'meetingLink'
        );

        $this->consultationDate = now()
            ->format('Y-m-d');

        $this->formOpen = true;

        $this->resetValidation();

        session()->flash(
            'success',
            'Pengajuan Bimbingan berhasil disimpan dan dikirim kepada Dosen Pembimbing.'
        );

        $this->dispatch(
            'consultation-created',
            consultationId:
                $consultation->id
        );
    }

    protected function moduleKey(): string
    {
        return 'consultations';
    }
}