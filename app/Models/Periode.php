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
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

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
        return self::aktif()->first();
    }
}