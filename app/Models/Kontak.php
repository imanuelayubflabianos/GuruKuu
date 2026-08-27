<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;

    protected $table = 'kontak';

    // WAJIB: Semua kolom yang bisa di-update harus ada di sini
    protected $fillable = [
        'pengirim',
        'identifier',
        'pesan',
        'balasan',
        'is_siswa',
        'is_read',
        'is_replied'
    ];
}