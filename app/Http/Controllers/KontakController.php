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
        $deviceId = $this->getOrCreateDeviceId($request);

        // 🛡️ Honeypot bot detection
        if ($request->filled('website_hp')) {
            return back()->withErrors(['pesan' => 'Aktivitas mencurigakan terdeteksi.'])->withInput();
        }

        // 🛡️ Rate limiting: Maksimal 5 pengiriman dalam 5 menit per IP
        $throttleKey = 'guest_contact:' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'pesan' => "Terlalu banyak pesan terkirim. Mohon tunggu {$seconds} detik sebelum mengirim pesan kembali demi kenyamanan bersama."
            ])->withInput();
        }

        $request->validate(
            ['pesan' => 'required|min:5|max:255', 'captcha' => 'required|numeric'],
            ['pesan.max' => 'Pesan Anda melebihi batas maksimal 255 karakter.']
        );
        if ($request->captcha != session('captcha_answer')) return back()->withErrors(['captcha' => 'Jawaban salah.'])->withInput();

        $pesanBersih = strip_tags(trim($request->pesan));

        if (\App\Services\ProfanityFilterService::containsLink($pesanBersih)) {
            return back()->withInput()->with('error', 'Demi keamanan, pengiriman pesan tidak boleh mengandung tautan / link URL luar.');
        }

        $profanity = \App\Services\ProfanityFilterService::check($pesanBersih);
        if (!$profanity['clean']) {
            try {
                \App\Models\Pelanggaran::create([
                    'user_id' => null,
                    'tipe' => 'kontak_toxic',
                    'kata_terdeteksi' => $profanity['detected'],
                    'isi_teks' => $pesanBersih,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'is_read' => false,
                    'tindakan' => 'diblokir_otomatis',
                ]);
            } catch (\Throwable $e) {}

            return back()->with('violation_popup', $profanity['message']);
        }
        
        $cleanId = preg_replace('/[^a-zA-Z0-9]/', '', (string)$deviceId);
        $code = strtoupper(substr($cleanId, -4));
        $namaTamu = 'Tamu #' . ($code ?: rand(1000, 9999));

        Kontak::create([
            'pengirim' => $namaTamu, 
            'identifier' => $deviceId,
            'pesan' => $pesanBersih, 
            'is_siswa' => false
        ]);

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 300);

        return redirect()->route('kontak.guest.page')->with('success', 'Pesan terkirim!');
    }

    public function editGuest(Request $request, Kontak $kontak)
    {
        $deviceId = $this->getOrCreateDeviceId($request);
        if ($kontak->identifier !== $deviceId || $kontak->is_siswa) {
            return back()->with('error', 'Akses ditolak.');
        }
        $request->validate(
            ['pesan' => 'required|min:5|max:255'],
            ['pesan.max' => 'Pesan Anda melebihi batas maksimal 255 karakter.']
        );

        $pesanBaru = strip_tags(trim($request->pesan));
        if (\App\Services\ProfanityFilterService::containsLink($pesanBaru)) {
            return back()->withInput()->with('error', 'Demi keamanan, pengiriman pesan tidak boleh mengandung tautan / link URL luar.');
        }

        $profanity = \App\Services\ProfanityFilterService::check($pesanBaru);
        if (!$profanity['clean']) {
            return back()->withInput()->with('error', $profanity['message']);
        }

        $kontak->update(['pesan' => $pesanBaru]);
        return back()->with('success', 'Pesan berhasil diperbarui!');
    }

    public function destroyGuestMessage(Request $request, Kontak $kontak)
    {
        $deviceId = $this->getOrCreateDeviceId($request);
        if ($kontak->identifier !== $deviceId || $kontak->is_siswa) {
            return back()->with('error', 'Akses ditolak.');
        }
        $kontak->update(['pesan' => '[Pesan Dihapus]']);
        return back()->with('success', 'Pesan Anda berhasil dihapus.');
    }

    public function streamGuest(Request $request)
    {
        $deviceId = $this->getOrCreateDeviceId($request);
        $riwayat = Kontak::where('identifier', $deviceId)->where('is_siswa', false)->orderBy('created_at', 'asc')->get();
        return response()->json([
            'count' => $riwayat->count(),
            'last_update' => $riwayat->max('updated_at')?->timestamp ?? 0,
            'messages' => $riwayat->map(function($c) {
                return [
                    'id' => $c->id,
                    'pesan' => $c->pesan,
                    'balasan' => $c->balasan,
                    'is_replied' => (bool)$c->is_replied,
                    'time' => $c->created_at->format('H:i'),
                    'reply_time' => $c->updated_at->format('H:i'),
                ];
            }),
        ]);
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
        // 🛡️ Rate limiting: Maksimal 10 pesan per menit per siswa
        $throttleKey = 'siswa_chat:' . Auth::id();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'pesan' => "Pengiriman pesan terlalu cepat. Tunggu {$seconds} detik sebelum mengirim kembali."
            ])->withInput();
        }

        $request->validate(
            ['pesan' => 'required|min:3|max:255', 'captcha' => 'required|numeric'],
            ['pesan.max' => 'Pesan Anda melebihi batas maksimal 255 karakter.']
        );
        if ($request->captcha != session('siswa_chat_captcha')) {
            return back()->withErrors(['captcha' => 'Jawaban verifikasi matematika salah.'])->withInput();
        }

        $pesanBersih = strip_tags(trim($request->pesan));

        if (\App\Services\ProfanityFilterService::containsLink($pesanBersih)) {
            return back()->withInput()->with('error', 'Demi keamanan, pesan tidak boleh mengandung tautan / link URL luar.');
        }

        $profanity = \App\Services\ProfanityFilterService::check($pesanBersih);
        if (!$profanity['clean']) {
            try {
                \App\Models\Pelanggaran::create([
                    'user_id' => Auth::id(),
                    'tipe' => 'kontak_toxic',
                    'kata_terdeteksi' => $profanity['detected'],
                    'isi_teks' => $pesanBersih,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'is_read' => false,
                    'tindakan' => 'diblokir_otomatis',
                ]);
            } catch (\Throwable $e) {}

            return back()->with('violation_popup', $profanity['message']);
        }

        $user = Auth::user();
        Kontak::create([
            'pengirim' => $user->name, 
            'identifier' => $user->nis,
            'pesan' => $pesanBersih, 
            'is_siswa' => true,
        ]);

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        return back()->with('success', 'Pesan berhasil dikirim ke Admin!');
    }

    public function editSiswa(Request $request, Kontak $kontak)
    {
        if ($kontak->identifier !== Auth::user()->nis) return back()->with('error', 'Akses ditolak.');
        $request->validate(
            ['pesan' => 'required|min:3|max:255'],
            ['pesan.max' => 'Pesan Anda melebihi batas maksimal 255 karakter.']
        );
        $pesanBaru = strip_tags(trim($request->pesan));
        if (\App\Services\ProfanityFilterService::containsLink($pesanBaru)) {
            return back()->withInput()->with('error', 'Demi keamanan, pesan tidak boleh mengandung tautan / link URL luar.');
        }
        $kontak->update(['pesan' => $pesanBaru]);
        return back()->with('success', 'Pesan berhasil diperbarui!');
    }

    public function siswaDestroy(Kontak $kontak)
    {
        if ($kontak->identifier !== Auth::user()->nis) return back()->with('error', 'Akses ditolak.');
        $kontak->update(['pesan' => '[Pesan Dihapus]']);
        return back()->with('success', 'Pesan berhasil dihapus.');
    }

    public function destroySiswaMessage(Kontak $kontak)
    {
        if ($kontak->identifier !== Auth::user()->nis) return back()->with('error', 'Akses ditolak.');
        $kontak->update(['pesan' => '[Pesan Dihapus]']);
        return back()->with('success', 'Pesan Anda berhasil dihapus.');
    }

    public function streamSiswa()
    {
        $user = Auth::user();
        $pesan = Kontak::where('identifier', $user->nis)->where('is_siswa', true)->orderBy('created_at', 'asc')->get();
        return response()->json([
            'count' => $pesan->count(),
            'last_update' => $pesan->max('updated_at')?->timestamp ?? 0,
            'messages' => $pesan->map(function($c) {
                return [
                    'id' => $c->id,
                    'pesan' => $c->pesan,
                    'balasan' => $c->balasan,
                    'is_replied' => (bool)$c->is_replied,
                    'time' => $c->created_at->format('H:i'),
                    'reply_time' => $c->updated_at->format('H:i'),
                ];
            }),
        ]);
    }
}