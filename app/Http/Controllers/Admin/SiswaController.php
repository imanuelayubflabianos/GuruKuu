<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\SiswaImport;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    public function index() 
    { 
        return view('admin.siswa.index', [
            'siswa' => User::where('role', 'siswa')->with('jurusan')->latest()->get()
        ]); 
    }

    public function create() 
    { 
        return view('admin.siswa.create', [
            'jurusan' => Jurusan::all()
        ]); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|unique:users,nis', 
            'name' => 'required|string|max:255',
            'kelas' => 'required|string|max:255', 
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tanggal_lahir' => 'required|date', 
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $data = [
            'nis' => $request->nis, 
            'name' => $request->name, 
            'kelas' => $request->kelas,
            'jurusan_id' => $request->jurusan_id, 
            'tanggal_lahir' => $request->tanggal_lahir,
            'role' => 'siswa', 
            'email' => null, 
            'password' => null, 
            'is_active' => true
        ];
        
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('siswa', 'public');
        }
        
        User::create($data);
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new SiswaImport, $request->file('file'));
        return back()->with('success', 'Data siswa berhasil diimport dari Excel!');
    }

    public function edit(User $siswa) 
    { 
        return view('admin.siswa.edit', [
            'siswa' => $siswa,
            'jurusan' => Jurusan::all()
        ]); 
    }

    public function update(Request $request, User $siswa)
    {
        $request->validate([
            'nis' => 'required|string|unique:users,nis,' . $siswa->id, 
            'name' => 'required|string|max:255',
            'kelas' => 'required|string|max:255', 
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tanggal_lahir' => 'required|date', 
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $data = [
            'nis' => $request->nis, 
            'name' => $request->name, 
            'kelas' => $request->kelas,
            'jurusan_id' => $request->jurusan_id, 
            'tanggal_lahir' => $request->tanggal_lahir
        ];
        
        if ($request->hasFile('photo')) {
            if ($siswa->photo) Storage::disk('public')->delete($siswa->photo);
            $data['photo'] = $request->file('photo')->store('siswa', 'public');
        }
        
        $siswa->update($data);
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa diperbarui!');
    }

    public function destroy(User $siswa)
    {
        if ($siswa->photo) Storage::disk('public')->delete($siswa->photo);
        $siswa->delete();
        return back()->with('success', 'Siswa dihapus!');
    }
}