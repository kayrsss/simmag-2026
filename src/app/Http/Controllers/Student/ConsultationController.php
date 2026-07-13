<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Internship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    /**
     * Menyimpan pengajuan bimbingan mahasiswa.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $internship = Internship::query()
            ->where(
                'student_id',
                Auth::id()
            )
            ->latest('id')
            ->first();

        if (! $internship) {
            return redirect()
                ->route(
                    'student.consultations.index'
                )
                ->withInput()
                ->with(
                    'error',
                    'Data magang Anda belum tersedia.'
                );
        }

        if (
            blank(
                $internship
                    ->supervisor_lecturer_id
            )
        ) {
            return redirect()
                ->route(
                    'student.consultations.index'
                )
                ->withInput()
                ->with(
                    'error',
                    'Dosen Pembimbing belum ditentukan oleh Administrator.'
                );
        }

        $validated = $request->validate(
            [
                'consultation_date' => [
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

                'meeting_link' => [
                    'nullable',
                    'url',
                    'max:255',
                ],
            ],
            [
                'consultation_date.required' =>
                    'Tanggal Bimbingan wajib diisi.',

                'consultation_date.date' =>
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

                'meeting_link.url' =>
                    'Tautan pertemuan harus berupa URL yang valid.',

                'meeting_link.max' =>
                    'Tautan pertemuan maksimal 255 karakter.',
            ]
        );

        DB::transaction(
            function () use (
                $internship,
                $validated
            ): void {
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
                            'consultation_date'
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
                                'meeting_link'
                            ] ?? null
                        )
                            ? trim(
                                $validated[
                                    'meeting_link'
                                ]
                            )
                            : null,

                    'status' =>
                        'Diajukan',
                ]);
            }
        );

        return redirect()
            ->route(
                'student.consultations.index'
            )
            ->with(
                'success',
                'Pengajuan Bimbingan berhasil disimpan dan dikirim kepada Dosen Pembimbing.'
            );
    }

    /**
     * Menghapus pengajuan yang masih berstatus Diajukan.
     */
    public function destroy(
        Consultation $consultation
    ): RedirectResponse {
        if (
            (int) $consultation->student_id
            !== (int) Auth::id()
        ) {
            abort(403);
        }

        if (
            $consultation->status
            !== 'Diajukan'
        ) {
            return redirect()
                ->route(
                    'student.consultations.index'
                )
                ->with(
                    'error',
                    'Bimbingan yang sudah diproses tidak dapat dihapus.'
                );
        }

        $consultation->delete();

        return redirect()
            ->route(
                'student.consultations.index'
            )
            ->with(
                'success',
                'Pengajuan Bimbingan berhasil dihapus.'
            );
    }
}