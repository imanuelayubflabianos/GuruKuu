<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'jurusan_id',
        'tingkat',
        'jumlah_siswa',
    ];

    public function getLabelSingkatAttribute(): string
    {
        $nama = trim((string) $this->nama_kelas);
        $tingkat = trim((string) $this->tingkat);
        $kodeJurusan = trim((string) ($this->jurusan?->kode_jurusan ?? ''));

        $nama = preg_replace('/\s*Kelas\s*' . preg_quote($tingkat, '/') . '\s*/i', ' ', $nama);
        $nama = trim(preg_replace('/\s+/', ' ', $nama));

        if ($kodeJurusan !== '' && !str_contains(strtolower($nama), strtolower($kodeJurusan))) {
            $nomorKelas = preg_match('/(\d+)\s*$/', $nama, $matches) ? $matches[1] : '';
            if ($nomorKelas !== '') {
                $nama = $kodeJurusan . ' ' . $nomorKelas;
            }
        }

        return $tingkat !== '' ? 'Kelas ' . $tingkat . ' ' . $nama : $nama;
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function guru()
    {
        return $this->belongsToMany(Guru::class, 'guru_kelas')
                    ->withPivot('mata_pelajaran')
                    ->withTimestamps();
    }

    public function siswa()
    {
        return $this->belongsToMany(User::class, 'siswa_kelas')
                    ->withPivot('tahun_ajaran')
                    ->withTimestamps();
    }
}