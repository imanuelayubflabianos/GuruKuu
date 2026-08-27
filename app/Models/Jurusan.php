<?php
// app/Models/Jurusan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = ['nama_jurusan', 'kode_jurusan', 'deskripsi'];

    public function guru(): HasMany
    {
        return $this->hasMany(Guru::class);
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'siswa');
    }
}