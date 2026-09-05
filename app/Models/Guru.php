<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $fillable = [
        'nip',
        'nama',
        'email',
        'phone',
        'photo',
        'kategori',
        'jurusan_id',
        'bio',
        'rata_rata_nilai',
        'total_penilaian',
    ];

    protected $casts = [
        'rata_rata_nilai' => 'decimal:2',
        'total_penilaian' => 'integer',
    ];

    protected $appends = ['photo_url'];

    // ==================== RELASI ELOQUENT ====================

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'guru_id');
    }

    public function penghargaan()
    {
        return $this->hasMany(Penghargaan::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'guru_kelas', 'guru_id', 'kelas_id')
                    ->withPivot('mata_pelajaran')
                    ->withTimestamps();
    }

    // ==================== SCOPES ====================

    public function scopeNormada($query)
    {
        return $query->where('kategori', 'normada');
    }

    public function scopeProduktif($query)
    {
        return $query->where('kategori', 'produktif');
    }

    // ==================== ACCESSOR & HELPER ====================

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        
        $initials = collect(explode(' ', $this->nama))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');
        
        $colors = ['003366', '00A86B', 'FFC107', '6366f1', 'ec4899'];
        $color = $colors[$this->id % count($colors)];
        
        return "https://ui-avatars.com/api/?name={$initials}&background={$color}&color=fff&size=200&bold=true";
    }

    public function getPersentasePartisipasiDiKelas(?int $kelasId = null, ?int $periodeId = null): float
    {
        if ($kelasId) {
            $kelas = Kelas::find($kelasId);
            if (!$kelas || $kelas->jumlah_siswa <= 0) return 0.0;
            $totalSiswa = $kelas->jumlah_siswa;

            $query = Penilaian::where('guru_id', $this->id)->where('class_id', $kelasId);
            if ($periodeId) {
                $query->where('periode_id', $periodeId);
            }
            $jumlahMenilai = $query->distinct('siswa_id')->count('siswa_id');
            return round(($jumlahMenilai / $totalSiswa) * 100, 1);
        }

        $totalSiswa = $this->kelas->sum('jumlah_siswa');
        if ($totalSiswa <= 0) return 0.0;

        $query = Penilaian::where('guru_id', $this->id);
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }
        $jumlahMenilai = $query->distinct('siswa_id')->count('siswa_id');
        return round(($jumlahMenilai / $totalSiswa) * 100, 1);
    }

    public function getJumlahSiswaMenilaiDiKelas(?int $kelasId = null, ?int $periodeId = null): int
    {
        $query = Penilaian::where('guru_id', $this->id);
        if ($kelasId) {
            $query->where('class_id', $kelasId);
        }
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }
        return $query->distinct('siswa_id')->count('siswa_id');
    }

    public function getRataRataEvaluasiDiKelas(?int $kelasId = null, ?int $periodeId = null): float
    {
        $query = Penilaian::where('guru_id', $this->id);
        if ($kelasId) {
            $query->where('class_id', $kelasId);
        }
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }
        
        $penilaian = $query->get();
        if ($penilaian->isEmpty()) return 0.0;

        return round($penilaian->avg('total_nilai') / 6, 2);
    }

    public function updateRataRata()
    {
        $penilaian = $this->penilaian()->get();
        if ($penilaian->isEmpty()) {
            $this->update(['rata_rata_nilai' => 0, 'total_penilaian' => 0]);
            return;
        }
        $this->update([
            'rata_rata_nilai' => round($penilaian->avg('total_nilai') / 6, 2),
            'total_penilaian' => $penilaian->count(),
        ]);
    }
}