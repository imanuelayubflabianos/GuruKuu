<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'nis',
        'email',
        'password',
        'role',
        'phone',
        'kelas',
        'jurusan_id',
        'tanggal_lahir',
        'is_active',
        'force_change_password',
        'activated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'activated_at' => 'datetime',
        'is_active' => 'boolean',
        'force_change_password' => 'boolean',
    ];

    protected $appends = ['photo_url'];

    // ==================== RELASI ====================

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'siswa_id');
    }

    // ✅ BARU: Relasi ke Kelas (Many-to-Many)
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'siswa_kelas')
                    ->withPivot('tahun_ajaran')
                    ->withTimestamps();
    }

    // ==================== ACCESSOR ====================

    public function getPhotoUrlAttribute()
    {
        // Generate avatar default berdasarkan nama
        $initials = collect(explode(' ', $this->name))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');
        
        $colors = ['003366', '00A86B', 'FFC107', '6366f1', 'ec4899'];
        $color = $colors[$this->id % count($colors)];
        
        return "https://ui-avatars.com/api/?name={$initials}&background={$color}&color=fff&size=200&bold=true";
    }

    // ✅ BARU: Get kelas aktif siswa saat ini
    public function getCurrentKelasAttribute()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        if (!$periodeAktif) return null;
        
        return $this->kelas()
                    ->wherePivot('tahun_ajaran', $periodeAktif->tahun_ajaran)
                    ->first();
    }

    // ==================== SCOPE ====================

    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ==================== METHOD ====================

    public function hasPenilaianForGuruInPeriode(Guru $guru, Periode $periode)
    {
        return $this->penilaian()
                    ->where('guru_id', $guru->id)
                    ->where('periode_id', $periode->id)
                    ->exists();
    }

    public function getGuruYangBisaDinilai()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        if (!$periodeAktif) return collect();

        // ✅ Get kelas aktif siswa
        $kelasAktif = $this->current_kelas;
        if (!$kelasAktif) return collect();

        // ✅ Return hanya guru yang mengajar di kelas siswa tersebut
        return $kelasAktif->guru;
    }
}