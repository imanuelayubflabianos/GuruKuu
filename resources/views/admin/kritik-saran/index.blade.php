@extends('layouts.admin')
@section('title', 'Feedback & Ulasan Siswa')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">LAPORAN & FEEDBACK</div>
        <h1 class="page-title">Feedback & Ulasan Siswa</h1>
        <p class="page-subtitle">Daftar kritik dan saran dari siswa untuk para guru yang telah dinilai.</p>
    </div>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="feedbackTable">
            <thead>
                <tr>
                    <th style="width: 50px;">NO</th>
                    <th>GURU</th>
                    <th>SISWA</th>
                    <th>KELAS</th>
                    <th>KRITIK & SARAN</th>
                    <th class="text-center" style="width: 180px;">AKSI MODERASI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedbacks as $index => $f)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $f->guru->photo_url }}" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;">
                            <div>
                                <div class="fw-bold">{{ $f->guru->nama }}</div>
                                <small class="text-muted font-mono">{{ $f->guru->nip }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($f->siswa)
                            <div class="fw-bold text-dark">{{ $f->siswa->name }}</div>
                            <small class="text-muted font-mono">NIS: {{ $f->siswa->nis }}</small>
                            <div>
                                <span class="badge bg-secondary-subtle text-secondary border mt-1" style="font-size: 0.65rem;" title="Identitas siswa disembunyikan dari guru dan sesama siswa">
                                    <i class="bi bi-shield-lock-fill me-1"></i>Anonim bagi Guru & Siswa Lain
                                </span>
                            </div>
                        @else
                            <div class="fw-bold"><i class="bi bi-incognito me-1"></i>Siswa (Anonim)</div>
                            <small class="text-muted">Data Akun Siswa</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $f->kelas ? $f->kelas->nama_kelas . ' Kelas ' . $f->kelas->tingkat : '-' }}</span>
                    </td>
                    <td>
                        @php
                            $isCensored = $f->is_censored || str_contains($f->kritik ?? '', 'melanggar');
                        @endphp
                        @if($isCensored)
                            <div class="p-2 rounded border border-warning-subtle bg-warning-subtle text-dark small mb-2">
                                <div class="fw-bold text-warning-emphasis d-flex align-items-center gap-1 mb-1">
                                    <i class="bi bi-shield-exclamation text-warning"></i>
                                    <span>Ulasan Disembunyikan (Disensor)</span>
                                </div>
                                <div class="text-muted" style="font-size: 0.8rem;">
                                    {{ $f->censored_reason ?: 'Ulasan ini tidak memenuhi kriteria kebijakan komunitas.' }}
                                </div>
                            </div>
                            
                            <details class="mt-1">
                                <summary class="text-muted small" style="cursor: pointer; font-size: 0.78rem;">
                                    <i class="bi bi-eye me-1"></i>Lihat ulasan asli (Khusus Admin)
                                </summary>
                                <div class="p-2 mt-1 rounded bg-light border small">
                                    @if($f->kritik)
                                        <div class="mb-1"><strong class="text-danger small">Kritik:</strong> <span class="text-dark">{{ $f->kritik }}</span></div>
                                    @endif
                                    @if($f->saran)
                                        <div><strong class="text-success small">Saran:</strong> <span class="text-dark">{{ $f->saran }}</span></div>
                                    @endif
                                </div>
                            </details>
                        @else
                            @if($f->kritik)
                                <div class="mb-1">
                                    <strong class="text-danger small"><i class="bi bi-chat-left-dots me-1"></i>Kritik:</strong>
                                    <span class="small text-dark">{{ $f->kritik }}</span>
                                </div>
                            @endif
                            @if($f->saran)
                                <div>
                                    <strong class="text-success small"><i class="bi bi-lightbulb me-1"></i>Saran:</strong>
                                    <span class="small text-dark">{{ $f->saran }}</span>
                                </div>
                            @endif
                        @endif
                        <div class="mt-1">
                            <small class="text-muted font-mono" style="font-size: 0.75rem;">{{ $f->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            @if(!$isCensored)
                                <form action="{{ route('admin.kritik-saran.warn', $f->id) }}" method="POST" class="d-inline"
                                      data-confirm="Beri peringatan kepada {{ $f->siswa ? $f->siswa->name : 'siswa ini' }} dan sensor ulasan agar disembunyikan dari publik & guru?"
                                      data-confirm-title="Beri Peringatan & Sensor Ulasan"
                                      data-confirm-btn="Beri Peringatan"
                                      data-confirm-type="warning">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Beri Peringatan & Sensor Ulasan">
                                        <i class="bi bi-shield-exclamation me-1"></i>Peringatan
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.kritik-saran.unwarn', $f->id) }}" method="POST" class="d-inline"
                                      data-confirm="Batalkan status sensor dan tampilkan kembali ulasan ini?"
                                      data-confirm-title="Batalkan Sensor"
                                      data-confirm-btn="Buka Sensor"
                                      data-confirm-type="question">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Batalkan Sensor">
                                        <i class="bi bi-shield-check me-1"></i>Buka Sensor
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.kritik-saran.destroy', $f->id) }}" method="POST" class="d-inline"
                                  data-confirm="Apakah Anda yakin ingin menghapus permanen ulasan ini? Data yang dihapus tidak dapat dipulihkan."
                                  data-confirm-title="Hapus Ulasan Permanen"
                                  data-confirm-btn="Hapus Permanen"
                                  data-confirm-type="danger">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Ulasan Permanen">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-chat-left-text fs-1 d-block mb-2"></i>
                        Belum ada ulasan kritik atau saran yang masuk.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#feedbackTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        pageLength: 10
    });
});
</script>
@endpush