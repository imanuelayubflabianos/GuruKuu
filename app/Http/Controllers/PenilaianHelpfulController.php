<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\PenilaianHelpful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianHelpfulController extends Controller
{
    /**
     * Toggle "Apakah ulasan ini berguna?" (Thumbs up / Like ala Play Store)
     */
    public function toggle(Request $request, Penilaian $penilaian)
    {
        $user = Auth::user();
        $ip = $request->ip();

        if ($user) {
            $existing = PenilaianHelpful::where('penilaian_id', $penilaian->id)
                ->where('user_id', $user->id)
                ->first();
        } else {
            $existing = PenilaianHelpful::where('penilaian_id', $penilaian->id)
                ->whereNull('user_id')
                ->where('ip_address', $ip)
                ->first();
        }

        $helpful = false;
        if ($existing) {
            // Batalkan reaksi (unvote)
            $existing->delete();
            $helpful = false;
        } else {
            // Berikan reaksi berguna (vote)
            PenilaianHelpful::create([
                'penilaian_id' => $penilaian->id,
                'user_id' => $user ? $user->id : null,
                'ip_address' => $ip,
            ]);
            $helpful = true;
        }

        // Hitung ulang total helpful
        $newCount = PenilaianHelpful::where('penilaian_id', $penilaian->id)->count();
        $penilaian->update(['helpful_count' => $newCount]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'helpful' => $helpful,
                'count' => $newCount,
                'message' => $helpful ? 'Terima kasih, tanggapan Anda telah dicatat!' : 'Tanggapan telah dibatalkan.',
            ]);
        }

        return back()->with('success', $helpful ? 'Terima kasih atas tanggapan Anda.' : 'Tanggapan dibatalkan.');
    }
}
