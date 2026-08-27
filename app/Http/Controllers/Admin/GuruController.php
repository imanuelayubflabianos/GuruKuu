<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\GuruImport;
use App\Models\Guru;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    public function index() 
    { 
        return view('admin.guru.index', [
            'guru' => Guru::with('jurusan')->latest()->get()
        ]); 
    }

    public function create() 
    { 
        return view('admin.guru.create', [
            'jurusan' => Jurusan::all()
        ]); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|unique:guru,nip', 
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:normada,produktif', 
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'bio' => 'nullable|string', 
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $data = $request->only(['nip', 'nama', 'kategori', 'jurusan_id', 'bio']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('guru', 'public');
        }
        
        Guru::create($data);
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new GuruImport, $request->file('file'));
        return back()->with('success', 'Data guru berhasil diimport dari Excel!');
    }

    public function edit(Guru $guru) 
    { 
        return view('admin.guru.edit', [
            'guru' => $guru,
            'jurusan' => Jurusan::all()
        ]); 
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nip' => 'required|string|unique:guru,nip,' . $guru->id, 
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:normada,produktif', 
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'bio' => 'nullable|string', 
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $data = $request->only(['nip', 'nama', 'kategori', 'jurusan_id', 'bio']);
        if ($request->hasFile('photo')) {
            if ($guru->photo) Storage::disk('public')->delete($guru->photo);
            $data['photo'] = $request->file('photo')->store('guru', 'public');
        }
        
        $guru->update($data);
        return redirect()->route('admin.guru.index')->with('success', 'Data guru diperbarui!');
    }

    public function destroy(Guru $guru)
    {
        if ($guru->photo) Storage::disk('public')->delete($guru->photo);
        $guru->delete();
        return back()->with('success', 'Guru dihapus!');
    }
}