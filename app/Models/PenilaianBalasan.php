<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianBalasan extends Model
{
    use HasFactory;

    protected $table = 'penilaian_balasans';

    protected $fillable = [
        'penilaian_id',
        'user_id',
        'parent_id',
        'pesan',
        'role',
        'is_anonim',
    ];

    protected $casts = [
        'is_anonim' => 'boolean',
    ];

    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(PenilaianBalasan::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(PenilaianBalasan::class, 'parent_id')->oldest();
    }

    /**
     * Dapatkan nama tampilan pengirim yang aman untuk publik & guru
     */
    public function getAuthorNameAttribute()
    {
        if ($this->role === 'guru') {
            return $this->user ? $this->user->name : 'Guru Pembimbing';
        }

        if ($this->is_anonim) {
            return 'Penulis Ulasan (Anonim)';
        }

        return $this->user ? $this->user->name : 'Siswa';
    }

    public function getRoleBadgeClassAttribute()
    {
        return match ($this->role) {
            'guru' => 'bg-primary text-white',
            'admin' => 'bg-dark text-white',
            default => 'bg-warning-subtle text-dark border border-warning-subtle',
        };
    }
}
