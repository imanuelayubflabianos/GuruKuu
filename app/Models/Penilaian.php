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
        'kedisiplinan', 'cara_mengajar', 'komunikasi',
        'tanggung_jawab', 'kreativitas', 'keramahan',
        'total_nilai', 'kritik', 'saran',
    ];

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

    // ==================== METHOD ====================

    public static function hitungTotal(array $data): int
    {
        return ($data['kedisiplinan'] ?? 0) + ($data['cara_mengajar'] ?? 0) +
               ($data['komunikasi'] ?? 0) + ($data['tanggung_jawab'] ?? 0) +
               ($data['kreativitas'] ?? 0) + ($data['keramahan'] ?? 0);
    }

    // Rata-rata dari 6 aspek evaluasi (skala 1-5)
    public function getRataRataEvaluasiAttribute(): float
    {
        return round($this->total_nilai / 6, 2);
    }

    // ==================== DETEKSI TOXIC ====================

    public static function detectToxic(?string $text): bool
    {
        if (!$text) return false;
        $toxicWords = [
            'anjing', 'babi', 'goblok', 'tolol', 'bodoh', 'bangsat', 'keparat',
            'sialan', 'brengsek', 'kampret', 'monyet', 'bego', 'dungu', 'idiot',
            'setan', 'iblis', 'bajingan', 'pecundang', 'sampah', 'busuk', 'mampus',
            'mati aja', 'gila', 'sinting', 'bebal', 'otak udang',
        ];
        $textLower = strtolower($text);
        foreach ($toxicWords as $word) {
            if (str_contains($textLower, $word)) return true;
        }
        return false;
    }

    public function isToxic(): bool
    {
        return self::detectToxic($this->kritik) || self::detectToxic($this->saran);
    }
}