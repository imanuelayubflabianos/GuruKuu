<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;

    protected $table = 'kontak';

    protected $fillable = [
        'pengirim',
        'identifier',
        'pesan',
        'balasan',
        'is_siswa',
        'is_read',
        'is_replied',
    ];

    protected $casts = [
        'is_siswa' => 'boolean',
        'is_read' => 'boolean',
        'is_replied' => 'boolean',
    ];
}