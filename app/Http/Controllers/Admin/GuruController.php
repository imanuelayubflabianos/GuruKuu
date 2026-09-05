<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        return view('admin.guru.create', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip'        => 'required|string|max:50|unique:guru,nip',
            'nama'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255|unique:guru,email',
            'phone'      => 'nullable|string|max:50',
            'kategori'   => 'required|in:normada,produktif',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['photo']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('guru', 'public');
            $data['photo'] = $path;
        }

        Guru::create($data);
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function edit(Guru $guru)
    {
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        return view('admin.guru.edit', compact('guru', 'jurusans'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nip'        => 'required|string|max:50|unique:guru,nip,' . $guru->id,
            'nama'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255|unique:guru,email,' . $guru->id,
            'phone'      => 'nullable|string|max:50',
            'kategori'   => 'required|in:normada,produktif',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['photo']);

        if ($request->hasFile('photo')) {
            if ($guru->photo && Storage::disk('public')->exists($guru->photo)) {
                Storage::disk('public')->delete($guru->photo);
            }
            $path = $request->file('photo')->store('guru', 'public');
            $data['photo'] = $path;
        }

        $guru->update($data);
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil diperbarui!');
    }

    public function destroy(Guru $guru)
    {
        if ($guru->photo && Storage::disk('public')->exists($guru->photo)) {
            Storage::disk('public')->delete($guru->photo);
        }
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus!');
    }
}