<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiakadSyncLog extends Model
{
    protected $table = 'siakad_sync_logs';

    protected $fillable = [
        'sync_type',
        'status',
        'total_records',
        'success_count',
        'failed_count',
        'message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'total_records' => 'integer',
        'success_count' => 'integer',
        'failed_count' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}