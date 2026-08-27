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

    public function scopeTerbaik($query, $limit = 10)
    {
        return $query->orderBy('rata_rata_nilai', 'desc')
                     ->orderBy('total_penilaian', 'desc')
                     ->limit($limit);
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

    public function updateRataRata()
    {
        $penilaian = $this->penilaian;
        
        if ($penilaian->isEmpty()) {
            $this->update([
                'rata_rata_nilai' => 0,
                'total_penilaian' => 0,
            ]);
            return;
        }

        $totalNilai = $penilaian->avg('total_nilai') / 6;
        
        $this->update([
            'rata_rata_nilai' => round($totalNilai, 2),
            'total_penilaian' => $penilaian->count(),
        ]);
    }

    public function getTotalSiswaDiajarAttribute()
    {
        return $this->kelas->sum('jumlah_siswa');
    }

    public function getRasioPenilaianAttribute()
    {
        $totalSiswa = $this->total_siswa_diajar;
        if ($totalSiswa == 0) return 0;
        
        $totalPenilaian = $this->penilaian()->count();
        return round(($totalPenilaian / $totalSiswa) * 100, 2);
    }
}