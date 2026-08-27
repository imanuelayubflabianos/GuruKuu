<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ManajemenAkunController extends Controller
{
    /**
     * Tampilkan daftar akun siswa (aktif & belum aktif)
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'siswa');

        // Filter status
        if ($request->filter === 'aktif') {
            $query->where('is_active', true);
        } elseif ($request->filter === 'belum') {
            $query->where('is_active', false);
        }

        // Search by NIS atau nama
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nis', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%");
            });
        }

        $siswa = $query->with('jurusan')->latest()->paginate(15);

        $stats = [
            'total' => User::where('role', 'siswa')->count(),
            'aktif' => User::where('role', 'siswa')->where('is_active', true)->count(),
            'belum' => User::where('role', 'siswa')->where('is_active', false)->count(),
        ];

        return view('admin.manajemen-akun.index', compact('siswa', 'stats'));
    }

    /**
     * Detail siswa
     */
    public function show(User $siswa)
    {
        if ($siswa->role !== 'siswa') {
            return redirect()->route('admin.manajemen-akun.index')
                ->with('error', 'User bukan siswa.');
        }
        
        $siswa->load('jurusan');
        return view('admin.manajemen-akun.show', compact('siswa'));
    }

    /**
     * Aktivasi akun siswa (tanpa generate password)
     */
    public function activate(User $siswa)
    {
        if ($siswa->role !== 'siswa') {
            return back()->with('error', 'User bukan siswa.');
        }

        $siswa->is_active = true;
        $siswa->activated_at = now();
        $siswa->save();

        return back()->with('success', 'Akun ' . $siswa->name . ' berhasil diaktifkan.');
    }

    /**
     * Generate password awal untuk siswa
     */
    public function generatePassword(User $siswa)
    {
        if ($siswa->role !== 'siswa') {
            return back()->with('error', 'User bukan siswa.');
        }

        // Generate password sementara: GuruKuu@XXXX
        $passwordBaru = $this->generateTemporaryPassword();

        $siswa->password = Hash::make($passwordBaru);
        $siswa->is_active = true;
        $siswa->force_change_password = true;
        $siswa->activated_at = $siswa->activated_at ?? now();
        $siswa->save();

        // Simpan password di session agar hanya bisa dilihat 1x
        session()->flash('generated_password', $passwordBaru);
        session()->flash('generated_password_for', $siswa->name . ' (NIS: ' . $siswa->nis . ')');

        return back()->with('success', 'Password berhasil di-generate! Segera sampaikan ke siswa.');
    }

    /**
     * Reset password siswa
     */
    public function resetPassword(User $siswa)
    {
        if ($siswa->role !== 'siswa') {
            return back()->with('error', 'User bukan siswa.');
        }

        $passwordBaru = $this->generateTemporaryPassword();

        $siswa->password = Hash::make($passwordBaru);
        $siswa->force_change_password = true;
        $siswa->save();

        session()->flash('generated_password', $passwordBaru);
        session()->flash('generated_password_for', $siswa->name . ' (NIS: ' . $siswa->nis . ')');

        return back()->with('success', 'Password berhasil direset! Segera sampaikan ke siswa.');
    }

    /**
     * Nonaktifkan akun
     */
    public function deactivate(User $siswa)
    {
        if ($siswa->role !== 'siswa') {
            return back()->with('error', 'User bukan siswa.');
        }

        $siswa->is_active = false;
        $siswa->save();

        return back()->with('success', 'Akun ' . $siswa->name . ' berhasil dinonaktifkan.');
    }

    /**
     * Generate password sementara yang aman
     */
    private function generateTemporaryPassword(): string
    {
        return 'GuruKuu@' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}