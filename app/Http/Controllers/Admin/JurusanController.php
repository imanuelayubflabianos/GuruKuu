<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusan = Jurusan::orderBy('kode_jurusan')->get();
        return view('admin.jurusan.index', compact('jurusan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|unique:jurusan,kode_jurusan',
            'nama_jurusan' => 'required|string|max:255',
        ]);

        Jurusan::create($request->only(['kode_jurusan', 'nama_jurusan']));
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil ditambahkan!');
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|unique:jurusan,kode_jurusan,' . $jurusan->id,
            'nama_jurusan' => 'required|string|max:255',
        ]);

        $jurusan->update($request->only(['kode_jurusan', 'nama_jurusan']));
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil diperbarui!');
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil dihapus!');
    }
}