<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_name',
        'device_type',
        'platform',
        'browser',
        'status',
        'is_active',
        'login_at',
        'last_activity',
        'logout_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'login_at' => 'datetime',
        'last_activity' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function parseUserAgent(?string $userAgent): array
    {
        if (empty($userAgent)) {
            return [
                'device_name' => 'Perangkat Tidak Dikenal',
                'device_type' => 'Desktop',
                'platform' => 'Tidak Dikenal',
                'browser' => 'Tidak Dikenal',
            ];
        }

        // Detect Platform
        $platform = 'Lainnya';
        if (preg_match('/windows nt 10/i', $userAgent)) {
            $platform = 'Windows 10/11';
        } elseif (preg_match('/windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iphone/i', $userAgent)) {
            $platform = 'iPhone';
        } elseif (preg_match('/ipad/i', $userAgent)) {
            $platform = 'iPad';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        }

        // Detect Device Type
        $deviceType = 'Desktop';
        if (preg_match('/(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle|playbook|silk|(puffin(?!.*(IP|AP|WP))))/i', $userAgent)) {
            $deviceType = 'Tablet';
        } elseif (preg_match('/(mobi|ipod|phone|blackberry|opera mini|fennec|minimo|symbian|psp|nintendo ds)/i', $userAgent)) {
            $deviceType = 'Ponsel';
        }

        // Detect Browser
        $browser = 'Browser Web';
        if (preg_match('/edg/i', $userAgent)) {
            $browser = 'Microsoft Edge';
        } elseif (preg_match('/chrome|crios/i', $userAgent) && !preg_match('/opr|opera/i', $userAgent)) {
            $browser = 'Google Chrome';
        } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
            $browser = 'Mozilla Firefox';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome|crios|android/i', $userAgent)) {
            $browser = 'Apple Safari';
        } elseif (preg_match('/opr|opera/i', $userAgent)) {
            $browser = 'Opera';
        }

        $deviceName = "{$platform} ({$browser})";

        return [
            'device_name' => $deviceName,
            'device_type' => $deviceType,
            'platform' => $platform,
            'browser' => $browser,
        ];
    }

    public static function recordLogin($user, Request $request, ?string $sessionId = null): ?self
    {
        if (!$user) return null;

        try {
            $sessionId = $sessionId ?: $request->session()->getId();
            $userAgent = $request->userAgent() ?: 'Unknown';
            $parsed = self::parseUserAgent($userAgent);
            $ip = $request->ip() ?: '127.0.0.1';

            // Mark any previous active session with this exact session_id as logged out first
            if (Schema::hasTable('login_histories')) {
                return self::create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'device_name' => $parsed['device_name'],
                    'device_type' => $parsed['device_type'],
                    'platform' => $parsed['platform'],
                    'browser' => $parsed['browser'],
                    'status' => 'active',
                    'is_active' => true,
                    'login_at' => now(),
                    'last_activity' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Silently fail if table not yet migrated
        }

        return null;
    }

    public static function recordActivity($user, Request $request): void
    {
        if (!$user) return;

        try {
            if (!Schema::hasTable('login_histories')) return;

            $sessionId = $request->session()->getId();
            $history = self::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->where('is_active', true)
                ->first();

            if ($history) {
                $history->update([
                    'last_activity' => now(),
                    'ip_address' => $request->ip() ?: $history->ip_address,
                ]);
            } else {
                // If not tracked yet in this session, track now
                self::recordLogin($user, $request, $sessionId);
            }
        } catch (\Throwable $e) {
            // Silently fail
        }
    }

    public static function markLoggedOut(?string $sessionId, ?int $userId = null): void
    {
        if (empty($sessionId)) return;

        try {
            if (!Schema::hasTable('login_histories')) return;

            $query = self::where('session_id', $sessionId);
            if ($userId) {
                $query->where('user_id', $userId);
            }

            $query->update([
                'is_active' => false,
                'status' => 'logged_out',
                'logout_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently fail
        }
    }

    public static function getHistoriesForUser($user, ?string $currentSessionId = null): array
    {
        $currentSessionId = $currentSessionId ?: session()->getId();
        $histories = [];

        try {
            if (Schema::hasTable('login_histories')) {
                // Ensure current session is tracked
                if (request()) {
                    self::recordActivity($user, request());
                }

                // Check active sessions from Laravel's sessions table if driver is database
                $activeSessionIds = [];
                if (Schema::hasTable('sessions')) {
                    $activeSessionIds = DB::table('sessions')
                        ->where('user_id', $user->id)
                        ->pluck('id')
                        ->toArray();
                }

                $list = self::where('user_id', $user->id)
                    ->orderBy('last_activity', 'desc')
                    ->take(25)
                    ->get();

                foreach ($list as $item) {
                    $isCurrent = ($item->session_id === $currentSessionId);
                    $stillActive = $item->is_active;

                    // If sessions table exists and session_id is not in active sessions, mark logged_out
                    if (!empty($activeSessionIds) && !empty($item->session_id)) {
                        if (!in_array($item->session_id, $activeSessionIds) && !$isCurrent) {
                            $stillActive = false;
                        }
                    }

                    $item->is_current = $isCurrent;
                    $item->is_active_session = $stillActive;
                    $item->status_label = $isCurrent ? 'Sesi Ini (Sedang Aktif)' : ($stillActive ? 'Sedang Login' : 'Sudah Logout');
                    $histories[] = $item;
                }
            }
        } catch (\Throwable $e) {
            // Fallback: check sessions table directly
        }

        // If login_histories empty or table not ready, fallback to `sessions` table
        if (empty($histories) && Schema::hasTable('sessions')) {
            try {
                $sessions = DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->orderBy('last_activity', 'desc')
                    ->get();

                foreach ($sessions as $s) {
                    $parsed = self::parseUserAgent($s->user_agent);
                    $isCurrent = ($s->id === $currentSessionId);
                    $lastAct = \Carbon\Carbon::createFromTimestamp($s->last_activity);

                    $obj = new self([
                        'id' => 0,
                        'session_id' => $s->id,
                        'ip_address' => $s->ip_address,
                        'user_agent' => $s->user_agent,
                        'device_name' => $parsed['device_name'],
                        'device_type' => $parsed['device_type'],
                        'platform' => $parsed['platform'],
                        'browser' => $parsed['browser'],
                        'is_active' => true,
                        'login_at' => $lastAct,
                        'last_activity' => $lastAct,
                    ]);
                    $obj->is_current = $isCurrent;
                    $obj->is_active_session = true;
                    $obj->status_label = $isCurrent ? 'Sesi Ini (Sedang Aktif)' : 'Sedang Login';
                    $histories[] = $obj;
                }
            } catch (\Throwable $e) {}
        }

        return $histories;
    }

    public function getIconAttribute(): string
    {
        $platform = strtolower($this->platform ?? '');
        $deviceType = strtolower($this->device_type ?? '');

        if (str_contains($platform, 'windows')) {
            return 'bi-windows text-primary';
        } elseif (str_contains($platform, 'android')) {
            return 'bi-android2 text-success';
        } elseif (str_contains($platform, 'ios') || str_contains($platform, 'iphone') || str_contains($platform, 'ipad') || str_contains($platform, 'mac')) {
            return 'bi-apple text-dark';
        } elseif (str_contains($platform, 'linux')) {
            return 'bi-ubuntu text-danger';
        }

        if ($deviceType === 'ponsel' || $deviceType === 'mobile') {
            return 'bi-phone text-secondary';
        } elseif ($deviceType === 'tablet') {
            return 'bi-tablet text-secondary';
        }

        return 'bi-laptop text-primary';
    }
}
