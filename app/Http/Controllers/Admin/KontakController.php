<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use App\Models\User;
use App\Models\Guru;
use App\Services\ProfanityFilterService;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function index(Request $request)
    {
        $kontak = Kontak::latest()->paginate(15)->withQueryString();
        return view('admin.kontak.index', compact('kontak'));
    }

    public function reply(Request $request, Kontak $kontak)
    {
        $request->validate(['balasan' => 'required|string|max:255'], ['balasan.max' => 'Balasan administrator melebihi batas maksimal 255 karakter.']);

        if (ProfanityFilterService::containsLink($request->balasan)) {
            return back()->withInput()->with('error', 'Balasan tidak boleh mengandung tautan / link URL luar demi keamanan.');
        }

        $kontak->update([
            'balasan' => $request->balasan,
            'is_replied' => true,
            'is_read' => true,
        ]);
        return back()->with('success', 'Balasan berhasil dikirim!');
    }

    public function editReply(Request $request, Kontak $kontak)
    {
        $request->validate(['balasan' => 'required|string|max:255'], ['balasan.max' => 'Balasan administrator melebihi batas maksimal 255 karakter.']);

        if (ProfanityFilterService::containsLink($request->balasan)) {
            return back()->withInput()->with('error', 'Balasan tidak boleh mengandung tautan / link URL luar demi keamanan.');
        }

        $kontak->update(['balasan' => $request->balasan]);
        return back()->with('success', 'Balasan berhasil diperbarui!');
    }

    public function destroyReply(Kontak $kontak)
    {
        if ($kontak->pesan === null) {
            $kontak->delete();
        } else {
            $kontak->update([
                'balasan' => null,
                'is_replied' => false
            ]);
        }
        return back()->with('success', 'Balasan berhasil dihapus.');
    }

    public function destroy(Kontak $kontak)
    {
        $kontak->delete();
        return back()->with('success', 'Pesan berhasil dihapus.');
    }

    /**
     * 💬 Buka halaman riwayat chat percakapan dengan pengguna tertentu
     */
    public function chat(string $identifier)
    {
        $riwayat = Kontak::where('identifier', $identifier)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($riwayat->isEmpty()) {
            return redirect()->route('admin.kontak.index')->with('error', 'Percakapan chat tidak ditemukan.');
        }

        // Tandai seluruh pesan dari pengguna ini sebagai telah dibaca oleh admin
        Kontak::where('identifier', $identifier)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $first = $riwayat->first();
        $senderName = $first->display_pengirim;
        $roleLabel = 'Tamu / Pengguna';
        $userObj = null;

        if ($first->is_siswa) {
            $userObj = User::where('nis', $identifier)->first();
            $senderName = $userObj?->name ?? $first->pengirim;
            $roleLabel = 'Siswa (NIS: ' . $identifier . ')';
        } else {
            $guru = Guru::where('nip', $identifier)->orWhere('email', $identifier)->first();
            if ($guru) {
                $senderName = $guru->nama;
                $roleLabel = 'Guru (NIP: ' . ($guru->nip ?: '-') . ')';
            } else {
                $cleanId = preg_replace('/[^a-zA-Z0-9]/', '', (string)$identifier);
                $code = strtoupper(substr($cleanId, -4));
                $senderName = 'Tamu #' . ($code ?: $first->id);
                $roleLabel = 'Tamu / Pengguna (#' . ($code ?: $first->id) . ')';
            }
        }

        return view('admin.kontak.chat', compact('identifier', 'riwayat', 'senderName', 'roleLabel', 'userObj', 'first'));
    }

    /**
     * 💬 Kirim balasan pesan pada room chat pengguna tertentu
     */
    public function sendChatMessage(Request $request, string $identifier)
    {
        $request->validate([
            'balasan' => 'required|string|min:1|max:255',
        ], [
            'balasan.required' => 'Isi balasan tidak boleh kosong.',
            'balasan.max' => 'Balasan melebihi batas maksimal 255 karakter.',
        ]);

        if (ProfanityFilterService::containsLink($request->balasan)) {
            return back()->withInput()->with('error', 'Balasan tidak boleh mengandung tautan / link URL luar demi keamanan.');
        }

        // Cek apakah ada pesan user di percakapan ini yang belum dibalas
        $unrepliedMessage = Kontak::where('identifier', $identifier)
            ->where(function($q) {
                $q->whereNull('balasan')->orWhere('is_replied', false);
            })
            ->whereNotNull('pesan')
            ->latest()
            ->first();

        if ($unrepliedMessage) {
            $unrepliedMessage->update([
                'balasan' => trim($request->balasan),
                'is_replied' => true,
                'is_read' => true,
            ]);
        } else {
            // Semua pesan sebelumnya sudah dibalas atau admin chat beruntun:
            // Buat record baru agar chat lama TIDAK TERTIMPA!
            $prev = Kontak::where('identifier', $identifier)->latest()->first();
            Kontak::create([
                'pengirim' => $prev ? $prev->pengirim : 'Admin',
                'identifier' => $identifier,
                'pesan' => null,
                'balasan' => trim($request->balasan),
                'is_siswa' => $prev ? (bool)$prev->is_siswa : false,
                'is_read' => true,
                'is_replied' => true,
            ]);
        }

        return redirect()->route('admin.kontak.chat', $identifier)
            ->with('success', 'Balasan pesan berhasil terkirim kepada pengguna!');
    }

    /**
     * 💬 Live polling stream chat untuk auto-refresh tanpa reload browser
     */
    public function stream(string $identifier)
    {
        $riwayat = Kontak::where('identifier', $identifier)
            ->orderBy('created_at', 'asc')
            ->get();

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
}
