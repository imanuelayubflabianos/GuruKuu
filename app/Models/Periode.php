<?php
// app/Models/Periode.php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    use HasFactory;

    protected $table = 'periode';

    protected $fillable = [
        'nama_periode',
        'tahun_ajaran',
        'semester',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    protected static function booted()
    {
        static::saved(function ($periode) {
            if ($periode->status === 'aktif') {
                // Pastikan seluruh periode lain otomatis selesai / nonaktif (arsip)
                static::where('id', '!=', $periode->id)
                    ->where('status', 'aktif')
                    ->update(['status' => 'selesai']);
            }
        });
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function penghargaan(): HasMany
    {
        return $this->hasMany(Penghargaan::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeTerjadwal($query)
    {
        return $query->where('status', 'terjadwal');
    }

    public function scopeSelesai($query)
    {
        return $query->whereIn('status', ['selesai', 'nonaktif']);
    }

    public function scopeArsip($query)
    {
        return $query->whereIn('status', ['selesai', 'nonaktif']);
    }

    /**
     * Hitung status otomatis berdasarkan rentang tanggal
     */
    public static function calculateStatusFromDates($tanggalMulai, $tanggalSelesai): string
    {
        $now = now();
        $mulai = $tanggalMulai ? Carbon::parse($tanggalMulai) : null;
        $selesai = $tanggalSelesai ? Carbon::parse($tanggalSelesai) : null;

        if ($mulai && $now->lt($mulai)) {
            return 'terjadwal';
        }

        if ($selesai && $now->gt($selesai)) {
            return 'selesai';
        }

        return 'aktif';
    }

    /**
     * Sinkronisasi seluruh status periode secara otomatis berdasarkan waktu sekarang.
     */
    public static function syncAllStatuses(): bool
    {
        static $lastSynced = null;
        $now = now();

        // Throttle 1 detik agar tidak redundan dalam satu request (kecuali saat testing)
        if (!app()->runningUnitTests()) {
            static $lastSyncedWallTime = null;
            $wallNow = microtime(true);
            if ($lastSyncedWallTime && ($wallNow - $lastSyncedWallTime) < 1.0) {
                return false;
            }
            $lastSyncedWallTime = $wallNow;
        }

        $periods = self::all();
        if ($periods->isEmpty()) {
            return false;
        }

        $hasChanges = false;

        // 1. Selesaikan periode yang tanggal selesai sudah terlewat, dan jadwalkan yang belum mulai
        foreach ($periods as $p) {
            if ($p->tanggal_selesai && $now->gt($p->tanggal_selesai)) {
                if ($p->status !== 'selesai' && $p->status !== 'nonaktif') {
                    $p->update(['status' => 'selesai']);
                    $hasChanges = true;
                }
            } elseif ($p->tanggal_mulai && $now->lt($p->tanggal_mulai)) {
                if ($p->status !== 'terjadwal' && $p->status !== 'nonaktif') {
                    $p->update(['status' => 'terjadwal']);
                    $hasChanges = true;
                }
            }
        }

        // 2. Cari kandidat periode yang tanggalnya mencakup waktu saat ini (mulai <= now <= selesai)
        $activeCandidate = self::where(function ($q) use ($now) {
                $q->where(function ($sub) use ($now) {
                    $sub->whereNotNull('tanggal_mulai')
                        ->where('tanggal_mulai', '<=', $now);
                })->where(function ($sub) use ($now) {
                    $sub->whereNull('tanggal_selesai')
                        ->orWhere('tanggal_selesai', '>=', $now);
                });
            })
            ->where('status', '!=', 'nonaktif')
            ->orderByDesc('tanggal_mulai')
            ->first();

        $currentActive = self::where('status', 'aktif')->first();

        if ($activeCandidate) {
            if (!$currentActive || $currentActive->id !== $activeCandidate->id) {
                if ($currentActive) {
                    $currentActive->update(['status' => 'selesai']);
                }

                $activeCandidate->update(['status' => 'aktif']);
                $hasChanges = true;

                Setting::set('active_periode_changed_at', (string)$now->timestamp);
                Setting::set('active_periode_id', (string)$activeCandidate->id);
                Setting::set('active_periode_name', $activeCandidate->nama_periode);
                Guru::recalculateAll($activeCandidate->id);
                Guru::clearLeaderboardCache();
            }
        }

        return $hasChanges;
    }

    public static function getActivePeriode(): ?self
    {
        self::syncAllStatuses();

        $active = self::where('status', 'aktif')->first();
        if (!$active && self::count() > 0) {
            $active = self::latest('tanggal_selesai')->first() ?? self::latest('tanggal_mulai')->first();
        }
        return $active;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif'     => 'Aktif',
            'terjadwal' => 'Terjadwal',
            'selesai'   => 'Selesai',
            'nonaktif'  => 'Nonaktif',
            default     => ucfirst((string)$this->status),
        };
    }
}
