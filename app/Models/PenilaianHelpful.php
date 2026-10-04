<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianHelpful extends Model
{
    use HasFactory;

    protected $table = 'penilaian_helpfuls';

    protected $fillable = [
        'penilaian_id',
        'user_id',
        'ip_address',
    ];

    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
