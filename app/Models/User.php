<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nis',
        'kelas',
        'jurusan_id',
        'tanggal_lahir',
        'is_active',
        'warning_count',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_lahir' => 'date',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function getPhotoUrlAttribute()
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }

    public function kelas()
    {
        return $this->belongsToMany(\App\Models\Kelas::class, 'siswa_kelas')
                    ->withPivot('tahun_ajaran')
                    ->withTimestamps();
    }

    public function jurusan()
    {
        return $this->belongsTo(\App\Models\Jurusan::class);
    }
}