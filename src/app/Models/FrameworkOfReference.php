<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrameworkOfReference extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'Draft';
    public const STATUS_MENUNGGU_REVIEW = 'Menunggu_Review';
    public const STATUS_DISETUJUI_PL = 'Disetujui_PL';
    public const STATUS_PERLU_REVISI = 'Perlu_Revisi';
    public const STATUS_DISETUJUI = 'Disetujui';

    protected $fillable = [
        'internship_id',
        'version',
        'title',
        'description',
        'start_date',
        'target_end_date',
        'work_plan',
        'ownership_clause',
        'confidentiality_clause',
        'remuneration_clause',
        'file_path',
        'status',
        'field_supervisor_approved_at',
        'lecturer_approved_at',
        'field_supervisor_notes',
        'lecturer_notes',
        'previous_version_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'target_end_date' => 'date',
            'field_supervisor_approved_at' => 'datetime',
            'lecturer_approved_at' => 'datetime',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_MENUNGGU_REVIEW => 'Menunggu Review',
            self::STATUS_DISETUJUI_PL => 'Disetujui Pembimbing Lapangan',
            self::STATUS_PERLU_REVISI => 'Perlu Revisi',
            self::STATUS_DISETUJUI => 'Disetujui',
        ];
    }

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }

    public function previousVersion()
    {
        return $this->belongsTo(self::class, 'previous_version_id');
    }

    public function nextVersions()
    {
        return $this->hasMany(self::class, 'previous_version_id');
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    public function getIsReadonlyAttribute(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }
}