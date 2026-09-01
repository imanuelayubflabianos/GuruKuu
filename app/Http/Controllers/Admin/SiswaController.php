<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'siswa')->with('kelas');
        
        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('kelas.id', $request->kelas);
            });
        }
        
        $siswa = $query->latest()->get();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        return view('admin.siswa.index', compact('siswa', 'kelasList'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('admin.siswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|unique:users,nis',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'required|date',
        ]);

        $user = User::create([
            'name' => $request->name,
            'nis' => $request->nis,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'siswa',
            'tanggal_lahir' => $request->tanggal_lahir,
            'is_active' => true,
        ]);

        $user->kelas()->attach($request->kelas_id, ['tahun_ajaran' => now()->year . '/' . (now()->year + 1)]);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function edit(User $siswa)
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('admin.siswa.edit', compact('siswa', 'kelasList'));
    }

    public function update(Request $request, User $siswa)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|unique:users,nis,' . $siswa->id,
            'email' => 'required|email|unique:users,email,' . $siswa->id,
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'required|date',
        ]);

        $siswa->update([
            'name' => $request->name,
            'nis' => $request->nis,
            'email' => $request->email,
            'tanggal_lahir' => $request->tanggal_lahir,
        ]);

        if ($request->filled('password')) {
            $siswa->update(['password' => bcrypt($request->password)]);
        }

        $siswa->kelas()->sync([
            $request->kelas_id => ['tahun_ajaran' => now()->year . '/' . (now()->year + 1)]
        ]);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(User $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus!');
    }

    public function toggleStatus(User $siswa)
    {
        $siswa->update(['is_active' => !$siswa->is_active]);
        return back()->with('success', 'Status siswa berhasil diubah!');
    }
}