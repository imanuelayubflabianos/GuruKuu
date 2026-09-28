<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)->orWhere('email', $user->email)->first();

        $identifier = $guru?->nip ?? $user->nis;
        $pesan = Kontak::where('identifier', $identifier)
            ->where('is_siswa', false)
            ->orderBy('created_at', 'asc')
            ->get();

        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        session(['guru_chat_captcha' => $num1 + $num2]);

        $defaultBio = 'Guru pengajar di SMK Negeri 1 Bangsri yang berdedikasi membimbing dan mendidik generasi muda berprestasi.';

        return view('guru.pengaturan.index', compact('user', 'guru', 'pesan', 'defaultBio', 'num1', 'num2'));
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)->orWhere('email', $user->email)->first();

        if (!$guru) {
            return back()->with('error', 'Data profil guru tidak ditemukan.');
        }

        $request->validate([
            'bio'   => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'bio.max' => 'Deskripsi diri maksimal 100 karakter.',
        ]);

        $defaultBio = 'Guru pengajar di SMK Negeri 1 Bangsri yang berdedikasi membimbing generasi muda.';
        $bio = trim((string) $request->bio);
        if (empty($bio)) {
            $bio = $guru->bio ?: $defaultBio;
        }

        $guruData = [
            'bio' => $bio,
        ];
        $userData = [];

        if ($request->hasFile('photo')) {
            if ($guru->photo && Storage::disk('public')->exists($guru->photo)) {
                Storage::disk('public')->delete($guru->photo);
            }
            $path = $request->file('photo')->store('guru', 'public');
            $guruData['photo'] = $path;
            $userData['photo'] = $path;
        }

        $guru->update($guruData);
        if (!empty($userData)) {
            $user->update($userData);
        }

        return back()->with('success', 'Foto profil dan deskripsi diri berhasil diperbarui!');
    }

    public function kirimPesanAdmin(Request $request)
    {
        $request->validate([
            'pesan'   => 'required|string|min:3|max:100',
            'captcha' => 'required|numeric',
        ], [
            'pesan.required'   => 'Pesan tidak boleh kosong.',
            'pesan.max'        => 'Pesan Anda melebihi batas maksimal 100 karakter.',
            'captcha.required' => 'Verifikasi wajib diisi.',
            'captcha.numeric'  => 'Jawaban verifikasi harus berupa angka.',
        ]);

        if ($request->captcha != session('guru_chat_captcha')) {
            return redirect()->to(route('guru.pengaturan') . '#tabChat')->withInput()->withErrors(['captcha' => 'Jawaban verifikasi tidak cocok. Silakan coba lagi.']);
        }

        $pesanTeks = trim($request->pesan);
        if (\App\Services\ProfanityFilterService::containsLink($pesanTeks)) {
            return redirect()->to(route('guru.pengaturan') . '#tabChat')->withInput()->withErrors(['pesan' => 'Demi keamanan, pesan tidak boleh mengandung tautan / link URL luar.']);
        }

        $profanity = \App\Services\ProfanityFilterService::check($pesanTeks);
        if (!$profanity['clean']) {
            return redirect()->to(route('guru.pengaturan') . '#tabChat')->withInput()->withErrors(['pesan' => $profanity['message']]);
        }

        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)->orWhere('email', $user->email)->first();

        Kontak::create([
            'pengirim'   => $guru?->nama ?? $user->name,
            'identifier' => $guru?->nip ?? $user->nis,
            'pesan'      => $pesanTeks,
            'is_siswa'   => false,
        ]);

        return redirect()->to(route('guru.pengaturan') . '#tabChat')->with('success', 'Pesan Anda berhasil dikirimkan ke Admin!');
    }

    public function editPesanAdmin(Request $request, Kontak $kontak)
    {
        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)->orWhere('email', $user->email)->first();
        $identifier = $guru?->nip ?? $user->nis;

        if ($kontak->identifier !== $identifier || $kontak->is_siswa) {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate(['pesan' => 'required|string|min:3|max:100'], [
            'pesan.max' => 'Pesan Anda melebihi batas maksimal 100 karakter.'
        ]);

        $pesanTeks = trim($request->pesan);
        if (\App\Services\ProfanityFilterService::containsLink($pesanTeks)) {
            return redirect()->to(route('guru.pengaturan') . '#tabChat')->withInput()->withErrors(['pesan' => 'Demi keamanan, pesan tidak boleh mengandung tautan / link URL luar.']);
        }

        $profanity = \App\Services\ProfanityFilterService::check($pesanTeks);
        if (!$profanity['clean']) {
            return redirect()->to(route('guru.pengaturan') . '#tabChat')->withInput()->withErrors(['pesan' => $profanity['message']]);
        }

        $kontak->update(['pesan' => $pesanTeks]);

        return redirect()->to(route('guru.pengaturan') . '#tabChat')->with('success', 'Pesan Anda berhasil diperbarui!');
    }

    public function hapusPesanAdmin(Kontak $kontak)
    {
        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)->orWhere('email', $user->email)->first();
        $identifier = $guru?->nip ?? $user->nis;

        if ($kontak->identifier !== $identifier || $kontak->is_siswa) {
            return back()->with('error', 'Akses ditolak.');
        }

        $kontak->update(['pesan' => '[Pesan Dihapus]']);

        return redirect()->to(route('guru.pengaturan') . '#tabChat')->with('success', 'Pesan berhasil dihapus.');
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
