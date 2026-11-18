<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PengaturanWakilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'wakil') {
            return redirect()->route('admin.pengaturan.index');
        }

        $pesan = Kontak::where('identifier', $user->nis)
            ->orWhere('identifier', 'wakil')
            ->orderBy('created_at', 'asc')
            ->get();

        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        session(['wakil_chat_captcha' => $num1 + $num2]);

        return view('admin.pengaturan.wakil', compact('user', 'pesan', 'num1', 'num2'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak cocok.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password akun Wakil berhasil diperbarui.');
    }

    public function kirimPesan(Request $request)
    {
        $request->validate([
            'pesan' => 'required|string|min:3|max:255',
            'captcha' => 'required|numeric',
        ], [
            'pesan.required' => 'Pesan tidak boleh kosong.',
            'pesan.max' => 'Pesan maksimal 255 karakter.',
            'captcha.required' => 'Verifikasi captcha wajib diisi.',
        ]);

        if ($request->captcha != session('wakil_chat_captcha')) {
            return redirect()->to(route('admin.pengaturan.wakil') . '#tabChatWakil')->withInput()->withErrors(['captcha' => 'Jawaban verifikasi captcha tidak cocok.']);
        }

        $pesanTeks = trim($request->pesan);
        if (\App\Services\ProfanityFilterService::containsLink($pesanTeks)) {
            return redirect()->to(route('admin.pengaturan.wakil') . '#tabChatWakil')->withInput()->withErrors(['pesan' => 'Pesan tidak boleh mengandung link luar.']);
        }

        $user = Auth::user();
        Kontak::create([
            'pengirim' => $user->name ?? 'Wakil',
            'identifier' => $user->nis ?: 'wakil',
            'pesan' => $pesanTeks,
            'is_siswa' => false,
            'is_read' => false,
            'is_replied' => false,
        ]);

        return redirect()->to(route('admin.pengaturan.wakil') . '#tabChatWakil')->with('success', 'Pesan berhasil dikirim ke Administrator.');
    }
}

