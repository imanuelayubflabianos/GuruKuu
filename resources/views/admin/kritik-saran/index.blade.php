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
                        <div class="fw-bold">{{ $f->siswa->name ?? 'Anonim' }}</div>
                        <small class="text-muted font-mono">NIS: {{ $f->siswa->nis ?? '-' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $f->kelas?->nama_kelas ?? '-' }}</span>
                    </td>
                    <td>
                        @if(str_contains($f->kritik ?? '', 'melanggar'))
                            <div class="text-danger fw-bold small"><i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $f->kritik }}</div>
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
                        @if(!str_contains($f->kritik ?? '', 'melanggar'))
                        <div class="d-flex justify-content-center gap-1">
                            <form action="{{ route('admin.kritik-saran.warn', $f->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Peringatkan siswa dan hapus isi ulasan ini?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Beri Peringatan & Sensor">
                                    <i class="bi bi-shield-exclamation"></i> Peringatan
                                </button>
                            </form>
                            <form action="{{ route('admin.kritik-saran.destroy', $f->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Ulasan">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                        @else
                            <span class="badge bg-secondary">Telah Dimoderasi</span>
                        @endif
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