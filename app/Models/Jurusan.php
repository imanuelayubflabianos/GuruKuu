<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = [
        'nama_jurusan',
        'kode_jurusan',
        'deskripsi',
        'logo',
    ];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_jurusan) . '&background=random&color=fff';
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'jurusan_id');
    }

    public function siswa()
    {
        return $this->hasMany(User::class, 'jurusan_id')->where('role', 'siswa');
    }

    public function guru()
    {
        return $this->hasMany(Guru::class, 'jurusan_id');
    }
}