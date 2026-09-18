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
        'deactivation_type',
        'deactivated_until',
        'warning_count',
        'deactivated_reason',
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
        'deactivated_until' => 'datetime',
    ];

    public function isDeactivated(): bool
    {
        return !$this->is_active;
    }

    public function isPermanentlyDeactivated(): bool
    {
        return !$this->is_active && $this->deactivation_type === 'permanen';
    }

    public function isTemporarilyDeactivated(): bool
    {
        return !$this->is_active && $this->deactivation_type === 'berkala';
    }

    public function getDeactivationStatusLabelAttribute(): string
    {
        if ($this->is_active) {
            return 'Aktif';
        }

        if ($this->deactivation_type === 'berkala' && $this->deactivated_until) {
            if (now()->gte($this->deactivated_until)) {
                return 'Masa Suspensi Berakhir';
            }
            return 'Nonaktif Berkala (s/d ' . $this->deactivated_until->translatedFormat('d M Y') . ')';
        }

        return 'Nonaktif Permanen';
    }

    public function getNamaKelasAttribute(): string
    {
        try {
            $rel = $this->relationLoaded('kelas') ? $this->getRelation('kelas') : $this->kelas()->get();
            if ($rel && $rel->isNotEmpty()) {
                $k = $rel->first();
                $tingkat = $k->tingkat ? ' Kelas ' . $k->tingkat : '';
                return $k->nama_kelas . $tingkat;
            }
        } catch (\Throwable $e) {}

        if (!empty($this->attributes['kelas'])) {
            return (string) $this->attributes['kelas'];
        }

        if ($this->relationLoaded('jurusan') && $this->jurusan) {
            return $this->jurusan->nama_jurusan;
        }

        return 'Siswa';
    }

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

    public function guruProfile()
    {
        return $this->hasOne(\App\Models\Guru::class, 'nip', 'nis');
    }

    public function getDetailRoleLabelAttribute()
    {
        if ($this->role === 'guru') {
            $guru = \App\Models\Guru::where('nip', $this->nis)->orWhere('email', $this->email)->first();
            $kategori = $guru && $guru->kategori ? 'Guru ' . ucfirst($guru->kategori) : 'Guru';
            return $kategori;
        }
        return 'Siswa';
    }
}