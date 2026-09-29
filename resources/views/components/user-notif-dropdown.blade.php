@php
    $user = auth()->user();
    $role = $user?->role ?? 'siswa';
    $notifs = collect();

    if ($role === 'siswa') {
        // 1. Status Akun: Suspensi / Banned
        if ($user->isDeactivated()) {
            $isPermanent = $user->isPermanentlyDeactivated();
            $durasiText = 'Permanen';
            if (!$isPermanent && $user->deactivated_until) {
                $diffDays = ceil(now()->diffInDays($user->deactivated_until, false));
                if ($diffDays > 0) {
                    $durasiText = $diffDays . ' Hari (s/d ' . $user->deactivated_until->translatedFormat('d M Y') . ')';
                } else {
                    $durasiText = 's/d ' . $user->deactivated_until->translatedFormat('d M Y H:i');
                }
            }
            $notifs->push([
                'type' => 'danger',
                'icon' => 'bi-slash-circle-fill text-danger',
                'title' => 'Akun Dinonaktifkan (' . $durasiText . ')',
                'desc' => 'Akun Anda dinonaktifkan ' . ($isPermanent ? 'secara permanen' : 'selama ' . $durasiText) . '. Alasan: ' . ($user->deactivated_reason ?: 'Pelanggaran tata tertib ulasan.'),
                'time' => 'Status Akun',
                'url' => route('siswa.dashboard'),
            ]);
        }

        // 2. Notifikasi Pelanggaran / Peringatan Etika Belum Dibaca
        $pelanggarans = \App\Models\Pelanggaran::where('user_id', $user->id)
            ->where('siswa_is_read', false)
            ->latest()
            ->take(3)
            ->get();
        foreach ($pelanggarans as $p) {
            $notifs->push([
                'type' => 'warning',
                'icon' => 'bi-shield-exclamation text-danger',
                'title' => 'Peringatan Tata Tertib',
                'desc' => $p->notifikasi_siswa ?: 'Ulasan Anda terdeteksi mengandung kata yang tidak sesuai etika sekolah.',
                'time' => $p->created_at->diffForHumans(),
                'url' => route('siswa.dashboard'),
            ]);
        }

        // 3. Notifikasi Guru Membalas Ulasan Anda
        $guruThreadReplies = \App\Models\PenilaianBalasan::where('role', 'guru')
            ->whereHas('penilaian', function($q) use ($user) {
                $q->where('siswa_id', $user->id);
            })
            ->with(['penilaian.guru'])
            ->latest()
            ->take(3)
            ->get();
        foreach ($guruThreadReplies as $gtr) {
            $namaGuru = $gtr->penilaian?->guru?->nama ?? 'Guru';
            $guruId = $gtr->penilaian?->guru_id;
            $notifs->push([
                'type' => 'primary',
                'icon' => 'bi-chat-heart-fill text-primary',
                'title' => "Guru {$namaGuru} Membalas Ulasan",
                'desc' => '"' . \Illuminate\Support\Str::limit($gtr->pesan, 65) . '"',
                'time' => $gtr->created_at->diffForHumans(),
                'url' => $guruId ? route('siswa.guru.show', $guruId) : route('siswa.riwayat'),
            ]);
        }

        // Balasan langsung di tabel penilaian (jika ada balasan_guru dan belum masuk di thread)
        $directReplies = \App\Models\Penilaian::where('siswa_id', $user->id)
            ->whereNotNull('balasan_guru')
            ->with('guru')
            ->latest('updated_at')
            ->take(2)
            ->get();
        foreach ($directReplies as $dr) {
            if (!$guruThreadReplies->contains('penilaian_id', $dr->id)) {
                $namaGuru = $dr->guru?->nama ?? 'Guru';
                $notifs->push([
                    'type' => 'primary',
                    'icon' => 'bi-reply-fill text-primary',
                    'title' => "Guru {$namaGuru} Menanggapi Ulasan",
                    'desc' => '"' . \Illuminate\Support\Str::limit($dr->balasan_guru, 65) . '"',
                    'time' => $dr->updated_at->diffForHumans(),
                    'url' => route('siswa.guru.show', $dr->guru_id),
                ]);
            }
        }

        // 4. Status Periode Penilaian (Reset / Aktif Baru)
        if ($periode = \App\Models\Periode::where('status', 'aktif')->first()) {
            $notifs->push([
                'type' => 'success',
                'icon' => 'bi-arrow-clockwise text-success',
                'title' => "Periode Baru: {$periode->nama_periode}",
                'desc' => "Periode evaluasi {$periode->tahun_ajaran} (Semester {$periode->semester}) telah aktif/direset. Berikan evaluasi objektif!",
                'time' => 'Periode Aktif',
                'url' => route('siswa.guru.index'),
            ]);
        }

        // 5. Balasan Pesan dari Admin (Fitur Chat Kontak)
        $identifier = $user->nis;
        $repliedChats = $identifier ? \App\Models\Kontak::where('identifier', $identifier)
            ->whereNotNull('balasan')
            ->latest('updated_at')
            ->take(2)
            ->get() : collect();
        foreach ($repliedChats as $c) {
            $notifs->push([
                'type' => 'info',
                'icon' => 'bi-headset text-info',
                'title' => 'Balasan dari Administrator',
                'desc' => \Illuminate\Support\Str::limit($c->balasan, 65),
                'time' => $c->updated_at->diffForHumans(),
                'url' => route('siswa.pengaturan') . '#tabChat',
            ]);
        }
    } elseif ($role === 'guru') {
        $guruModel = $user->guru ?? \App\Models\Guru::where('nip', $user->nis)->first();

        // 1. Status Periode Penilaian (Reset / Aktif Baru)
        if ($periode = \App\Models\Periode::where('status', 'aktif')->first()) {
            $notifs->push([
                'type' => 'success',
                'icon' => 'bi-arrow-clockwise text-success',
                'title' => "Periode Penilaian: {$periode->nama_periode}",
                'desc' => "Periode {$periode->tahun_ajaran} Semester {$periode->semester} berjalan aktif. Seluruh statistik & leaderboard telah disinkronkan ke periode ini.",
                'time' => 'Periode Aktif',
                'url' => route('guru.dashboard'),
            ]);
        }

        // 2. Notifikasi Ulasan Dibalas Kembali oleh Siswa
        if ($guruModel) {
            $siswaReplies = \App\Models\PenilaianBalasan::where('role', 'siswa')
                ->whereHas('penilaian', function($q) use ($guruModel) {
                    $q->where('guru_id', $guruModel->id);
                })
                ->with('penilaian')
                ->latest()
                ->take(3)
                ->get();
            foreach ($siswaReplies as $sr) {
                $notifs->push([
                    'type' => 'primary',
                    'icon' => 'bi-chat-left-text-fill text-primary',
                    'title' => 'Siswa Membalas Balasan Anda',
                    'desc' => 'Tanggapan siswa: "' . \Illuminate\Support\Str::limit($sr->pesan, 65) . '"',
                    'time' => $sr->created_at->diffForHumans(),
                    'url' => route('guru.ulasan'),
                ]);
            }

            // 3. Ulasan Baru dari Siswa
            $recentReviews = \App\Models\Penilaian::where('guru_id', $guruModel->id)
                ->where(function($q) {
                    $q->where('is_censored', false)->orWhereNull('is_censored');
                })
                ->latest()
                ->take(3)
                ->get();
            foreach ($recentReviews as $rev) {
                $score = round(($rev->rata_rata_evaluasi / 5) * 100);
                $notifs->push([
                    'type' => 'info',
                    'icon' => 'bi-star-fill text-warning',
                    'title' => "Penilaian Siswa Baru ({$score}%)",
                    'desc' => $rev->kritik ?: ($rev->saran ?: 'Siswa memberikan penilaian performa pengajaran.'),
                    'time' => $rev->created_at->diffForHumans(),
                    'url' => route('guru.ulasan'),
                ]);
            }
        }

        // 4. Balasan Pesan dari Admin (Fitur Chat Kontak)
        $identifier = $guruModel?->nip ?? $user->nis;
        $repliedChats = $identifier ? \App\Models\Kontak::where('identifier', $identifier)
            ->whereNotNull('balasan')
            ->latest('updated_at')
            ->take(2)
            ->get() : collect();
        foreach ($repliedChats as $c) {
            $notifs->push([
                'type' => 'info',
                'icon' => 'bi-headset text-info',
                'title' => 'Balasan dari Administrator',
                'desc' => \Illuminate\Support\Str::limit($c->balasan, 65),
                'time' => $c->updated_at->diffForHumans(),
                'url' => route('guru.pengaturan') . '#tabChat',
            ]);
        }
    }

    $unreadCount = $notifs->whereIn('type', ['danger', 'warning'])->count();
    $totalCount = $notifs->count();
