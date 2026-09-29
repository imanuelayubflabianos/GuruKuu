@php
    $role = $targetRole ?? (auth()->check() ? auth()->user()->role : 'publik');
    $faqs = \App\Services\FaqService::getForRole($role);
@endphp


<div class="card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-1 d-flex align-items-center" style="color: var(--text-dark);">
                <i class="bi bi-question-circle-fill text-primary me-2"></i>Pertanyaan Umum (FAQ)
            </h5>
            <p class="text-muted small mb-0">Jawaban ringkas dan praktis seputar penggunaan sistem evaluasi GuruKuu.</p>
        </div>
        <span class="badge bg-light text-primary border px-2.5 py-1.5 fw-semibold">
            <i class="bi bi-patch-question me-1"></i> {{ count($faqs) }} Tanya Jawab
        </span>
    </div>

    <div class="accordion accordion-flush" id="faqAccordionCustom">
        @foreach($faqs as $index => $faq)
            <div class="accordion-item mb-2 border rounded-3 overflow-hidden shadow-none" style="border-color: var(--border) !important;">
                <h2 class="accordion-header" id="headingFaq{{ $index }}">
                    <button class="accordion-button collapsed fw-bold text-dark py-3 px-3.5 bg-light-subtle" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq{{ $index }}" aria-expanded="false" aria-controls="collapseFaq{{ $index }}" style="font-size: 0.92rem;">
                        <i class="bi {{ $faq['icon'] }} text-primary me-2.5 fs-5"></i>
                        {{ $faq['q'] }}
                    </button>
                </h2>
                <div id="collapseFaq{{ $index }}" class="accordion-collapse collapse" aria-labelledby="headingFaq{{ $index }}">
                    <div class="accordion-body text-secondary small lh-base p-3.5 bg-white border-top">
                        {{ $faq['a'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
