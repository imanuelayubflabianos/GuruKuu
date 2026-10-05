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

        // Pencarian nama, NIP, atau email
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }
        
        // Filter kategori (normada / produktif)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        // Filter kelas yang diajar
        $kelasId = $request->input('kelas_id', $request->input('kelas'));
        if (!empty($kelasId)) {
            $query->whereHas('kelas', function ($kelas) use ($kelasId) {
                $kelas->whereKey($kelasId);
            });
        }

        // Filter status akun aktif / nonaktif
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $activeNips = User::where('role', 'guru')->where('is_active', true)->pluck('nis')->filter()->toArray();
                $activeEmails = User::where('role', 'guru')->where('is_active', true)->pluck('email')->filter()->toArray();
                $query->where(function ($q) use ($activeNips, $activeEmails) {
                    $q->whereIn('nip', $activeNips)->orWhereIn('email', $activeEmails);
                });
            } elseif ($request->status === 'nonaktif') {
                $inactiveNips = User::where('role', 'guru')->where('is_active', false)->pluck('nis')->filter()->toArray();
                $inactiveEmails = User::where('role', 'guru')->where('is_active', false)->pluck('email')->filter()->toArray();
                $query->where(function ($q) use ($inactiveNips, $inactiveEmails) {
                    $q->whereIn('nip', $inactiveNips)->orWhereIn('email', $inactiveEmails);
                });
            }
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
        $badges = \App\Models\Badge::orderBy('nama_badge')->get();
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('admin.guru.create', compact('jurusans', 'badges', 'kelasList'));
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
            'kelas_ids'  => 'nullable|array',
            'kelas_ids.*'=> 'exists:kelas,id',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'badge_ids'  => 'nullable|array',
            'badge_ids.*'=> 'exists:badge,id',
        ]);

        if (!\App\Services\ProfanityFilterService::isClean($request->nama) || ($request->filled('bio') && !\App\Services\ProfanityFilterService::isClean($request->bio))) {
            return back()->withInput()->with('error', 'Nama atau deskripsi diri guru mengandung kata yang melanggar etika moderasi bahasa.');
        }

        $data = $request->except(['photo', 'badge_ids', 'kelas_ids']);
        $data['kategori'] = $request->input('kategori') ?: 'normada';

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('guru', 'public');
            $data['photo'] = $path;
        }

        $guru = Guru::create($data);
        $guru->kelas()->sync($request->input('kelas_ids', []));

        if ($request->filled('badge_ids')) {
            $periodeAktif = \App\Models\Periode::where('status', 'aktif')->first() ?? \App\Models\Periode::first();
            foreach ($request->input('badge_ids', []) as $badgeId) {
                \App\Models\Penghargaan::firstOrCreate([
                    'guru_id'    => $guru->id,
                    'badge_id'   => $badgeId,
                    'periode_id' => $periodeAktif?->id,
                ]);
            }
        }

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan!');
    }

    /**
     * Import Jadwal dan Pembagian Jam Mengajar Guru dari Spreadsheet Excel / CSV
     */
    public function importJadwal(Request $request, \App\Services\GuruJadwalImportService $service)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:15360',
            'replace_existing' => 'nullable',
            'create_if_not_found' => 'nullable',
        ], [
            'file.required' => 'Silakan pilih file spreadsheet (.xlsx, .xls, .csv).',
            'file.mimes'    => 'Format file harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'file.max'      => 'Ukuran file maksimal adalah 15 MB.',
        ]);

        $replaceExisting = $request->boolean('replace_existing', true);
        $createIfNotFound = $request->boolean('create_if_not_found', false);

        try {
            $result = $service->import($request->file('file'), $replaceExisting, $createIfNotFound);

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['message'] ?? 'Gagal memproses file.');
            }

            $msg = $result['message'];
            if (!empty($result['unmatched_teachers'])) {
                $unmatchedCount = count($result['unmatched_teachers']);
                $msg .= " Catatan: Terdapat {$unmatchedCount} nama guru di file yang belum terdaftar di GuruKuu.";
            }

            return redirect()->route('admin.guru.index')
                ->with('success', $msg)
                ->with('import_details', $result);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Import Jadwal Guru Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage());
        }
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
            'rata_komunikasi' => $allPenilaian->avg('komunikasi') ?? 0,
            'rata_tanggung_jawab' => $allPenilaian->avg('tanggung_jawab') ?? 0,
            'rata_kreativitas' => $allPenilaian->avg('kreativitas') ?? 0,
            'rata_keramahan' => $allPenilaian->avg('keramahan') ?? 0,
        ];

        $semuaFeedback = \App\Models\Penilaian::with('siswa')
            ->where('guru_id', $guru->id)
            ->where(function($q) {
                $q->where('is_censored', false)->orWhereNull('is_censored');
            })
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

        $guru->load(['kelas.jurusan', 'penghargaan.badge']);
        return view('admin.guru.show', compact('guru', 'periodeAktif', 'stats', 'semuaFeedback', 'arsipPeriode'));
    }

    public function edit(Guru $guru)
    {
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $badges = \App\Models\Badge::orderBy('nama_badge')->get();
        $guru->load(['kelas', 'penghargaan']);
        $assignedBadgeIds = $guru->penghargaan->pluck('badge_id')->toArray();
        return view('admin.guru.edit', compact('guru', 'kelasList', 'badges', 'assignedBadgeIds'));
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
            'badge_ids'  => 'nullable|array',
            'badge_ids.*'=> 'exists:badge,id',
        ]);

        if (!\App\Services\ProfanityFilterService::isClean($request->nama) || ($request->filled('bio') && !\App\Services\ProfanityFilterService::isClean($request->bio))) {
            return back()->withInput()->with('error', 'Nama atau deskripsi diri guru mengandung kata yang melanggar etika moderasi bahasa.');
        }

        $data = $request->except(['photo', 'kelas_ids', 'badge_ids']);
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

        // Sync Badges
        $periodeAktif = \App\Models\Periode::where('status', 'aktif')->first() ?? \App\Models\Periode::first();
        \App\Models\Penghargaan::where('guru_id', $guru->id)->delete();
        if ($request->filled('badge_ids')) {
            foreach ($request->input('badge_ids', []) as $badgeId) {
                \App\Models\Penghargaan::create([
                    'guru_id'    => $guru->id,
                    'badge_id'   => $badgeId,
                    'periode_id' => $periodeAktif?->id,
                ]);
            }
        }

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
                if ($request->input('duration_days') === '5_hours') {
                    $deactivatedUntil = now()->addHours(5);
                } elseif ($request->filled('custom_until')) {
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
