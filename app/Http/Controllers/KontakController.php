<?php

namespace App\Http\Controllers;

use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class KontakController extends Controller
{
    private function getOrCreateDeviceId(Request $request)
    {
        $deviceId = $request->cookie('guest_device_id') ?: session('guest_device_id') ?: $request->input('_device_id');
        if (!$deviceId) $deviceId = 'guest_' . Str::random(16);
        session(['guest_device_id' => $deviceId]);
        Cookie::queue('guest_device_id', $deviceId, 43200);
        return $deviceId;
    }

    // ==================== ADMIN ====================
    public function index() { return view('admin.kontak.index', ['pesan' => Kontak::latest()->get()]); }

    public function reply(Request $request, Kontak $kontak)
    {
        $request->validate(['balasan' => 'required|min:5']);
        $kontak->update(['balasan' => $request->balasan, 'is_replied' => true, 'is_read' => true]);
        return back()->with('success', 'Pesan berhasil dibalas!');
    }

    // ✅ ADMIN EDIT BALASAN
    public function editReply(Request $request, Kontak $kontak)
    {
        $request->validate(['balasan' => 'required|min:5']);
        $kontak->update(['balasan' => $request->balasan]);
        return back()->with('success', 'Balasan berhasil diperbarui!');
    }

    public function destroyReply(Kontak $kontak)
    {
        $kontak->update(['balasan' => null, 'is_replied' => false]);
        return back()->with('success', 'Balasan admin berhasil dihapus.');
    }

    public function destroy(Kontak $kontak)
    {
        $kontak->delete();
        return back()->with('success', 'Seluruh percakapan dihapus.');
    }

    // ==================== GUEST (PUBLIK) ====================
    public function guestPage(Request $request)
    {
        $deviceId = $this->getOrCreateDeviceId($request);
        $riwayat = Kontak::where('identifier', $deviceId)->where('is_siswa', false)->orderBy('created_at', 'asc')->get();
        $num1 = rand(1, 9); $num2 = rand(1, 9);
        session(['captcha_answer' => $num1 + $num2]);
        return view('kontak.guest-page', compact('riwayat', 'deviceId', 'num1', 'num2'));
    }

    public function storeGuest(Request $request)
    {
        $request->validate(['pesan' => 'required|min:10', 'captcha' => 'required|numeric']);
        if ($request->captcha != session('captcha_answer')) return back()->withErrors(['captcha' => 'Jawaban salah.'])->withInput();
        
        Kontak::create([
            'pengirim' => 'Tamu', 'identifier' => $this->getOrCreateDeviceId($request),
            'pesan' => $request->pesan, 'is_siswa' => false
        ]);
        return redirect()->route('kontak.guest.page')->with('success', 'Pesan terkirim!');
    }

    // ✅ GUEST EDIT PESAN
    public function editGuest(Request $request, Kontak $kontak)
    {
        $deviceId = $this->getOrCreateDeviceId($request);
        if ($kontak->identifier !== $deviceId || $kontak->is_siswa) {
            return back()->with('error', 'Akses ditolak.');
        }
        $request->validate(['pesan' => 'required|min:10']);
        $kontak->update(['pesan' => $request->pesan]);
        return back()->with('success', 'Pesan berhasil diperbarui!');
    }

    // ✅ GUEST HAPUS PESAN (Jadi "[Pesan Dihapus]")
    public function destroyGuestMessage(Request $request, Kontak $kontak)
    {
        $deviceId = $this->getOrCreateDeviceId($request);
        if ($kontak->identifier !== $deviceId || $kontak->is_siswa) {
            return back()->with('error', 'Akses ditolak.');
        }
        $kontak->update(['pesan' => '[Pesan Dihapus]']);
        return back()->with('success', 'Pesan Anda dihapus.');
    }

    // ==================== SISWA ====================
    public function siswaIndex()
    {
        $user = Auth::user();
        $pesan = Kontak::where('identifier', $user->nis)->where('is_siswa', true)->orderBy('created_at', 'asc')->get();
        $num1 = rand(1, 9); $num2 = rand(1, 9);
        session(['siswa_chat_captcha' => $num1 + $num2]);
        return view('siswa.kontak.index', compact('pesan', 'num1', 'num2'));
    }

    public function storeSiswa(Request $request)
    {
        $request->validate(['pesan' => 'required|min:10', 'captcha' => 'required|numeric']);
        if ($request->captcha != session('siswa_chat_captcha')) {
            return back()->withErrors(['captcha' => 'Jawaban verifikasi matematika salah.'])->withInput();
        }
        $user = Auth::user();
        Kontak::create([
            'pengirim' => $user->name, 'identifier' => $user->nis,
            'pesan' => $request->pesan, 'is_siswa' => true,
        ]);
        return back()->with('success', 'Pesan berhasil dikirim ke Admin!');
    }

    public function editSiswa(Request $request, Kontak $kontak)
    {
        if ($kontak->identifier !== Auth::user()->nis) return back()->with('error', 'Akses ditolak.');
        $request->validate(['pesan' => 'required|min:10']);
        $kontak->update(['pesan' => $request->pesan]);
        return back()->with('success', 'Pesan berhasil diperbarui!');
    }

    public function destroySiswaMessage(Kontak $kontak)
    {
        if ($kontak->identifier !== Auth::user()->nis) return back()->with('error', 'Akses ditolak.');
        $kontak->update(['pesan' => '[Pesan Dihapus]']);
        return back()->with('success', 'Pesan Anda dihapus.');
    }
}