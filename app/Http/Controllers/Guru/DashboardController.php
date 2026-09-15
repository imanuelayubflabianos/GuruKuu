<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil data guru yang login berdasarkan NIP / NIS di tabel users atau email
        $guru = Guru::where('nip', $user->nis)
            ->orWhere('email', $user->email)
            ->first();
        
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data profil Guru tidak ditemukan dalam sistem.');
        }

        $periodeAktif = Periode::where('status', 'aktif')->first();

        // Ambil ulasan & kritik saran dari siswa untuk guru ini (100% Anonim)
        $ulasanTerbaru = Penilaian::where('guru_id', $guru->id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($q) {
                    $q->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
            })
            ->with(['periode'])
            ->latest()
            ->get();

        return view('guru.dashboard', compact(
            'guru',
            'ulasanTerbaru',
            'periodeAktif'
        ));
    }

    public function replyPenilaian(Request $request, Penilaian $penilaian)
    {
        $request->validate([
            'balasan_guru' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)
            ->orWhere('email', $user->email)
            ->first();

        if (!$guru || $penilaian->guru_id !== $guru->id) {
            return back()->with('error', 'Anda hanya dapat membalas ulasan yang ditujukan untuk profil Anda.');
        }

        $penilaian->update([
            'balasan_guru' => trim($request->balasan_guru),
            'balasan_guru_at' => now(),
        ]);

        return back()->with('success', 'Balasan ulasan berhasil disimpan dan ditampilkan!');
    }

    public function leaderboard()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        
        $leaderboard = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->get();

        // Fallback jika belum ada penilaian
        if ($leaderboard->isEmpty()) {
            $leaderboard = Guru::with('jurusan')
                ->orderBy('nama', 'asc')
                ->get();
        }

        return view('guru.leaderboard', compact(
            'periodeAktif',
            'leaderboard'
        ));
    }
}