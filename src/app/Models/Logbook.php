<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logbook extends Model
{
    protected $fillable = [
        'internship_id',
        'framework_of_reference_id',
        'activity_date',
        'description',
        'attachment_file',
        'status',
        'validation_notes',
        'validated_at',
        'submitted_at',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'validated_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function frameworkOfReference(): BelongsTo
    {
        return $this->belongsTo(FrameworkOfReference::class);
    }
}