<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'siswa')->with('kelas');
        
        // Filter berdasarkan kelas
        if ($request->filled('kelas')) {
            $kelasId = $request->kelas;
            $classMatches = (clone $query)->whereHas('kelas', fn($q) => $q->where('kelas.id', $kelasId));
            if (!$request->filled('search') || $classMatches->exists()) {
                $query->whereHas('kelas', function($q) use ($kelasId) {
                    $q->where('kelas.id', $kelasId);
                });
            }
        }

        // Filter berdasarkan status aktif/nonaktif
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('is_active', true);
            } elseif ($request->status === 'nonaktif') {
                $query->where('is_active', false);
            }
        }

        // Pencarian nama, NIS, email, atau rombel/kelas (mendukung multi-kata / token)
        if ($request->filled('search')) {
            $terms = preg_split('/\s+/', trim($request->search), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($terms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                      ->orWhere('nis', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%")
                      ->orWhereHas('kelas', fn($k) => $k->where('nama_kelas', 'like', "%{$term}%"));
                });
            }
        }
        
        $siswa = $query->latest()->paginate(20)->withQueryString();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        return view('admin.siswa.index', compact('siswa', 'kelasList'));
    }

    public function show(User $siswa)
    {
        abort_unless($siswa->role === 'siswa', 404);

        $siswa->load(['jurusan', 'kelas.jurusan', 'badges']);
        $penilaian = Penilaian::with(['guru', 'periode', 'kelas'])
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->get();
        $pelanggaran = Pelanggaran::with('guru')
            ->where('user_id', $siswa->id)
            ->latest()
            ->get();

        return view('admin.siswa.show', compact('siswa', 'penilaian', 'pelanggaran'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $jurusan = \App\Models\Jurusan::orderBy('nama_jurusan')->get();
        $badges = \App\Models\Badge::orderBy('nama_badge')->get();
        return view('admin.siswa.create', compact('kelasList', 'jurusan', 'badges'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|unique:users,nis',
            'kelas' => 'required|string|max:100',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tanggal_lahir' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'badge_ids' => 'nullable|array',
            'badge_ids.*' => 'exists:badge,id',
        ]);

        if (!\App\Services\ProfanityFilterService::isClean($request->name)) {
            return back()->withInput()->with('error', 'Nama siswa mengandung kata yang melanggar etika moderasi bahasa.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('siswa', 'public');
        }

        $siswa = User::create([
            'name' => $request->name,
            'nis' => $request->nis,
            'email' => $request->nis . '@gurukuu.local',
            'password' => bcrypt($request->nis),
            'role' => 'siswa',
            'kelas' => $request->kelas,
            'jurusan_id' => $request->jurusan_id,
            'tanggal_lahir' => $request->tanggal_lahir,
            'photo' => $photoPath,
            'is_active' => true,
        ]);

        if ($request->filled('badge_ids')) {
            $siswa->badges()->sync($request->input('badge_ids', []));
        }

        $this->syncSiswaKelasPivot($siswa, $request->kelas);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function edit(User $siswa)
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $jurusan = \App\Models\Jurusan::orderBy('nama_jurusan')->get();
        $badges = \App\Models\Badge::orderBy('nama_badge')->get();
        $siswa->load('badges');
        $assignedBadgeIds = $siswa->badges->pluck('id')->toArray();
        return view('admin.siswa.edit', compact('siswa', 'kelasList', 'jurusan', 'badges', 'assignedBadgeIds'));
    }

    public function update(Request $request, User $siswa)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|unique:users,nis,' . $siswa->id,
            'kelas' => 'required|string|max:100',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tanggal_lahir' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'badge_ids' => 'nullable|array',
            'badge_ids.*' => 'exists:badge,id',
        ]);

        if (!\App\Services\ProfanityFilterService::isClean($request->name)) {
            return back()->withInput()->with('error', 'Nama siswa mengandung kata yang melanggar etika moderasi bahasa.');
        }

        $data = [
            'name' => $request->name,
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'jurusan_id' => $request->jurusan_id,
            'tanggal_lahir' => $request->tanggal_lahir,
        ];

        if ($request->hasFile('photo')) {
            if ($siswa->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($siswa->photo);
            }
            $data['photo'] = $request->file('photo')->store('siswa', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $siswa->update($data);
        $siswa->badges()->sync($request->input('badge_ids', []));

        $this->syncSiswaKelasPivot($siswa, $request->kelas);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    private function syncSiswaKelasPivot(User $siswa, ?string $rawKelas): void
    {
        if (empty(trim((string)$rawKelas))) return;
        $matchedKelas = Kelas::all()->first(function ($k) use ($rawKelas) {
            $tingkat = (string) $k->tingkat;
            $nama = (string) $k->nama_kelas;
            $roman = match ((int)$tingkat) { 10 => 'X', 11 => 'XI', 12 => 'XII', default => $tingkat };
            $cands = [$nama, "{$tingkat} {$nama}", "{$roman} {$nama}", "Kelas {$tingkat} {$nama}", "Kelas {$roman} {$nama}", $k->label_singkat];
            foreach ($cands as $c) {
                if (strcasecmp(trim($c), trim($rawKelas)) === 0) return true;
            }
            return stripos($rawKelas, $nama) !== false;
        });

        if ($matchedKelas) {
            $tahunAjaran = \App\Models\Periode::getActivePeriode()?->tahun_ajaran ?? (now()->year . '/' . (now()->year + 1));
            $siswa->kelas()->syncWithoutDetaching([$matchedKelas->id => ['tahun_ajaran' => $tahunAjaran]]);
            if ($matchedKelas->jurusan_id && empty($siswa->jurusan_id)) {
                $siswa->update(['jurusan_id' => $matchedKelas->jurusan_id]);
            }
        }
    }

    public function destroy(User $siswa)
    {
        if ($siswa->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($siswa->photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($siswa->photo);
        }
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus!');
    }

    public function toggleStatus(Request $request, User $siswa)
    {
        if ($siswa->is_active) {
            $deactType = $request->input('deactivation_type', 'permanen');
            $customReason = trim($request->input('deactivated_reason', ''));

            if ($deactType === 'berkala') {
                if ($request->input('duration_days') === '5_hours') {
                    $deactivatedUntil = now()->addHours(5);
                } elseif ($request->filled('custom_until')) {
                    $deactivatedUntil = \Carbon\Carbon::parse($request->custom_until)->endOfDay();
                } else {
                    $days = (int) $request->input('duration_days', 3);
                    $deactivatedUntil = now()->addDays($days);
                }

                $reason = $customReason ?: 'Dinonaktifkan sementara oleh Admin / Operator Sekolah hingga ' . $deactivatedUntil->translatedFormat('d F Y') . ' karena evaluasi tata tertib.';
                $siswa->update([
                    'is_active' => false,
                    'deactivation_type' => 'berkala',
                    'deactivated_until' => $deactivatedUntil,
                    'deactivated_reason' => $reason,
                ]);
                $msg = "Akun siswa {$siswa->name} dinonaktifkan berkala hingga " . $deactivatedUntil->translatedFormat('d M Y H:i') . ".";
            } else {
                $reason = $customReason ?: 'Dinonaktifkan permanen oleh Admin / Operator Sekolah. Hubungi Admin / Operator Sekolah untuk pengaktifan kembali.';
                $siswa->update([
                    'is_active' => false,
                    'deactivation_type' => 'permanen',
                    'deactivated_until' => null,
                    'deactivated_reason' => $reason,
                ]);
                $msg = "Akun siswa {$siswa->name} berhasil dinonaktifkan permanen. Siswa harus menghubungi Admin / Operator Sekolah.";
            }
        } else {
            $siswa->update([
                'is_active' => true,
                'deactivation_type' => null,
                'deactivated_until' => null,
                'deactivated_reason' => null,
            ]);
            $msg = "Akun siswa {$siswa->name} telah diaktifkan kembali!";
        }

        return back()->with('success', $msg);
    }


    public function resetPassword(Request $request, User $siswa)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $siswa->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success', "Password akun siswa {$siswa->name} (NIS: {$siswa->nis}) berhasil diperbarui!");
    }
}
