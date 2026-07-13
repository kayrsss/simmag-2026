<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logbook extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'Draft';

    public const STATUS_WAITING_VALIDATION =
        'Menunggu_Validasi';

    public const STATUS_VALIDATED =
        'Tervalidasi';

    public const STATUS_REVISION_REQUIRED =
        'Perlu_Revisi';

    protected $fillable = [
        'internship_id',
        'framework_of_reference_id',
        'student_id',
        'activity_date',
        'activity',
        'evidence_name',
        'evidence_path',
        'status',
        'review_note',
        'submitted_at',
        'validated_at',
        'validated_by',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'submitted_at' => 'datetime',
            'validated_at' => 'datetime',
        ];
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(
            Internship::class,
            'internship_id'
        );
    }

    public function frameworkOfReference(): BelongsTo
    {
        return $this->belongsTo(
            FrameworkOfReference::class,
            'framework_of_reference_id'
        );
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'validated_by'
        );
    }

    public function canBeEditedByStudent(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_DRAFT,
                self::STATUS_REVISION_REQUIRED,
            ],
            true
        );
    }

    public function isWaitingValidation(): bool
    {
        return $this->status ===
            self::STATUS_WAITING_VALIDATION;
    }

    public function isValidated(): bool
    {
        return $this->status ===
            self::STATUS_VALIDATED;
    }

    public function isReadOnly(): bool
    {
        return $this->isValidated();
    }
}