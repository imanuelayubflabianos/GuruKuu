<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::withCount(['kelas', 'siswa'])
            ->orderBy('nama_jurusan')
            ->paginate(15);
        return view('admin.jurusan.index', compact('jurusans'));
    }

    public function show(Request $request, Jurusan $jurusan)
    {
        $jurusan->loadCount(['kelas', 'siswa', 'guru']);
        $kelas = $jurusan->kelas()->with('jurusan')->withCount('siswa')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasId = $request->integer('kelas_id') ?: null;
        if ($kelasId && !$kelas->contains('id', $kelasId)) {
            $kelasId = null;
        }
        $siswa = $jurusan->siswa()
            ->when($kelasId, fn ($query) => $query->whereHas('kelas', fn ($kelasQuery) => $kelasQuery->whereKey($kelasId)))
            ->with('kelas')
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.jurusan.show', compact('jurusan', 'kelas', 'siswa', 'kelasId'));
    }

    public function create()
    {
        return view('admin.jurusan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|max:50|unique:jurusan,kode_jurusan',
            'nama_jurusan' => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $data = $request->only(['kode_jurusan', 'nama_jurusan', 'deskripsi']);
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('jurusan', 'public');
        }

        Jurusan::create($data);
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil ditambahkan!');
    }

    public function edit(Jurusan $jurusan)
    {
        return view('admin.jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|max:50|unique:jurusan,kode_jurusan,' . $jurusan->id,
            'nama_jurusan' => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $data = $request->only(['kode_jurusan', 'nama_jurusan', 'deskripsi']);
        if ($request->hasFile('logo')) {
            if ($jurusan->logo && Storage::disk('public')->exists($jurusan->logo)) {
                Storage::disk('public')->delete($jurusan->logo);
            }
            $data['logo'] = $request->file('logo')->store('jurusan', 'public');
        }

        $jurusan->update($data);
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil diperbarui!');
    }

    public function destroy(Jurusan $jurusan)
    {
        if ($jurusan->logo && Storage::disk('public')->exists($jurusan->logo)) {
            Storage::disk('public')->delete($jurusan->logo);
        }
        $jurusan->delete();
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil dihapus!');
    }
}
