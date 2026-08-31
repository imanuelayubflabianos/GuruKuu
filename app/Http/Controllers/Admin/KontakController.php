<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kontak;
use Illuminate\Http\Request;

class KontakController extends Controller
{
    public function index()
    {
        $kontak = Kontak::latest()->get();
        return view('admin.kontak.index', compact('kontak'));
    }

    public function reply(Request $request, Kontak $kontak)
    {
        $request->validate(['balasan' => 'required|string']);
        $kontak->update([
            'balasan' => $request->balasan,
            'is_replied' => true
        ]);
        return back()->with('success', 'Balasan berhasil dikirim!');
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
}