<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Cache memori runtime (dalam 1 siklus request PHP) untuk kecepatan maksimal 0ms
     */
    protected static ?array $runtimeCache = null;

    /**
     * Ambil semua settings dalam satu mapping memory & cache terpusat
     */
    public static function allCached(): array
    {
        if (self::$runtimeCache !== null) {
            return self::$runtimeCache;
        }

        try {
            self::$runtimeCache = Cache::remember('gurukuu_all_settings', 3600, function () {
                return self::pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            self::$runtimeCache = [];
        }

        return self::$runtimeCache ?? [];
    }

    /**
     * Ambil nilai setting berdasarkan key (dengan in-memory fallback dan cache)
     */
    public static function get(string $key, $default = null)
    {
        $all = self::allCached();
        if (array_key_exists($key, $all) && $all[$key] !== null && trim((string)$all[$key]) !== '') {
            return $all[$key];
        }

        return $default;
    }

    /**
     * Simpan atau update nilai setting berdasarkan key
     */
    public static function set(string $key, $value): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        self::$runtimeCache = null;
        try {
            Cache::forget('gurukuu_all_settings');
            Cache::forget("setting_{$key}");
            Cache::flush();
        } catch (\Throwable $e) {}

        return $setting;
    }

    /**
     * Bersihkan cache settings secara menyeluruh
     */
    public static function clearCache(): void
    {
        self::$runtimeCache = null;
        try {
            Cache::forget('gurukuu_all_settings');
            Cache::flush();
        } catch (\Throwable $e) {}
    }

    /**
     * Bersihkan seluruh cache settings dan view cache
     */
    public static function clearAllCache(): void
    {
        self::$runtimeCache = null;
        try {
            Cache::forget('gurukuu_all_settings');
            Cache::flush();
            \Illuminate\Support\Facades\Artisan::call('view:clear');
        } catch (\Throwable $e) {}
    }
}
