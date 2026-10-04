<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'siswa_id', 'guru_id', 'periode_id', 'class_id',
        'kedisiplinan', 'komunikasi',
        'tanggung_jawab', 'kreativitas', 'keramahan',
        'total_nilai', 'kritik', 'saran', 'helpful_count',
        'is_censored', 'censored_reason',
        'balasan_guru', 'balasan_guru_at',
    ];

    protected $casts = [
        'is_censored' => 'boolean',
        'balasan_guru_at' => 'datetime',
        'helpful_count' => 'integer',
    ];

    protected $attributes = [
        'cara_mengajar' => 0,
    ];

    protected static function booted()
    {
        static::deleting(function ($penilaian) {
            // Hapus semua riwayat percakapan diskusi/balasan ulasan ini
            $penilaian->balasans()->delete();
            // Hapus upvote / helpful ulasan ini
            $penilaian->helpfuls()->delete();
            // Hapus relasi log pelanggaran yang terkait ulasan ini jika ada
            \App\Models\Pelanggaran::where('penilaian_id', $penilaian->id)->delete();
        });
    }

    // ==================== RELASI ====================

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function balasans()
    {
        return $this->hasMany(PenilaianBalasan::class, 'penilaian_id')->oldest();
    }

    public function helpfuls()
    {
        return $this->hasMany(PenilaianHelpful::class, 'penilaian_id');
    }

    public function isHelpfulBy(?int $userId = null, ?string $ip = null): bool
    {
        if ($userId) {
            return $this->helpfuls()->where('user_id', $userId)->exists();
        }
        if ($ip) {
            return $this->helpfuls()->where('ip_address', $ip)->exists();
        }
        return false;
    }

    // ==================== METHOD ====================

    public static function hitungTotal(array $data): int
    {
        return ($data['kedisiplinan'] ?? 0) +
               ($data['komunikasi'] ?? 0) + ($data['tanggung_jawab'] ?? 0) +
               ($data['kreativitas'] ?? 0) + ($data['keramahan'] ?? 0);
    }

    // Rata-rata dari 6 aspek evaluasi (skala 1-5)
    public function getRataRataEvaluasiAttribute(): float
    {
        return round($this->total_nilai / 5, 2);
    }

    public function getKritikAttribute($value): ?string
    {
        if (empty($value)) return $value;
        return \App\Services\ProfanityFilterService::mask($value);
    }

    public function getSaranAttribute($value): ?string
    {
        if (empty($value)) return $value;
        return \App\Services\ProfanityFilterService::mask($value);
    }

    public function getBalasanGuruAttribute($value): ?string
    {
        if (empty($value)) return $value;
        return \App\Services\ProfanityFilterService::mask($value);
    }

    // ==================== DETEKSI TOXIC ====================

    public static function detectToxic(?string $text): bool
    {
        if (!$text) return false;
        return !\App\Services\ProfanityFilterService::isClean($text);
    }

    public function isToxic(): bool
    {
        return self::detectToxic($this->getRawOriginal('kritik')) || self::detectToxic($this->getRawOriginal('saran'));
    }
}