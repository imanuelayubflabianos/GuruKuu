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

    protected $appends = ['photo_url', 'kategori_label'];

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

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'penghargaan', 'guru_id', 'badge_id')
                    ->withPivot('periode_id')
                    ->withTimestamps();
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

    public const MIN_PENILAIAN_LEADERBOARD = 5;

    protected static array $leaderboardCache = [];

    public static function leaderboardFor(string $mode = 'rating', ?int $kelasId = null, ?int $periodeId = null)
    {
        // Pastikan periode aktif jika tidak dispesifikasi
        if (!$periodeId) {
            $periodeAktif = Periode::where('status', 'aktif')->first();
            $periodeId = $periodeAktif?->id;
        }

        $cacheKey = $mode . '_' . ($kelasId ?? 0) . '_' . ($periodeId ?? 0);
        if (isset(self::$leaderboardCache[$cacheKey])) {
            return self::$leaderboardCache[$cacheKey];
        }

        $query = self::with(['jurusan', 'penghargaan.badge']);

        if ($mode === 'partisipasi') {
            if (!$kelasId) return collect();
            // Hanya guru yang terdaftar mengajar di kelas ini sesuai relasi resmi di database
            $query->whereHas('kelas', fn ($kelas) => $kelas->whereKey($kelasId));

            $kelas = Kelas::find($kelasId);
            $totalSiswa = $kelas?->jumlah_siswa ?: ($kelas?->siswa()->count() ?: 0);

            // Ambil penilaian siswa unik (1 siswa tidak dihitung ganda untuk guru yang sama)
            $penilaians = Penilaian::select('id', 'guru_id', 'siswa_id', 'total_nilai', 'periode_id', 'class_id')
                ->where('class_id', $kelasId)
                ->when($periodeId, fn ($penilaian) => $penilaian->where('periode_id', $periodeId))
                ->latest()
                ->get()
                ->unique(fn ($p) => $p->guru_id . '_' . $p->siswa_id);

            $gurus = $query->get()->map(function ($guru) use ($penilaians, $totalSiswa) {
                $guruPenilaians = $penilaians->where('guru_id', $guru->id);
                $jumlahMemilih = $guruPenilaians->count();

                $persentase = $totalSiswa > 0 ? round(($jumlahMemilih / $totalSiswa) * 100, 1) : 0;
                $rataRata = $jumlahMemilih > 0 ? round($guruPenilaians->avg('total_nilai') / 5, 2) : 0.0;

                $guru->setAttribute('total_penilaian', $jumlahMemilih);
                $guru->setAttribute('rata_rata_nilai', $rataRata);
                $guru->setAttribute('persentase_kepuasan', round(($rataRata / 5) * 100));
                $guru->setAttribute('partisipasi_persen', $persentase);
                $guru->setAttribute('is_eligible_leaderboard', $jumlahMemilih >= self::MIN_PENILAIAN_LEADERBOARD);

                return $guru;
            });

            // Hanya tampilkan guru yang memiliki setidaknya 1 penilaian pada kelas ini
            $gurusWithData = $gurus->filter(fn ($g) => $g->total_penilaian > 0);

            // Bagi 2 grup: Memenuhi syarat (>= MIN) & Belum memenuhi syarat (1 s/d < MIN)
            $eligible = $gurusWithData->filter(fn ($g) => $g->is_eligible_leaderboard)
                ->sort(function ($a, $b) {
                    if ($b->partisipasi_persen != $a->partisipasi_persen) {
                        return $b->partisipasi_persen <=> $a->partisipasi_persen;
                    }
                    if ($b->total_penilaian != $a->total_penilaian) {
                        return $b->total_penilaian <=> $a->total_penilaian;
                    }
                    return strcmp($a->nama, $b->nama);
                })->values();

            $notEligible = $gurusWithData->filter(fn ($g) => !$g->is_eligible_leaderboard)
                ->sort(function ($a, $b) {
                    if ($b->partisipasi_persen != $a->partisipasi_persen) {
                        return $b->partisipasi_persen <=> $a->partisipasi_persen;
                    }
                    if ($b->total_penilaian != $a->total_penilaian) {
                        return $b->total_penilaian <=> $a->total_penilaian;
                    }
                    return strcmp($a->nama, $b->nama);
                })->values();

            $rank = 1;
            foreach ($eligible as $guru) {
                $guru->setAttribute('leaderboard_rank', $rank++);
            }
            foreach ($notEligible as $guru) {
                $guru->setAttribute('leaderboard_rank', null);
            }

            return self::$leaderboardCache[$cacheKey] = $eligible->concat($notEligible);
        }

        // Mode Semua Guru (Rating Kepuasan)
        // Ambil penilaian periode aktif, pastikan 1 siswa tidak dihitung ganda untuk guru yang sama
        $penilaians = Penilaian::select('id', 'guru_id', 'siswa_id', 'total_nilai', 'periode_id')
            ->when($periodeId, fn ($q) => $q->where('periode_id', $periodeId))
            ->latest()
            ->get()
            ->unique(fn ($p) => $p->guru_id . '_' . $p->siswa_id);

        $penilaianByGuru = $penilaians->groupBy('guru_id');

        $gurus = $query->get()->map(function ($guru) use ($penilaianByGuru) {
            $guruPenilaians = $penilaianByGuru->get($guru->id, collect());
            $totalPenilaian = $guruPenilaians->count();

            if ($totalPenilaian > 0) {
                $rataRata = round($guruPenilaians->avg('total_nilai') / 5, 2);
                $persentaseKepuasan = round(($rataRata / 5) * 100);
            } else {
                $rataRata = 0.0;
                $persentaseKepuasan = 0;
            }

            $guru->setAttribute('total_penilaian', $totalPenilaian);
            $guru->setAttribute('rata_rata_nilai', $rataRata);
            $guru->setAttribute('persentase_kepuasan', $persentaseKepuasan);
            $guru->setAttribute('is_eligible_leaderboard', $totalPenilaian >= self::MIN_PENILAIAN_LEADERBOARD);

            return $guru;
        });

        // Hanya tampilkan guru yang memiliki setidaknya 1 penilaian pada periode ini
        $gurusWithData = $gurus->filter(fn ($g) => $g->total_penilaian > 0);

        // Group 1: Memenuhi syarat ranking (minimal 5 penilaian)
        $eligible = $gurusWithData->filter(fn ($g) => $g->is_eligible_leaderboard)
            ->sort(function ($a, $b) {
                if ($b->rata_rata_nilai != $a->rata_rata_nilai) {
                    return $b->rata_rata_nilai <=> $a->rata_rata_nilai;
                }
                if ($b->total_penilaian != $a->total_penilaian) {
                    return $b->total_penilaian <=> $a->total_penilaian;
                }
                return strcmp($a->nama, $b->nama);
            })->values();

        // Group 2: Belum memenuhi syarat (< 5 penilaian)
        $notEligible = $gurusWithData->filter(fn ($g) => !$g->is_eligible_leaderboard)
            ->sort(function ($a, $b) {
                if ($b->rata_rata_nilai != $a->rata_rata_nilai) {
                    return $b->rata_rata_nilai <=> $a->rata_rata_nilai;
                }
                if ($b->total_penilaian != $a->total_penilaian) {
                    return $b->total_penilaian <=> $a->total_penilaian;
                }
                return strcmp($a->nama, $b->nama);
            })->values();

        // Berikan nomor peringkat hanya untuk guru yang eligible
        $rank = 1;
        foreach ($eligible as $guru) {
            $guru->setAttribute('leaderboard_rank', $rank++);
        }
        foreach ($notEligible as $guru) {
            $guru->setAttribute('leaderboard_rank', null);
        }

        return self::$leaderboardCache[$cacheKey] = $eligible->concat($notEligible);
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
        
        // Warna tema default Guru: Biru Tua (#003366)
        $color = '003366';
        
        return "https://ui-avatars.com/api/?name={$initials}&background={$color}&color=fff&size=200&bold=true";
    }

    public function getKategoriLabelAttribute(): string
    {
        if ($this->kategori === 'produktif') {
            return $this->jurusan ? 'Guru Produktif ' . $this->jurusan->nama_jurusan : 'Guru Produktif';
        }
        if ($this->kategori === 'normada') {
            return 'Guru Normada';
        }
        return $this->jurusan ? 'Guru ' . $this->jurusan->nama_jurusan : 'Guru Pengajar';
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

        return round($penilaian->avg('total_nilai') / 5, 2);
    }

    public function updateRataRata(?int $periodeId = null)
    {
        if ($periodeId === null) {
            $periodeAktif = Periode::where('status', 'aktif')->first();
            $periodeId = $periodeAktif?->id;
        }

        $query = $this->penilaian()->select(['id', 'guru_id', 'siswa_id', 'total_nilai', 'periode_id']);
        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $penilaian = $query->latest()->get()->unique('siswa_id');
        if ($penilaian->isEmpty()) {
            $this->update(['rata_rata_nilai' => 0, 'total_penilaian' => 0]);
            return;
        }

        $this->update([
            'rata_rata_nilai' => round($penilaian->avg('total_nilai') / 5, 2),
            'total_penilaian' => $penilaian->count(),
        ]);
    }

    public static function recalculateAll(?int $periodeId = null): void
    {
        $gurus = self::all();
        foreach ($gurus as $guru) {
            $guru->updateRataRata($periodeId);
        }

        try {
            \Illuminate\Support\Facades\Cache::flush();
        } catch (\Throwable $e) {}
    }
}
