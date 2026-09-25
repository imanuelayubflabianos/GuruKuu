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
            'nama'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:guru,email,' . $guru->id,
            'phone' => 'nullable|string|max:50',
            'bio'   => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $defaultBio = 'Guru pengajar di SMK Negeri 1 Bangsri yang berdedikasi membimbing dan mendidik generasi muda berprestasi.';
        $bio = trim($request->bio);
        if (empty($bio)) {
            $bio = $guru->bio ?: $defaultBio;
        }

        $guruData = [
            'nama'  => trim($request->nama),
            'email' => $request->filled('email') ? trim($request->email) : null,
            'phone' => $request->filled('phone') ? trim($request->phone) : null,
            'bio'   => $bio,
        ];

        $userData = [
            'name'  => trim($request->nama),
            'email' => $request->filled('email') ? trim($request->email) : $user->email,
        ];

        if ($request->hasFile('photo')) {
            if ($guru->photo && Storage::disk('public')->exists($guru->photo)) {
                Storage::disk('public')->delete($guru->photo);
            }
            $path = $request->file('photo')->store('guru', 'public');
            $guruData['photo'] = $path;
            $userData['photo'] = $path;
        }

        $guru->update($guruData);
        $user->update($userData);

        return back()->with('success', 'Profil dan informasi guru berhasil diperbarui!');
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

        if ($request->captcha != session('guru_chat_captcha')) {
            return redirect()->to(route('guru.pengaturan') . '#tabChat')->withInput()->withErrors(['captcha' => 'Jawaban verifikasi tidak cocok. Silakan coba lagi.']);
        }

        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)->orWhere('email', $user->email)->first();

        Kontak::create([
            'pengirim'   => $guru?->nama ?? $user->name,
            'identifier' => $guru?->nip ?? $user->nis,
            'pesan'      => trim($request->pesan),
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

        $request->validate(['pesan' => 'required|string|min:3|max:1000']);
        $kontak->update(['pesan' => trim($request->pesan)]);

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
