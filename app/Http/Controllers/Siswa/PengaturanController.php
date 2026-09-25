<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $kelasAktif = $periodeAktif ? $user->kelas()->wherePivot('tahun_ajaran', $periodeAktif->tahun_ajaran)->first() : null;

        $pesan = Kontak::where('identifier', $user->nis)
            ->where('is_siswa', true)
            ->orderBy('created_at', 'asc')
            ->get();

        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        session(['siswa_chat_captcha' => $num1 + $num2]);

        return view('siswa.pengaturan.index', compact('user', 'kelasAktif', 'pesan', 'num1', 'num2'));
    }

    public function kirimPesanAdmin(Request $request)
    {
        $request->validate([
            'pesan'   => 'required|string|min:3|max:1000',
            'captcha' => 'required|numeric',
        ], [
            'pesan.required'   => 'Pesan tidak boleh kosong.',
            'captcha.required' => 'Verifikasi wajib diisi.',
            'captcha.numeric'  => 'Jawaban verifikasi harus berupa angka.',
        ]);

        if ($request->captcha != session('siswa_chat_captcha')) {
            return redirect()->to(route('siswa.pengaturan') . '#tabChat')->withInput()->withErrors(['captcha' => 'Jawaban verifikasi tidak cocok. Silakan coba lagi.']);
        }

        $user = Auth::user();

        Kontak::create([
            'pengirim'   => $user->name,
            'identifier' => $user->nis,
            'pesan'      => trim($request->pesan),
            'is_siswa'   => true,
        ]);

        return redirect()->to(route('siswa.pengaturan') . '#tabChat')->with('success', 'Pesan Anda berhasil dikirim ke Admin!');
    }

    public function gantiPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password akun Anda berhasil diperbarui!');
    }
}
