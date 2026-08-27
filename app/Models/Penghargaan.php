<?php
// app/Models/Penghargaan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penghargaan extends Model
{
    use HasFactory;

    protected $table = 'penghargaan';

    protected $fillable = ['guru_id', 'badge_id', 'periode_id'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }
}