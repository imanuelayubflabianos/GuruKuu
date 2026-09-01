@extends('layouts.admin')
@section('title', 'Feedback Siswa')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">LAPORAN & FEEDBACK</div>
        <h1 class="page-title">Feedback Siswa</h1>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="feedbackTable">
            <thead>
                <tr>
                    <th>GURU</th>
                    <th>SISWA</th>
                    <th>PESAN</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedbacks as $f)
                <tr>
                    <td>{{ $f->guru->nama }}</td>
                    <td>{{ $f->siswa->name ?? 'Anonim' }}</td>
                    <td>
                        @if(str_contains($f->kritik, 'melanggar aturan'))
                            <span class="text-danger fw-bold">{{ $f->kritik }}</span>
                        @else
                            {{ Str::limit($f->kritik ?: $f->saran, 100) }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if(!str_contains($f->kritik, 'melanggar aturan'))
                        <form action="{{ route('admin.kritik-saran.warn-delete', $f->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan dan beri peringatan otomatis?')">
                            @csrf @method('PUT')
                            <button class="btn btn-sm btn-danger" title="Hapus & Beri Peringatan">
                                <i class="bi bi-trash"></i> Hapus & Peringatkan
                            </button>
                        </form>
                        @else
                        <span class="badge bg-secondary">Sudah Diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4">Belum ada feedback.</td></tr>
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