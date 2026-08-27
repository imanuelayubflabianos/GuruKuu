@extends('layouts.admin')
@section('title', 'Feedback Siswa')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">UMPAN BALIK</div>
        <h1 class="page-title">Kritik & Saran</h1>
        <p class="page-subtitle">Kelola feedback siswa. Feedback toxic terdeteksi otomatis.</p>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="kritikTable">
            <thead>
                <tr>
                    <th>GURU</th>
                    <th>FEEDBACK</th>
                    <th>STATUS</th>
                    <th>WAKTU</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kritik as $k)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $k->guru->photo_url }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                            <div>
                                <strong>{{ $k->guru->nama }}</strong>
                                <br><small class="text-muted">{{ $k->guru->kategori }}</small>
                            </div>
                        </div>
                    </td>
                    <td style="max-width: 350px;">
                        @if($k->kritik)
                            <div class="mb-1 p-2 rounded small" style="background: #fef3c7; border-left: 3px solid #f59e0b;">
                                <strong>Kritik:</strong> {{ $k->kritik }}
                            </div>
                        @endif
                        @if($k->saran)
                            <div class="p-2 rounded small" style="background: #dbeafe; border-left: 3px solid #3b82f6;">
                                <strong>Saran:</strong> {{ $k->saran }}
                            </div>
                        @endif
                    </td>
                    <td>
                        {{-- ✅ BADGE DETEKSI TOXIC --}}
                        @if($k->isToxic())
                            <span class="badge-custom" style="background: #fee2e2; color: #991b1b;">
                                <i class="bi bi-exclamation-triangle-fill"></i> TOXIC
                            </span>
                        @else
                            <span class="badge-custom" style="background: #d1fae5; color: #065f46;">
                                <i class="bi bi-check-circle-fill"></i> AMAN
                            </span>
                        @endif
                    </td>
                    <td class="font-mono small">{{ $k->created_at->format('d M Y') }}</td>
                    <td class="text-center">
                        {{-- Hapus feedback saja --}}
                        <form action="{{ route('admin.kritik-saran.destroy-feedback', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus feedback (kritik & saran) ini? Nilai penilaian tetap tersimpan.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-warning me-1" title="Hapus Feedback">
                                <i class="bi bi-eraser"></i>
                            </button>
                        </form>
                        {{-- Hapus seluruh penilaian --}}
                        <form action="{{ route('admin.kritik-saran.destroy', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('⚠️ Hapus SELURUH penilaian ini (termasuk nilai)?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Hapus Semua">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Belum ada feedback.
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
    $('#kritikTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        order: [[3, 'desc']],
        pageLength: 10
    });
});
</script>
@endpush