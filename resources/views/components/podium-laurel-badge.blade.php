@props(['rank' => 1, 'text' => null])
@php
    $rankNum = (int)$rank;
    $badgeText = $text ?? ($rankNum === 1 ? '#1st' : ($rankNum === 2 ? '#2nd' : '#3rd'));
@endphp

{{-- 🌟 Top Border Straddling Badge (#1st, #2nd, #3rd) --}}
<div class="gk-podium-header-badge gk-rank-{{ $rankNum }}">
    <span class="badge rounded-pill gk-rank-badge-pill">{{ $badgeText }}</span>
</div>

{{-- 🌿 Bottom Roman Laurel Wreath Cradle (Mahkota Daun Romawi Menyelimuti Bawah & Samping) --}}
<div class="gk-laurel-bottom-cradle gk-rank-{{ $rankNum }}" aria-hidden="true">
    {{-- Left Laurel Branch --}}
    <svg class="gk-laurel-bottom-branch gk-laurel-bottom-left" viewBox="0 0 54 135" fill="currentColor">
        <path d="M48,131 C32,130 16,124 10,105 C6,90 6,45 14,8" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" />
        <path d="M44,129 C38,125 35,117 38,113 C44,113 48,123 44,129 Z" />
        <path d="M36,132 C26,132 20,125 24,120 C30,119 37,124 36,132 Z" />
        <path d="M26,126 C16,127 9,121 12,115 C18,113 26,117 26,126 Z" />
        <path d="M22,118 C18,111 20,103 26,101 C28,107 28,115 22,118 Z" />
        <path d="M12,107 C3,104 0,95 4,91 C9,91 14,98 12,107 Z" />
        <path d="M11,97 C12,89 18,83 24,84 C24,91 19,97 11,97 Z" />
        <path d="M7,87 C-2,83 -2,73 3,70 C9,71 11,79 7,87 Z" />
        <path d="M7,77 C9,69 16,64 22,66 C21,73 16,78 7,77 Z" />
        <path d="M6,65 C-1,60 0,50 6,48 C11,50 12,58 6,65 Z" />
        <path d="M7,55 C10,47 18,43 23,46 C21,53 16,57 7,55 Z" />
        <path d="M8,43 C3,37 5,28 11,27 C15,30 15,37 8,43 Z" />
        <path d="M9,34 C13,27 20,25 23,29 C21,35 17,38 9,34 Z" />
        <path d="M11,21 C8,14 11,6 15,5 C18,9 17,17 11,21 Z" />
        <path d="M13,19 C17,13 23,13 24,18 C22,23 17,24 13,19 Z" />
        <circle cx="21" cy="110" r="2.2" />
        <circle cx="10" cy="90" r="2.2" />
        <circle cx="7" cy="70" r="2.2" />
        <circle cx="7" cy="50" r="2.2" />
        <circle cx="9" cy="30" r="2.2" />
    </svg>

    {{-- Bottom Center Laurel Tie / Knot --}}
    <svg class="gk-laurel-bottom-center" viewBox="0 0 54 22" fill="currentColor">
        <path d="M27,11 C24,6 18,5 14,8 C12,12 17,15 22,13 C17,16 14,20 18,21 C22,21 25,16 27,11 Z" />
        <path d="M27,11 C30,6 36,5 40,8 C42,12 37,15 32,13 C37,16 40,20 36,21 C32,21 29,16 27,11 Z" />
        <circle cx="27" cy="11" r="3.2" />
        <circle cx="21" cy="12" r="1.8" />
        <circle cx="33" cy="12" r="1.8" />
    </svg>

    {{-- Right Laurel Branch (Mirrored) --}}
    <svg class="gk-laurel-bottom-branch gk-laurel-bottom-right" viewBox="0 0 54 135" fill="currentColor">
        <path d="M48,131 C32,130 16,124 10,105 C6,90 6,45 14,8" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" />
        <path d="M44,129 C38,125 35,117 38,113 C44,113 48,123 44,129 Z" />
        <path d="M36,132 C26,132 20,125 24,120 C30,119 37,124 36,132 Z" />
        <path d="M26,126 C16,127 9,121 12,115 C18,113 26,117 26,126 Z" />
        <path d="M22,118 C18,111 20,103 26,101 C28,107 28,115 22,118 Z" />
        <path d="M12,107 C3,104 0,95 4,91 C9,91 14,98 12,107 Z" />
        <path d="M11,97 C12,89 18,83 24,84 C24,91 19,97 11,97 Z" />
        <path d="M7,87 C-2,83 -2,73 3,70 C9,71 11,79 7,87 Z" />
        <path d="M7,77 C9,69 16,64 22,66 C21,73 16,78 7,77 Z" />
        <path d="M6,65 C-1,60 0,50 6,48 C11,50 12,58 6,65 Z" />
        <path d="M7,55 C10,47 18,43 23,46 C21,53 16,57 7,55 Z" />
        <path d="M8,43 C3,37 5,28 11,27 C15,30 15,37 8,43 Z" />
        <path d="M9,34 C13,27 20,25 23,29 C21,35 17,38 9,34 Z" />
        <path d="M11,21 C8,14 11,6 15,5 C18,9 17,17 11,21 Z" />
        <path d="M13,19 C17,13 23,13 24,18 C22,23 17,24 13,19 Z" />
        <circle cx="21" cy="110" r="2.2" />
        <circle cx="10" cy="90" r="2.2" />
        <circle cx="7" cy="70" r="2.2" />
        <circle cx="7" cy="50" r="2.2" />
        <circle cx="9" cy="30" r="2.2" />
    </svg>
</div>
