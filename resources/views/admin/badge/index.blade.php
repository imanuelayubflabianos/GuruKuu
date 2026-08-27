@extends('layouts.admin')
@section('title', 'Badge & Penghargaan')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><div class="page-label">APRESIASI</div><h1 class="page-title">Badge & Penghargaan</h1></div>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addBadge"><i class="bi bi-plus-lg"></i> Tambah Badge</button>
</div>

<div class="row g-4">
    @foreach($badge as $b)
    <div class="col-md-3">
        <div class="card-custom p-4 text-center h-100">
            <div class="fs-1 mb-2">{{ $b->icon }}</div>
            <h6 class="fw-bold">{{ $b->nama_badge }}</h6>
            <small class="text-muted d-block mb-3">{{ $b->penghargaan_count }} Guru menerima</small>
            <form action="{{ route('admin.badge.destroy', $b) }}" method="POST" onsubmit="return confirm('Hapus badge ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger w-100">Hapus</button></form>
        </div>
    </div>
    @endforeach
</div>
@endsection