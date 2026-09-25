@php
    $currentSessionId = session()->getId();
    $histories = \App\Models\LoginHistory::getHistoriesForUser(auth()->user(), $currentSessionId);
    $activeCount = collect($histories)->where('is_active_session', true)->count();
    $otherActiveCount = collect($histories)->where('is_active_session', true)->where('is_current', false)->count();
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
            @if($otherActiveCount > 0)
            <form action="{{ route('auth.device.logout-others') }}" method="POST" onsubmit="return confirm('Keluarkan semua sesi di perangkat lain? Anda tetap login di perangkat ini.');">
                @csrf
                <button type="submit" class="btn btn-outline-warning btn-sm fw-semibold">
                    <i class="bi bi-shield-slash me-1"></i> Keluarkan Perangkat Lain ({{ $otherActiveCount }})
                </button>
            </form>
            @endif

            <form action="{{ route('auth.device.logout-all') }}" method="POST" onsubmit="return confirm('Peringatan: Aksi ini akan mengeluarkan seluruh perangkat termasuk yang sedang Anda gunakan saat ini. Lanjutkan?');">
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
                                <form action="{{ route('auth.device.logout', $history->id ?: $history->session_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Keluarkan sesi pada perangkat {{ $history->device_name }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1" title="Keluarkan perangkat ini">
                                        <i class="bi bi-box-arrow-right me-1"></i> Log Out
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small fst-italic">Selesai</span>
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
