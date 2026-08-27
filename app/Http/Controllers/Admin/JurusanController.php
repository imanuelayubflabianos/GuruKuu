<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index() { return view('admin.jurusan.index', ['jurusan' => Jurusan::all()]); }
    public function create() { return view('admin.jurusan.create'); }

    public function store(Request $request)
    {
        $request->validate(['nama_jurusan' => 'required|string|max:255', 'kode_jurusan' => 'required|string|unique:jurusan,kode_jurusan']);
        Jurusan::create($request->all());
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil ditambahkan!');
    }

    public function edit(Jurusan $jurusan) { return view('admin.jurusan.edit', compact('jurusan')); }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate(['nama_jurusan' => 'required|string|max:255', 'kode_jurusan' => 'required|string|unique:jurusan,kode_jurusan,' . $jurusan->id]);
        $jurusan->update($request->all());
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil diperbarui!');
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return back()->with('success', 'Jurusan dihapus!');
    }
}