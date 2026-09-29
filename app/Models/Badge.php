<?php
// app/Models/Badge.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    use HasFactory;

    protected $table = 'badge';

    protected $fillable = ['nama_badge', 'deskripsi', 'icon', 'warna'];

    public function penghargaan(): HasMany
    {
        return $this->hasMany(Penghargaan::class);
    }

    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'penghargaan', 'badge_id', 'guru_id')
                    ->withPivot('periode_id')
                    ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges', 'badge_id', 'user_id')
                    ->withPivot('periode_id')
                    ->withTimestamps();
    }
}