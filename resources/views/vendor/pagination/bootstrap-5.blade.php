@if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2 w-100 my-2" aria-label="Navigasi Halaman">
        <div class="small text-muted text-center text-md-start order-2 order-md-1">
            Menampilkan <span class="fw-semibold text-dark">{{ $paginator->firstItem() }}</span> - <span class="fw-semibold text-dark">{{ $paginator->lastItem() }}</span> dari <span class="fw-semibold text-dark">{{ $paginator->total() }}</span> data
        </div>

        <ul class="pagination pagination-sm m-0 flex-wrap justify-content-center order-1 order-md-2" style="gap: 4px;">
            {{-- Tombol Sebelumnya (<) --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link rounded border shadow-xs px-2.5 py-1 text-muted" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link rounded border shadow-xs px-2.5 py-1 text-dark" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Angka-angka Halaman (1, 2, 3...) --}}
            @foreach ($elements as $element)
                {{-- Separator Titik Tiga "..." --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link rounded border shadow-xs px-2.5 py-1 text-muted">{{ $element }}</span>
                    </li>
                @endif

                {{-- Link Halaman Berurutan --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link rounded border shadow-xs px-2.5 py-1 fw-bold text-white" style="background-color: #003366 !important; border-color: #003366 !important;">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link rounded border shadow-xs px-2.5 py-1 text-dark" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Selanjutnya (>) --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded border shadow-xs px-2.5 py-1 text-dark" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link rounded border shadow-xs px-2.5 py-1 text-muted" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
