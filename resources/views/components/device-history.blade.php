@php
    $currentSessionId = session()->getId();
    $histories = \App\Models\LoginHistory::getHistoriesForUser(auth()->user(), $currentSessionId);
    $activeCount = collect($histories)->where('is_active_session', true)->count();
    $otherActiveCount = collect($histories)->where('is_active_session', true)->where('is_current', false)->count();
    $inactiveCount = collect($histories)->where('is_active_session', false)->count();
@endphp

<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold mb-1 d-flex align-items-center" style="color: var(--text-dark);">
                <i class="bi bi-laptop-fill text-primary me-2"></i>Riwayat Perangkat & Sesi Login
            </h5>
            <p class="text-muted small mb-0">
                Daftar perangkat yang pernah atau sedang login ke akun Anda. Total <span class="badge bg-success">{{ $activeCount }} sesi aktif</span> saat ini.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($inactiveCount > 0)
            <form action="{{ route('auth.device.clear-history') }}" method="POST" data-confirm="Bersihkan seluruh riwayat login perangkat yang sudah keluar agar tidak menumpuk?" data-confirm-title="Bersihkan Riwayat Perangkat?" data-confirm-btn="Ya, Bersihkan" data-confirm-type="warning">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm fw-semibold" title="Bersihkan catatan riwayat perangkat yang sudah logout">
                    <i class="bi bi-trash3 me-1"></i> Bersihkan Riwayat ({{ $inactiveCount }})
                </button>
            </form>
            @endif

            @if($otherActiveCount > 0)
            <form action="{{ route('auth.device.logout-others') }}" method="POST" data-confirm="Keluarkan semua sesi di perangkat lain? Anda tetap login di perangkat ini." data-confirm-title="Keluarkan Sesi Perangkat Lain?" data-confirm-btn="Ya, Keluarkan" data-confirm-type="warning">
                @csrf
                <button type="submit" class="btn btn-outline-warning btn-sm fw-semibold">
                    <i class="bi bi-shield-slash me-1"></i> Keluarkan Perangkat Lain ({{ $otherActiveCount }})
                </button>
            </form>
            @endif

            <form action="{{ route('auth.device.logout-all') }}" method="POST" data-confirm="Peringatan: Aksi ini akan mengeluarkan seluruh perangkat termasuk yang sedang Anda gunakan saat ini. Lanjutkan?" data-confirm-title="Keluarkan Seluruh Perangkat?" data-confirm-btn="Ya, Keluarkan Semua" data-confirm-type="danger">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm fw-semibold">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluarkan Semua Perangkat
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small py-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="min-width: 220px;">Perangkat & Browser</th>
                    <th scope="col" style="min-width: 140px;">Alamat IP</th>
                    <th scope="col" style="min-width: 170px;">Waktu Login</th>
                    <th scope="col" style="min-width: 170px;">Aktivitas Terakhir</th>
                    <th scope="col" style="min-width: 150px;">Status</th>
                    <th scope="col" class="text-end" style="min-width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $history)
                    <tr class="{{ $history->is_current ? 'table-success table-opacity-10' : '' }}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light border" style="width: 36px; height: 36px;">
                                    <i class="bi {{ $history->icon }} fs-5"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-dark">{{ $history->device_name }}</strong>
                                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                        {{ $history->platform ?? 'OS' }} • {{ $history->device_type ?? 'Desktop' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">
                                <i class="bi bi-globe2 me-1 text-primary"></i>{{ $history->ip_address ?: '127.0.0.1' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-dark small">
                                <i class="bi bi-calendar3 me-1 text-muted"></i>{{ $history->login_at ? $history->login_at->translatedFormat('d M Y, H:i') : '-' }}
                            </div>
                        </td>
                        <td>
                            <div class="text-dark small">
                                <i class="bi bi-clock-history me-1 text-muted"></i>{{ $history->last_activity ? $history->last_activity->translatedFormat('d M Y, H:i') : '-' }}
                            </div>
                            <small class="text-muted" style="font-size: 0.74rem;">
                                {{ $history->last_activity ? $history->last_activity->diffForHumans() : '' }}
                            </small>
                        </td>
                        <td>
                            @if($history->is_current)
                                <span class="badge bg-success d-inline-flex align-items-center gap-1 px-2.5 py-1.5">
                                    <i class="bi bi-check-circle-fill"></i> Perangkat Ini (Aktif)
                                </span>
                            @elseif($history->is_active_session)
                                <span class="badge bg-primary d-inline-flex align-items-center gap-1 px-2.5 py-1.5">
                                    <i class="bi bi-broadcast"></i> Masih Log In
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border d-inline-flex align-items-center gap-1 px-2 py-1">
                                    <i class="bi bi-door-closed"></i> Sudah Log Out
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($history->is_current)
                                <span class="badge bg-light text-muted border px-2 py-1">
                                    <i class="bi bi-lock-fill me-1"></i>Sesi Utama
                                </span>
                            @elseif($history->is_active_session)
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-light border shadow-xs rounded-circle d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;" title="Pilihan Aksi">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="border-radius: 12px; font-size: 0.85rem; min-width: 175px; box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;">
                                        <li>
                                            <form action="{{ route('auth.device.logout', $history->id ?: $history->session_id) }}" method="POST" data-confirm="Keluarkan sesi pada perangkat {{ $history->device_name }}?" data-confirm-title="Keluarkan Sesi Perangkat?" data-confirm-btn="Ya, Keluarkan" data-confirm-type="warning">
                                                @csrf
                                                <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-danger">
                                                    <i class="bi bi-box-arrow-right text-danger"></i>
                                                    <span>Keluarkan Sesi</span>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <span class="text-muted small fst-italic">Selesai</span>
                                    @if($history->id)
                                    <form action="{{ route('auth.device.destroy', $history->id) }}" method="POST" data-confirm="Hapus catatan riwayat login perangkat ini?" data-confirm-title="Hapus Riwayat Perangkat?" data-confirm-btn="Ya, Hapus" data-confirm-type="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger p-0 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 26px; height: 26px;" title="Hapus dari riwayat">
                                            <i class="bi bi-trash3" style="font-size: 0.72rem;"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-info-circle fs-4 d-block mb-1"></i>
                            Belum ada catatan riwayat login.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2 text-muted small">
        <div>
            <i class="bi bi-shield-check text-success me-1"></i> Sesi kedaluwarsa secara otomatis jika tidak ada aktivitas sesuai kebijakan sistem.
        </div>
        <div>
            Mendeteksi aktivitas mencurigakan? Segera <strong>Keluarkan Semua Perangkat</strong>.
        </div>
    </div>
</div>
