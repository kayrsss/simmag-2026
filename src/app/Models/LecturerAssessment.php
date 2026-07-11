<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturerAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id',
        'evaluator_id',
        'consistency_score',
        'logbook_completeness_score',
        'neatness_score',
        'content_completeness_score',
        'writing_flow_score',
        'grammar_score',
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
        static::saving(function (LecturerAssessment $assessment) {
            $scores = [
                $assessment->consistency_score,
                $assessment->logbook_completeness_score,
                $assessment->neatness_score,
                $assessment->content_completeness_score,
                $assessment->writing_flow_score,
                $assessment->grammar_score,
            ];

            $assessment->overall_score = round(array_sum($scores) / count($scores), 2);
            $assessment->assessed_at ??= now();
        });

        static::saved(function (LecturerAssessment $assessment) {
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