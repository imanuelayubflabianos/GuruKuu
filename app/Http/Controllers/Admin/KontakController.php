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
        $request->validate(['balasan' => 'required|string|max:100'], ['balasan.max' => 'Balasan administrator melebihi batas maksimal 100 karakter.']);

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
        $request->validate(['balasan' => 'required|string|max:100'], ['balasan.max' => 'Balasan administrator melebihi batas maksimal 100 karakter.']);

        if (ProfanityFilterService::containsLink($request->balasan)) {
            return back()->withInput()->with('error', 'Balasan tidak boleh mengandung tautan / link URL luar demi keamanan.');
        }

        $kontak->update(['balasan' => $request->balasan]);
        return back()->with('success', 'Balasan berhasil diperbarui!');
    }

    public function destroyReply(Kontak $kontak)
    {
        $kontak->update([
            'balasan' => null,
            'is_replied' => false
        ]);
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
        $senderName = $first->pengirim;
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
            'balasan' => 'required|string|min:2|max:100',
        ], [
            'balasan.required' => 'Isi balasan tidak boleh kosong.',
            'balasan.max' => 'Balasan melebihi batas maksimal 100 karakter.',
        ]);

        if (ProfanityFilterService::containsLink($request->balasan)) {
            return back()->withInput()->with('error', 'Balasan tidak boleh mengandung tautan / link URL luar demi keamanan.');
        }

        // Cari pesan terbaru dari percakapan ini
        $lastMessage = Kontak::where('identifier', $identifier)
            ->latest()
            ->first();

        if ($lastMessage) {
            $lastMessage->update([
                'balasan' => trim($request->balasan),
                'is_replied' => true,
                'is_read' => true,
            ]);
        }

        return redirect()->route('admin.kontak.chat', $identifier)
            ->with('success', 'Balasan pesan berhasil terkirim kepada pengguna!');
    }
}
