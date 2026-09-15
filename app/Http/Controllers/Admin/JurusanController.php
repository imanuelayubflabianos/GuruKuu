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
        $jurusans = Jurusan::all();
        return view('admin.jurusan.index', compact('jurusans'));
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