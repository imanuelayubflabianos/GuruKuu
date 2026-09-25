@php
    $role = auth()->user()->role ?? 'siswa';

    if ($role === 'siswa') {
        $faqs = [
            [
                'q' => 'Apakah identitas dan nama saya benar-benar anonim saat menilai guru?',
                'a' => 'Ya, sistem GuruKuu menjamin 100% anonimitas bagi siswa. Nama, NIS, dan profil Anda tidak akan pernah ditampilkan kepada guru yang dinilai. Penilaian dan masukan Anda murni terdaftar secara objektif.',
                'icon' => 'bi-incognito'
            ],
            [
                'q' => 'Bagaimana jika saya keliru atau salah mengirimkan ulasan penilaian?',
                'a' => 'Demi menjaga integritas dan keaslian penilaian, evaluasi yang telah dikirim bersifat final. Apabila terjadi kendala teknis atau kekeliruan darurat, Anda dapat menghubungi Operator Sekolah melalui tab Hubungi Admin.',
                'icon' => 'bi-pencil-square'
            ],
            [
                'q' => 'Mengapa guru yang mengajar di kelas saya belum muncul di daftar penilaian?',
                'a' => 'Daftar guru disinkronkan otomatis berdasarkan rombongan belajar (kelas) Anda dan periode evaluasi yang aktif. Jika ada guru yang belum terdaftar, silakan sampaikan ke admin melalui pesan bantuan.',
                'icon' => 'bi-person-x'
            ],
            [
                'q' => 'Bagaimana cara mengganti kata sandi atau jika lupa password akun?',
                'a' => 'Penggantian dan reset kata sandi dikelola secara terpusat oleh pihak sekolah untuk mencegah penyalahgunaan akun. Silakan hubungi wali kelas atau Administrator Operator Sekolah.',
                'icon' => 'bi-key-fill'
            ],
            [
                'q' => 'Apakah ulasan yang mengandung kata tidak pantas akan terdeteksi?',
                'a' => 'Ya, sistem kami memiliki penyaring kata otomatis berteknologi cerdas. Ulasan kasar akan langsung disensor dan akun pelanggar dapat dikenakan sanksi suspensi dari sistem penilaian.',
                'icon' => 'bi-shield-exclamation'
            ],
        ];
    } elseif ($role === 'guru') {
        $faqs = [
            [
                'q' => 'Apakah guru dapat melihat siapa siswa yang memberikan nilai atau ulasan?',
                'a' => 'Tidak. Seluruh identitas siswa disamarkan secara permanen demi objektivitas dan kenyamanan proses evaluasi pembelajaran.',
                'icon' => 'bi-incognito'
            ],
            [
                'q' => 'Bagaimana cara memberikan tanggapan atau membalas masukan dari siswa?',
                'a' => 'Anda dapat membalas tanggapan siswa secara bijaksana dan edukatif melalui menu Ulasan dengan menekan tombol Balas pada ulasan yang bersangkutan.',
                'icon' => 'bi-chat-left-quote'
            ],
            [
                'q' => 'Bagaimana jika ada ulasan siswa yang tidak pantas atau melanggar norma?',
                'a' => 'Sistem secara otomatis menyensor kata-kata kasar dan mencatat riwayat pelanggaran. Administrator sekolah secara berkala meninjau dan menindaklanjuti log pelanggaran tersebut.',
                'icon' => 'bi-shield-shaded'
            ],
            [
                'q' => 'Kapan periode penilaian guru dibuka dan ditutup?',
                'a' => 'Jadwal dan periode penilaian ditentukan oleh Administrator Sekolah sesuai kalender evaluasi tengah semester atau akhir semester.',
                'icon' => 'bi-calendar-event'
            ],
        ];
    } else { // admin
        $faqs = [
            [
                'q' => 'Bagaimana cara membuka atau menutup periode penilaian evaluasi?',
                'a' => 'Masuk ke menu Pengaturan > Tab Periode. Anda dapat menambahkan periode ajaran baru atau mengaktifkan/menonaktifkan periode yang sudah ada.',
                'icon' => 'bi-calendar-range'
            ],
            [
                'q' => 'Bagaimana cara mengelola kata sensor (profanity filter) dan moderasi?',
                'a' => 'Akses menu Pengaturan > Tab Moderasi untuk memperbarui perbendaharaan kata terlarang. Riwayat siswa yang melanggar dapat dipantau di menu Notifikasi Pelanggaran.',
                'icon' => 'bi-shield-lock'
            ],
            [
                'q' => 'Bagaimana cara memantau dan mengeluarkan sesi login perangkat mencurigakan?',
                'a' => 'Buka Tab Riwayat Perangkat di Pengaturan. Anda dapat melihat detail IP, browser, status aktif, serta mengeluarkan perangkat secara individu atau sekaligus.',
                'icon' => 'bi-laptop'
            ],
            [
                'q' => 'Bagaimana proses sinkronisasi master data dengan SiPintu?',
                'a' => 'Gunakan modul Gateway SiPintu di sidebar untuk memeriksa status koneksi API dan menjalankan sinkronisasi data guru, siswa, dan rombel secara otomatis.',
                'icon' => 'bi-arrow-repeat'
            ],
        ];
    }
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
                <div id="collapseFaq{{ $index }}" class="accordion-collapse collapse" aria-labelledby="headingFaq{{ $index }}" data-bs-parent="#faqAccordionCustom">
                    <div class="accordion-body text-secondary small lh-base p-3.5 bg-white border-top">
                        {{ $faq['a'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
