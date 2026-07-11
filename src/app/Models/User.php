<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',

        'role',

        'identifier',
        'username',

        'nim',
        'nidn',
        'nip',

        'phone',

        'program_study_id',

        'institution_name',

        'avatar_url',

        'theme',
        'theme_color',

        'is_active',

        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole([
            'admin',
            'mahasiswa',
            'dosen_pembimbing',
            'pembimbing_lapangan',
        ]);
    }

    public function programStudy(): BelongsTo
    {
        return $this->belongsTo(ProgramStudy::class);
    }
}