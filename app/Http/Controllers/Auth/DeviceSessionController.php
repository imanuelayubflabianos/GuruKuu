<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeviceSessionController extends Controller
{
    public function logoutDevice(Request $request, $id)
    {
        $user = Auth::user();
        $currentSessionId = $request->session()->getId();

        $history = null;
        if (Schema::hasTable('login_histories')) {
            $history = LoginHistory::where('id', $id)->where('user_id', $user->id)->first();
        }

        // If not found by ID, might be session_id string passed
        if (!$history && Schema::hasTable('login_histories')) {
            $history = LoginHistory::where('session_id', $id)->where('user_id', $user->id)->first();
        }

        $sessionId = $history ? $history->session_id : $id;
        $deviceName = $history ? $history->device_name : 'Perangkat';

        // Delete from sessions table
        if ($sessionId && Schema::hasTable('sessions')) {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', $sessionId)
                ->delete();
        }

        // Update login history
        if ($history) {
            $history->update([
                'is_active' => false,
                'status' => 'logged_out',
                'logout_at' => now(),
            ]);
        } elseif (Schema::hasTable('login_histories') && $sessionId) {
            LoginHistory::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->update([
                    'is_active' => false,
                    'status' => 'logged_out',
                    'logout_at' => now(),
                ]);
        }

        // If user logged out their current device
        if ($sessionId === $currentSessionId) {
            Auth::guard('web')->logout();
            $request->session()->forget('url.intended');
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $recaller = Auth::getRecallerName();
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget($recaller));
            return redirect()->route('login')->with('info', 'Anda telah keluar dari perangkat ini.');
        }

        return back()->with('success', "Akses untuk perangkat {$deviceName} berhasil dikeluarkan.");
    }

    public function logoutOthers(Request $request)
    {
        $user = Auth::user();
        $currentSessionId = $request->session()->getId();

        // Delete other sessions from sessions table
        if (Schema::hasTable('sessions')) {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentSessionId)
                ->delete();
        }

        // Update other records in login_histories
        if (Schema::hasTable('login_histories')) {
            LoginHistory::where('user_id', $user->id)
                ->where('session_id', '!=', $currentSessionId)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'status' => 'logged_out',
                    'logout_at' => now(),
                ]);
        }

        return back()->with('success', 'Seluruh sesi di perangkat lain telah berhasil dikeluarkan.');
    }

    public function logoutAll(Request $request)
    {
        $user = Auth::user();

        // Delete all sessions from sessions table
        if (Schema::hasTable('sessions')) {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }

        // Update all records in login_histories
        if (Schema::hasTable('login_histories')) {
            LoginHistory::where('user_id', $user->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'status' => 'logged_out',
                    'logout_at' => now(),
                ]);
        }

        Auth::guard('web')->logout();
        $request->session()->forget('url.intended');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $recaller = Auth::getRecallerName();
        \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget($recaller));

        return redirect()->route('login')->with('info', 'Seluruh perangkat berhasil dikeluarkan. Silakan login kembali.');
    }

    public function clearHistory(Request $request)
    {
        $user = Auth::user();
        $currentSessionId = $request->session()->getId();

        if (Schema::hasTable('login_histories')) {
            $activeSessionIds = [];
            if (Schema::hasTable('sessions')) {
                $activeSessionIds = DB::table('sessions')
                    ->where('user_id', $user->id)
                    ->pluck('id')
                    ->toArray();
            }

            // Hapus riwayat yang sudah logout / tidak aktif
            LoginHistory::where('user_id', $user->id)
                ->where(function ($q) use ($currentSessionId, $activeSessionIds) {
                    $q->where('is_active', false)
                      ->orWhere('status', 'logged_out')
                      ->orWhere(function ($sub) use ($currentSessionId, $activeSessionIds) {
                          $sub->where('session_id', '!=', $currentSessionId);
                          if (!empty($activeSessionIds)) {
                              $sub->whereNotIn('session_id', $activeSessionIds);
                          }
                      });
                })
                ->delete();
        }

        return back()->with('success', 'Riwayat sesi perangkat yang sudah keluar berhasil dibersihkan.');
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        if (Schema::hasTable('login_histories')) {
            LoginHistory::where('id', $id)
                ->where('user_id', $user->id)
                ->delete();
        }

        return back()->with('success', 'Catatan riwayat perangkat berhasil dihapus.');
    }
}
