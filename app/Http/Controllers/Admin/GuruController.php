<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::with('jurusan');
        
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }
        
        $guru = $query->latest()->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        
        return view('admin.guru.index', compact('guru', 'jurusans'));
    }

    public function create()
    {
        $jurusans = Jurusan::all();
        return view('admin.guru.create', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:50|unique:guru,nip',
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:normada,produktif',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'bio' => 'nullable|string',
        ]);

        Guru::create($request->all());
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function edit(Guru $guru)
    {
        $jurusans = Jurusan::all();
        return view('admin.guru.edit', compact('guru', 'jurusans'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nip' => 'required|string|max:50|unique:guru,nip,' . $guru->id,
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:normada,produktif',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'bio' => 'nullable|string',
        ]);

        $guru->update($request->all());
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil diperbarui!');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus!');
    }
}