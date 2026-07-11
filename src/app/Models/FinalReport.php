<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalReport extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU_REVIEW = 'Menunggu_Review';
    public const STATUS_PERLU_REVISI = 'Perlu_Revisi';
    public const STATUS_DISETUJUI = 'Disetujui';

    protected $fillable = [
        'internship_id',
        'file_path',
        'word_count',
        'status',
        'revision_notes',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_MENUNGGU_REVIEW => 'Menunggu Review',
            self::STATUS_PERLU_REVISI => 'Perlu Revisi',
            self::STATUS_DISETUJUI => 'Disetujui',
        ];
    }

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }
}