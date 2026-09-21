<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;
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
        
        if ($request->filled('kelas_id')) {
            $query->whereHas('kelas', function ($kelas) use ($request) {
                $kelas->whereKey($request->kelas_id);
            });
        }
        
        $guru = $query->with('kelas.jurusan')->latest()->paginate(15)->withQueryString();
        $this->loadLinkedUsers($guru->getCollection());
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        return view('admin.guru.index', compact('guru', 'kelasList'));
    }

    /**
     * Memuat akun guru dalam satu query untuk menghindari query tambahan per baris tabel.
     */
    private function loadLinkedUsers($gurus): void
    {
        $nips = $gurus->pluck('nip')->filter()->values();
        $emails = $gurus->pluck('email')->filter()->values();

        if ($nips->isEmpty() && $emails->isEmpty()) {
            return;
        }

        $users = User::where('role', 'guru')
            ->where(function ($query) use ($nips, $emails) {
                if ($nips->isNotEmpty()) {
                    $query->whereIn('nis', $nips);
                }

                if ($emails->isNotEmpty()) {
                    $query->orWhereIn('email', $emails);
                }
            })
            ->get();

        $usersByNip = $users->keyBy('nis');
        $usersByEmail = $users->filter(fn ($user) => filled($user->email))->keyBy('email');

        $gurus->each(function ($guru) use ($usersByNip, $usersByEmail) {
            $guru->setRelation(
                'linkedUser',
                $usersByNip->get($guru->nip) ?? $usersByEmail->get($guru->email)
            );
        });
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
            'kategori'   => 'nullable|in:normada,produktif',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['photo']);
        $data['kategori'] = $request->input('kategori') ?: 'normada';

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('guru', 'public');
            $data['photo'] = $path;
        }

        Guru::create($data);
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function show(Guru $guru)
    {
        $periodeAktif = \App\Models\Periode::where('status', 'aktif')->first();
        $periodeId = $periodeAktif?->id;

        $allPenilaian = \App\Models\Penilaian::where('guru_id', $guru->id)
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->get();

        $stats = [
            'total_penilaian' => $allPenilaian->count(),
            'rata_kedisiplinan' => $allPenilaian->avg('kedisiplinan') ?? 0,
            'rata_cara_mengajar' => $allPenilaian->avg('cara_mengajar') ?? 0,
            'rata_komunikasi' => $allPenilaian->avg('komunikasi') ?? 0,
            'rata_tanggung_jawab' => $allPenilaian->avg('tanggung_jawab') ?? 0,
            'rata_kreativitas' => $allPenilaian->avg('kreativitas') ?? 0,
            'rata_keramahan' => $allPenilaian->avg('keramahan') ?? 0,
        ];

        $semuaFeedback = \App\Models\Penilaian::with('siswa')
            ->where('guru_id', $guru->id)
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->latest()
            ->get();

        $arsipPeriode = \App\Models\Periode::where('id', '!=', $periodeId)
            ->whereHas('penilaian', function($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->with(['penilaian' => function($q) use ($guru) {
                $q->where('guru_id', $guru->id)->latest();
            }])
            ->latest('tanggal_mulai')
            ->get();

        $guru->load('kelas.jurusan');
        return view('admin.guru.show', compact('guru', 'periodeAktif', 'stats', 'semuaFeedback', 'arsipPeriode'));
    }

    public function edit(Guru $guru)
    {
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $guru->load('kelas');
        return view('admin.guru.edit', compact('guru', 'kelasList'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nip'        => 'required|string|max:50|unique:guru,nip,' . $guru->id,
            'nama'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255|unique:guru,email,' . $guru->id,
            'phone'      => 'nullable|string|max:50',
            'kategori'   => 'nullable|in:normada,produktif',
            'kelas_ids'  => 'nullable|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['photo', 'kelas_ids']);
        $data['kategori'] = $request->input('kategori') ?: ($guru->kategori ?: 'normada');

        if ($request->hasFile('photo')) {
            if ($guru->photo && Storage::disk('public')->exists($guru->photo)) {
                Storage::disk('public')->delete($guru->photo);
            }
            $path = $request->file('photo')->store('guru', 'public');
            $data['photo'] = $path;
        }

        $guru->update($data);
        $guru->kelas()->sync($request->input('kelas_ids', []));

        // Sinkronkan ke akun User jika guru ini memiliki akun login sistem
        $linkedUser = \App\Models\User::where('nis', $guru->nip)
            ->orWhere('email', $guru->email)
            ->where('role', 'guru')
            ->first();

        if ($linkedUser) {
            $userUpdate = ['name' => $guru->nama];
            if (!empty($data['photo'])) {
                $userUpdate['photo'] = $data['photo'];
            }
            if (!empty($data['email'])) {
                $userUpdate['email'] = $data['email'];
            }
            $linkedUser->update($userUpdate);
        }

        return redirect()->route('admin.guru.index')->with('success', 'Data guru dan sinkronisasi akun berhasil diperbarui!');
    }

    public function destroy(Guru $guru)
    {
        if ($guru->photo && Storage::disk('public')->exists($guru->photo)) {
            Storage::disk('public')->delete($guru->photo);
        }
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus!');
    }

    public function resetPassword(Request $request, Guru $guru)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $linkedUser = \App\Models\User::where('nis', $guru->nip)
            ->orWhere('email', $guru->email)
            ->where('role', 'guru')
            ->first();

        if ($linkedUser) {
            $linkedUser->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);
            return back()->with('success', "Password akun login guru {$guru->nama} berhasil direset!");
        }

        return back()->with('error', "Akun login pengguna untuk guru {$guru->nama} belum terdaftar.");
    }

    public function toggleStatus(Request $request, Guru $guru)
    {
        $linkedUser = \App\Models\User::where('nis', $guru->nip)
            ->orWhere('email', $guru->email)
            ->where('role', 'guru')
            ->first();

        if (!$linkedUser) {
            return back()->with('error', "Akun login pengguna untuk guru {$guru->nama} belum terdaftar di sistem.");
        }

        if ($linkedUser->is_active) {
            $deactivationType = $request->input('deactivation_type', 'permanen');
            $reason = $request->input('deactivated_reason', 'Akun dinonaktifkan oleh Admin / Operator Sekolah.');
            $deactivatedUntil = null;

            if ($deactivationType === 'berkala') {
                if ($request->filled('custom_until')) {
                    $deactivatedUntil = \Carbon\Carbon::parse($request->input('custom_until'))->endOfDay();
                } else {
                    $days = (int) $request->input('duration_days', 3);
                    $deactivatedUntil = now()->addDays($days);
                }
            }

            $linkedUser->update([
                'is_active' => false,
                'deactivation_type' => $deactivationType,
                'deactivated_until' => $deactivatedUntil,
                'deactivated_reason' => $reason,
            ]);

            $typeLabel = $deactivationType === 'berkala'
                ? "secara berkala hingga " . $deactivatedUntil->format('d M Y H:i')
                : "secara permanen";

            return back()->with('success', "Akun login guru {$guru->nama} berhasil dinonaktifkan {$typeLabel}.");
        } else {
            // Aktifkan kembali
            $linkedUser->update([
                'is_active' => true,
                'deactivation_type' => null,
                'deactivated_until' => null,
                'deactivated_reason' => null,
            ]);

            return back()->with('success', "Akun login guru {$guru->nama} berhasil diaktifkan kembali!");
        }
    }
}
