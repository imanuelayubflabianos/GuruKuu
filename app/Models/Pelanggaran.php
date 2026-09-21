<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    use HasFactory;

    protected $table = 'pelanggaran';

    protected $fillable = [
        'user_id',
        'tipe',
        'guru_id',
        'penilaian_id',
        'kata_terdeteksi',
        'isi_teks',
        'ip_address',
        'user_agent',
        'is_read',
        'siswa_is_read',
        'read_at',
        'tindakan',
        'notifikasi_siswa',
    ];

    protected $casts = [
        'kata_terdeteksi' => 'array',
        'is_read' => 'boolean',
        'siswa_is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRecent($query)
    {
        return $query->latest();
    }

    /**
     * Label tipe pelanggaran
     */
    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe) {
            'penilaian_toxic' => 'Percobaan Ulasan Kasar / Toxic',
            'kontak_toxic' => 'Pesan Kontak Mengandung Kata Terlarang',
            'komentar_disensor' => 'Ulasan Disensor oleh Admin',
            'ulasan_dihapus' => 'Penilaian Dihapus oleh Admin',
            'peringatan_manual' => 'Peringatan Pelanggaran Manual',
            default => 'Pelanggaran Kebijakan',
        };
    }

    /**
     * Badge status tipe pelanggaran
     */
    public function getTipeBadgeClassAttribute(): string
    {
        return match ($this->tipe) {
            'penilaian_toxic' => 'bg-danger text-white',
            'kontak_toxic' => 'bg-warning text-dark',
            'komentar_disensor' => 'bg-secondary text-white',
            'ulasan_dihapus' => 'bg-danger text-white',
            'peringatan_manual' => 'bg-info text-white',
            default => 'bg-dark text-white',
        };
    }
}
