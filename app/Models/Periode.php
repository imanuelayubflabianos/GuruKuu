<?php
// app/Models/Periode.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    use HasFactory;

    protected $table = 'periode';

    protected $fillable = [
        'nama_periode', 'tahun_ajaran', 'semester',
        'status', 'tanggal_mulai', 'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(function ($periode) {
            if ($periode->status === 'aktif') {
                // Pastikan seluruh periode lain otomatis nonaktif (arsip)
                static::where('id', '!=', $periode->id)
                    ->where('status', 'aktif')
                    ->update(['status' => 'nonaktif']);
            }
        });
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function penghargaan(): HasMany
    {
        return $this->hasMany(Penghargaan::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public static function getActivePeriode(): ?self
    {
        $active = self::where('status', 'aktif')->first();
        if (!$active && self::count() > 0) {
            $active = self::latest('tanggal_mulai')->first();
            if ($active) {
                $active->update(['status' => 'aktif']);
            }
        }
        return $active;
    }
}