@endphp

<div class="dropdown">
    <button class="btn btn-light position-relative p-2 rounded-circle border shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Pusat Notifikasi">
        <i class="bi bi-bell-fill {{ $unreadCount > 0 ? 'text-danger' : ($totalCount > 0 ? 'text-primary' : 'text-secondary') }} fs-5"></i>
        @if($totalCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill {{ $unreadCount > 0 ? 'bg-danger' : 'bg-primary' }} border border-light" style="font-size: 0.65rem;">
                {{ $totalCount }}
                <span class="visually-hidden">notifikasi</span>
            </span>
        @endif
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 mt-2" style="border-radius: 14px; width: 340px; max-width: 90vw; overflow: hidden; z-index: 1060;">
        <li class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-bold small text-dark d-flex align-items-center gap-1.5">
                <i class="bi bi-bell text-primary"></i> Notifikasi Akun
            </span>
            @if($totalCount > 0)
                <span class="badge {{ $unreadCount > 0 ? 'bg-danger' : 'bg-primary' }} rounded-pill" style="font-size: 0.7rem;">{{ $totalCount }} Notifikasi</span>
            @endif
        </li>

        <div style="max-height: 320px; overflow-y: auto;">
            @forelse($notifs as $item)
                <a href="{{ $item['url'] }}" class="dropdown-item p-3 border-bottom text-wrap d-flex align-items-start gap-2.5" style="white-space: normal; transition: background 0.15s;">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center bg-light border flex-shrink-0" style="width: 34px; height: 34px;">
                        <i class="bi {{ $item['icon'] }} fs-6"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="d-flex align-items-center justify-content-between mb-0.5">
                            <strong class="text-dark small d-block text-truncate" style="font-size: 0.82rem;">{{ $item['title'] }}</strong>
                        </div>
                        <p class="text-muted mb-1 small" style="font-size: 0.76rem; line-height: 1.35;">{{ $item['desc'] }}</p>
                        <small class="text-secondary font-mono d-block" style="font-size: 0.68rem;">{{ $item['time'] }}</small>
                    </div>
                </a>
            @empty
                <div class="text-center py-4 px-3 text-muted">
                    <i class="bi bi-bell-slash fs-2 opacity-50 d-block mb-1"></i>
                    <small class="d-block fw-semibold">Belum Ada Notifikasi Baru</small>
                    <span style="font-size: 0.72rem;">Semua aktivitas dan informasi akun Anda terkini.</span>
                </div>
            @endforelse
        </div>

        @if($role === 'siswa')
            <li class="p-2 text-center bg-light border-top">
                <a href="{{ route('siswa.riwayat') }}" class="small text-primary text-decoration-none fw-semibold" style="font-size: 0.75rem;">
                    Buka Riwayat Penilaian <i class="bi bi-arrow-right"></i>
                </a>
            </li>
        @elseif($role === 'guru')
            <li class="p-2 text-center bg-light border-top">
                <a href="{{ route('guru.ulasan') }}" class="small text-primary text-decoration-none fw-semibold" style="font-size: 0.75rem;">
                    Lihat Semua Ulasan <i class="bi bi-arrow-right"></i>
                </a>
            </li>
        @endif
    </ul>
</div>
