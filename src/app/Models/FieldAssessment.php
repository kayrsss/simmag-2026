<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id',
        'evaluator_id',
        'discipline_score',
        'initiative_score',
        'teamwork_score',
        'communication_score',
        'adaptability_score',
        'diligence_score',
        'appearance_score',
        'honesty_score',
        'critical_thinking_score',
        'responsibility_score',
        'overall_score',
        'notes',
        'assessed_at',
    ];

    protected function casts(): array
    {
        return [
            'assessed_at' => 'datetime',
            'overall_score' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (FieldAssessment $assessment) {
            $scores = [
                $assessment->discipline_score,
                $assessment->initiative_score,
                $assessment->teamwork_score,
                $assessment->communication_score,
                $assessment->adaptability_score,
                $assessment->diligence_score,
                $assessment->appearance_score,
                $assessment->honesty_score,
                $assessment->critical_thinking_score,
                $assessment->responsibility_score,
            ];

            $assessment->overall_score = round(array_sum($scores) / count($scores), 2);
            $assessment->assessed_at ??= now();
        });

        static::saved(function (FieldAssessment $assessment) {
            $assessment->internship?->refreshCompletionStatus();
        });
    }

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}