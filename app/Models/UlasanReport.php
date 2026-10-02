<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UlasanReport extends Model
{
    use HasFactory;

    protected $table = 'ulasan_reports';

    protected $fillable = [
        'penilaian_id',
        'user_id',
        'ip_address',
        'alasan',
        'catatan',
        'is_reviewed',
    ];

    protected $casts = [
        'is_reviewed' => 'boolean',
    ];

    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getAlasanLabelAttribute(): string
    {
        return match ($this->alasan) {
            'kata_kasar' => 'Kata Kasar / Tidak Pantas',
            'ujaran_kebencian' => 'Ujaran Kebencian / Toxic',
            'fitnah' => 'Pencemaran / Fitnah Guru',
            'spam' => 'Spam / Tidak Relevan',
            default => 'Pelanggaran Aturan Komunitas',
        };
    }
}
