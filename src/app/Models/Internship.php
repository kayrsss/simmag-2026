<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Internship extends Model
{
    protected $fillable = [
        'student_id',
        'student_name',
        'student_nim',
        'student_email',
        'program_study_name',
        'university_name',
        'period_id',
        'supervisor_lecturer_id',
        'lecturer_name',
        'lecturer_identifier',
        'company_id',
        'company_name',
        'field_supervisor_name',
        'field_supervisor_position',
        'field_supervisor_phone',
        'field_supervisor_email',
        'status',
        'submitted_by',
        'submitted_at',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Internship $internship): void {
            $internship->syncSnapshotData();

            if (! $internship->submitted_by && auth()->check()) {
                $internship->submitted_by = auth()->id();
            }

            if (! $internship->submitted_at) {
                $internship->submitted_at = now();
            }
        });
    }

    public function syncSnapshotData(): void
    {
        if ($this->student_id) {
            $student = User::query()
                ->with('programStudy')
                ->find($this->student_id);

            if ($student) {
                $this->student_name = $student->name;
                $this->student_nim = $student->nim ?? $student->identifier;
                $this->student_email = $student->email;
                $this->program_study_name = $student->programStudy?->name;
                $this->university_name = $student->institution_name;
            }
        }

        if ($this->supervisor_lecturer_id) {
            $lecturer = User::query()->find($this->supervisor_lecturer_id);

            if ($lecturer) {
                $this->lecturer_name = $lecturer->name;
                $this->lecturer_identifier = $lecturer->nidn
                    ?? $lecturer->nip
                    ?? $lecturer->identifier;
            }
        } else {
            $this->lecturer_name = null;
            $this->lecturer_identifier = null;
        }

        if ($this->company_id) {
            $company = CompanyProfile::query()->find($this->company_id);

            $this->company_name = $company?->name;
        } else {
            $this->company_name = null;
        }
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function supervisorLecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_lecturer_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class, 'company_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function frameworksOfReference(): HasMany
    {
        return $this->hasMany(FrameworkOfReference::class);
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function finalReports(): HasMany
    {
        return $this->hasMany(FinalReport::class);
    }

    public function fieldAssessment(): HasOne
    {
        return $this->hasOne(FieldAssessment::class);
    }

    public function lecturerAssessment(): HasOne
    {
        return $this->hasOne(LecturerAssessment::class);
    }

    public function fieldAssessments(): HasMany
    {
        return $this->hasMany(FieldAssessment::class);
    }

    public function lecturerAssessments(): HasMany
    {
        return $this->hasMany(LecturerAssessment::class);
    }

    public function refreshCompletionStatus(): void
    {
        if (
            $this->fieldAssessment()->exists()
            && $this->lecturerAssessment()->exists()
            && $this->status !== 'selesai'
        ) {
            $this->update([
                'status' => 'selesai',
            ]);
        }
    }
}