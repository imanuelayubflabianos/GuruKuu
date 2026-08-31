<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $fillable = [
        'nip', 'nama', 'phone', 'photo', 'kategori',
        'jurusan_id', 'bio', 'rata_rata_nilai', 'total_penilaian',
    ];

    protected $casts = [
        'rata_rata_nilai' => 'decimal:2',
    ];

    protected $appends = ['photo_url'];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function penghargaan()
    {
        return $this->hasMany(Penghargaan::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'guru_kelas')
                    ->withPivot('mata_pelajaran')
                    ->withTimestamps();
    }

    public function scopeNormada($query)
    {
        return $query->where('kategori', 'normada');
    }

    public function scopeProduktif($query)
    {
        return $query->where('kategori', 'produktif');
    }

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

    // === METHOD INI WAJIB ADA AGAR TIDAK ERROR ===
    public function getPersentasePartisipasiDiKelas(int $kelasId, ?int $periodeId = null): float
    {
        $kelas = Kelas::find($kelasId);
        if (!$kelas) return 0.0;

        $totalSiswaDiKelas = $kelas->jumlah_siswa;
        if ($totalSiswaDiKelas == 0) return 0.0;

        $query = Penilaian::where('guru_id', $this->id)->where('class_id', $kelasId);
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $jumlahSiswaMenilai = $query->distinct('siswa_id')->count('siswa_id');
        return round(($jumlahSiswaMenilai / $totalSiswaDiKelas) * 100, 1);
    }

    public function getJumlahSiswaMenilaiDiKelas(int $kelasId, ?int $periodeId = null): int
    {
        $query = Penilaian::where('guru_id', $this->id)->where('class_id', $kelasId);
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }
        return $query->distinct('siswa_id')->count('siswa_id');
    }

    public function getRataRataEvaluasiDiKelas(int $kelasId, ?int $periodeId = null): float
    {
        $query = Penilaian::where('guru_id', $this->id)->where('class_id', $kelasId);
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }
        
        $penilaian = $query->get();
        if ($penilaian->isEmpty()) return 0.0;

        return round($penilaian->avg('total_nilai') / 6, 2);
    }

    public function updateRataRata()
    {
        $penilaian = $this->penilaian;
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