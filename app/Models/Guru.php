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

    public function scopeWithRatings($query)
    {
        return $query->whereHas('penilaian');
    }

    public function scopeTeachingInJurusan($query, int $jurusanId)
    {
        return $query->where(function ($query) use ($jurusanId) {
            $query->where('jurusan_id', $jurusanId)
                ->orWhereHas('kelas', function ($kelasQuery) use ($jurusanId) {
                    $kelasQuery->where('jurusan_id', $jurusanId);
                });
        });
    }

    public static function leaderboardFor(string $mode = 'rating', ?int $kelasId = null, ?int $periodeId = null)
    {
        $query = self::with('jurusan');

        if ($mode === 'partisipasi') {
            if (!$kelasId) return collect();
            $query->whereHas('kelas', fn ($kelas) => $kelas->whereKey($kelasId));
        } else {
            $query->withRatings();
            if ($kelasId) {
                $query->whereHas('kelas', fn ($kelas) => $kelas->whereKey($kelasId));
            }
            return $query->orderByDesc('rata_rata_nilai')->orderByDesc('total_penilaian')->get();
        }

        $kelas = Kelas::find($kelasId);
        $totalSiswa = $kelas?->jumlah_siswa ?: $kelas?->siswa()->count();
        $counts = Penilaian::where('class_id', $kelasId)
            ->when($periodeId, fn ($penilaian) => $penilaian->where('periode_id', $periodeId))
            ->selectRaw('guru_id, COUNT(DISTINCT siswa_id) as jumlah_memilih')
            ->groupBy('guru_id')
            ->pluck('jumlah_memilih', 'guru_id');

        return $query->get()->map(function ($guru) use ($counts, $totalSiswa) {
            $jumlahMemilih = (int) ($counts[$guru->id] ?? 0);
            $persentase = $totalSiswa > 0 ? round(($jumlahMemilih / $totalSiswa) * 100, 1) : 0;
            $guru->setAttribute('total_penilaian', $jumlahMemilih);
            $guru->setAttribute('rata_rata_nilai', $persentase / 20);
            $guru->setAttribute('partisipasi_persen', $persentase);
            return $guru;
        })->sortByDesc('partisipasi_persen')->values();
    }

    // ==================== ACCESSOR & HELPER ====================

    public function getLinkedUserAttribute()
    {
        if ($this->relationLoaded('linkedUser')) {
            return $this->getRelation('linkedUser');
        }

        return User::where(function($q) {
            $q->where('nis', $this->nip);

            if (!empty($this->email)) {
                $q->orWhere('email', $this->email);
            }
        })->where('role', 'guru')->first();
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            if (filter_var($this->photo, FILTER_VALIDATE_URL) || str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }
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

    public function updateRataRata(?int $periodeId = null)
    {
        if ($periodeId === null) {
            $periodeAktif = Periode::where('status', 'aktif')->first();
            $periodeId = $periodeAktif?->id;
        }

        $query = $this->penilaian();
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $penilaian = $query->get();
        if ($penilaian->isEmpty()) {
            $this->update(['rata_rata_nilai' => 0, 'total_penilaian' => 0]);
            return;
        }

        $this->update([
            'rata_rata_nilai' => round($penilaian->avg('total_nilai') / 6, 2),
            'total_penilaian' => $penilaian->count(),
        ]);
    }

    public static function recalculateAll(?int $periodeId = null): void
    {
        $gurus = self::all();
        foreach ($gurus as $guru) {
            $guru->updateRataRata($periodeId);
        }
    }
}
