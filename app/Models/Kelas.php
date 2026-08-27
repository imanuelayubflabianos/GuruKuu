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