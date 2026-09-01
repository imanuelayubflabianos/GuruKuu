@extends('layouts.admin')
@section('title', 'Feedback Siswa')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">FEEDBACK SISWA</div>
        <h1 class="page-title">Kritik & Saran</h1>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="feedbackTable">
            <thead>
                <tr>
                    <th>GURU</th>
                    <th>SISWA</th>
                    <th>FEEDBACK</th>
                    <th>TANGGAL</th>
                    <th class="text-center" style="width: 150px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedbacks as $f)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $f->guru->photo_url }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                            <strong class="small">{{ $f->guru->nama }}</strong>
                        </div>
                    </td>
                    <td>
                        <strong class="small">{{ $f->siswa->name ?? 'Anonim' }}</strong>
                        <br><small class="text-muted">{{ $f->siswa->nis ?? '-' }}</small>
                    </td>
                    <td style="max-width: 400px;">
                        @if($f->kritik)
                            <div class="mb-1 p-2 rounded small" style="background: #fff3cd; border-left: 3px solid #ffc107;">
                                <strong>Kritik:</strong> {{ $f->kritik }}
                            </div>
                        @endif
                        @if($f->saran)
                            <div class="p-2 rounded small" style="background: #d1ecf1; border-left: 3px solid #17a2b8;">
                                <strong>Saran:</strong> {{ $f->saran }}
                            </div>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $f->created_at->format('d M Y') }}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i> Aksi
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('admin.kritik-saran.action', $f) }}" method="POST" onsubmit="return confirm('Hapus feedback ini?')">
                                        @csrf
                                        <input type="hidden" name="action" value="hapus">
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="bi bi-trash me-2"></i>Hapus Feedback
                                        </button>
                                    </form>
                                </li>
                                @if($f->siswa_id)
                                <li>
                                    <form action="{{ route('admin.kritik-saran.action', $f) }}" method="POST" onsubmit="return confirm('Beri peringatan ke siswa ini?')">
                                        @csrf
                                        <input type="hidden" name="action" value="peringatan">
                                        <button class="dropdown-item text-warning" type="submit">
                                            <i class="bi bi-exclamation-triangle me-2"></i>Beri Peringatan
                                        </button>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">Belum ada feedback.</td>
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
        ordering: false,
        pageLength: 10
    });
});
</script>
@endpush