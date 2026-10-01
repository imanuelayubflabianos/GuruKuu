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

    public function getPesanAttribute($value): ?string
    {
        if (empty($value)) return $value;
        try {
            $value = \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Throwable $e) {}
        return \App\Services\ProfanityFilterService::mask($value);
    }

    public function setPesanAttribute($value): void
    {
        if (!empty($value)) {
            $this->attributes['pesan'] = \Illuminate\Support\Facades\Crypt::encryptString($value);
        } else {
            $this->attributes['pesan'] = $value;
        }
    }

    public function getBalasanAttribute($value): ?string
    {
        if (empty($value)) return $value;
        try {
            $value = \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Throwable $e) {}
        return \App\Services\ProfanityFilterService::mask($value);
    }

    public function setBalasanAttribute($value): void
    {
        if (!empty($value)) {
            $this->attributes['balasan'] = \Illuminate\Support\Facades\Crypt::encryptString($value);
        } else {
            $this->attributes['balasan'] = $value;
        }
    }

    public function getDisplayPengirimAttribute(): string
    {
        if ($this->is_siswa) {
            return $this->pengirim;
        }
        if (!empty($this->pengirim) && str_starts_with($this->pengirim, 'Tamu #')) {
            return $this->pengirim;
        }
        $cleanId = preg_replace('/[^a-zA-Z0-9]/', '', (string)$this->identifier);
        $code = strtoupper(substr($cleanId, -4));
        return 'Tamu #' . ($code ?: $this->id);
    }
}