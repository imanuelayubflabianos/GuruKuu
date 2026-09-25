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
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
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

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Seluruh perangkat berhasil dikeluarkan. Silakan login kembali.');
    }
}
