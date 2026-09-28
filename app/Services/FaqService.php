<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Str;

class FaqService
{
    /**
     * Default FAQs jika belum dikustomisasi
     */
    public static function defaults(): array
    {
        return [
            // PUBLIK
            [
                'id' => 'faq_p1',
                'role' => 'publik',
                'q' => 'Apa itu platform GuruKuu?',
                'a' => 'GuruKuu adalah sistem evaluasi dan apresiasi kinerja pendidik berbasis web yang transparan, objektif, dan kredibel untuk kemajuan pembelajaran siswa di sekolah.',
                'icon' => 'bi-info-circle-fill'
            ],
            [
                'id' => 'faq_p2',
                'role' => 'publik',
                'q' => 'Bagaimana sistem menjaga kerahasiaan dan privasi penilaian siswa?',
                'a' => 'Sistem menjamin kerahasiaan 100% anonim bagi siswa. Identitas pengulas disamarkan dan dienkripsi sehingga guru maupun publik tidak dapat melihat siapa yang mengirimkan penilaian.',
                'icon' => 'bi-shield-check'
            ],
            [
                'id' => 'faq_p3',
                'role' => 'publik',
                'q' => 'Bagaimana cara melihat profil dan capaian apresiasi guru?',
                'a' => 'Pengunjung dapat meninjau capaian bintang, persentase kepuasan, dan ulasan positif pada menu Leaderboard serta halaman detail profil masing-masing guru.',
                'icon' => 'bi-trophy-fill'
            ],

            // SISWA
            [
                'id' => 'faq_s1',
                'role' => 'siswa',
                'q' => 'Apakah identitas dan nama saya benar-benar anonim saat menilai guru?',
                'a' => 'Ya, sistem GuruKuu menjamin 100% anonimitas bagi siswa. Nama, NIS, dan profil Anda tidak akan pernah ditampilkan kepada guru yang dinilai. Penilaian dan masukan Anda murni terdaftar secara objektif.',
                'icon' => 'bi-incognito'
            ],
            [
                'id' => 'faq_s2',
                'role' => 'siswa',
                'q' => 'Bagaimana jika saya keliru atau salah mengirimkan ulasan penilaian?',
                'a' => 'Demi menjaga integritas dan keaslian penilaian, evaluasi yang telah dikirim bersifat final. Apabila terjadi kendala teknis atau kekeliruan darurat, Anda dapat menghubungi Operator Sekolah melalui tab Hubungi Admin.',
                'icon' => 'bi-pencil-square'
            ],
            [
                'id' => 'faq_s3',
                'role' => 'siswa',
                'q' => 'Mengapa guru yang mengajar di kelas saya belum muncul di daftar penilaian?',
                'a' => 'Daftar guru disinkronkan otomatis berdasarkan rombongan belajar (kelas) Anda dan periode evaluasi yang aktif. Jika ada guru yang belum terdaftar, silakan sampaikan ke admin melalui pesan bantuan.',
                'icon' => 'bi-person-x'
            ],
            [
                'id' => 'faq_s4',
                'role' => 'siswa',
                'q' => 'Bagaimana cara mengganti kata sandi atau jika lupa password akun?',
                'a' => 'Penggantian dan reset kata sandi dikelola secara terpusat oleh pihak sekolah untuk mencegah penyalahgunaan akun. Silakan hubungi wali kelas atau Administrator Operator Sekolah.',
                'icon' => 'bi-key-fill'
            ],
            [
                'id' => 'faq_s5',
                'role' => 'siswa',
                'q' => 'Apakah ulasan yang mengandung kata tidak pantas akan terdeteksi?',
                'a' => 'Ya, sistem kami memiliki penyaring kata otomatis berteknologi cerdas. Ulasan kasar akan langsung disensor dan akun pelanggar dapat dikenakan sanksi suspensi dari sistem penilaian.',
                'icon' => 'bi-shield-exclamation'
            ],

            // GURU
            [
                'id' => 'faq_g1',
                'role' => 'guru',
                'q' => 'Apakah guru dapat melihat siapa siswa yang memberikan nilai atau ulasan?',
                'a' => 'Tidak. Seluruh identitas siswa disamarkan secara permanen demi objektivitas dan kenyamanan proses evaluasi pembelajaran.',
                'icon' => 'bi-incognito'
            ],
            [
                'id' => 'faq_g2',
                'role' => 'guru',
                'q' => 'Bagaimana cara memberikan tanggapan atau membalas masukan dari siswa?',
                'a' => 'Anda dapat membalas tanggapan siswa secara bijaksana dan edukatif melalui menu Ulasan dengan menekan tombol Balas pada ulasan yang bersangkutan.',
                'icon' => 'bi-chat-left-quote'
            ],
            [
                'id' => 'faq_g3',
                'role' => 'guru',
                'q' => 'Bagaimana jika ada ulasan siswa yang tidak pantas atau melanggar norma?',
                'a' => 'Sistem secara otomatis menyensor kata-kata kasar dan mencatat riwayat pelanggaran. Administrator sekolah secara berkala meninjau dan menindaklanjuti log pelanggaran tersebut.',
                'icon' => 'bi-shield-shaded'
            ],
            [
                'id' => 'faq_g4',
                'role' => 'guru',
                'q' => 'Kapan periode penilaian guru dibuka dan ditutup?',
                'a' => 'Jadwal dan periode penilaian ditentukan oleh Administrator Sekolah sesuai kalender evaluasi tengah semester atau akhir semester.',
                'icon' => 'bi-calendar-event'
            ],

            // ADMIN
            [
                'id' => 'faq_a1',
                'role' => 'admin',
                'q' => 'Bagaimana cara membuka atau menutup periode penilaian evaluasi?',
                'a' => 'Masuk ke menu Pengaturan > Tab Periode. Anda dapat menambahkan periode ajaran baru atau mengaktifkan/menonaktifkan periode yang sudah ada.',
                'icon' => 'bi-calendar-range'
            ],
            [
                'id' => 'faq_a2',
                'role' => 'admin',
                'q' => 'Bagaimana cara mengelola kata sensor (profanity filter) dan moderasi?',
                'a' => 'Akses menu Pengaturan > Tab Moderasi untuk memperbarui perbendaharaan kata terlarang. Riwayat siswa yang melanggar dapat dipantau di menu Notifikasi Pelanggaran.',
                'icon' => 'bi-shield-lock'
            ],
            [
                'id' => 'faq_a3',
                'role' => 'admin',
                'q' => 'Bagaimana cara memantau dan mengeluarkan sesi login perangkat mencurigakan?',
                'a' => 'Buka Tab Riwayat Perangkat di Pengaturan. Anda dapat melihat detail IP, browser, status aktif, serta mengeluarkan perangkat secara individu atau sekaligus.',
                'icon' => 'bi-laptop'
            ],
            [
                'id' => 'faq_a4',
                'role' => 'admin',
                'q' => 'Bagaimana proses sinkronisasi master data dengan SiPintu?',
                'a' => 'Gunakan modul Gateway SiPintu di sidebar untuk memeriksa status koneksi API dan menjalankan sinkronisasi data guru, siswa, dan rombel secara otomatis.',
                'icon' => 'bi-arrow-repeat'
            ],
        ];
    }

    /**
     * Ambil seluruh FAQ yang tersimpan
     */
    public static function all(): array
    {
        $raw = Setting::get('site_faqs');
        if (!$raw) {
            return self::defaults();
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return self::defaults();
        }

        // Migrasikan 'semua' menjadi 'publik' secara transparan
        return array_map(function ($item) {
            if (($item['role'] ?? '') === 'semua') {
                $item['role'] = 'publik';
            }
            return $item;
        }, $decoded);
    }

    /**
     * Ambil FAQ yang sesuai dengan role target ('publik', 'siswa', 'guru', 'admin')
     */
    public static function getForRole(string $role): array
    {
        $all = self::all();
        return array_values(array_filter($all, function ($item) use ($role) {
            $r = $item['role'] ?? 'publik';
            return $r === $role;
        }));
    }

    /**
     * Tambah FAQ baru
     */
    public static function add(array $data): array
    {
        $all = self::all();
        $role = $data['role'] ?? 'publik';
        if ($role === 'semua') $role = 'publik';
        if (!in_array($role, ['publik', 'siswa', 'guru', 'admin'])) {
            $role = 'publik';
        }

        $new = [
            'id' => 'faq_' . Str::random(8),
            'role' => $role,
            'q' => trim($data['q'] ?? ''),
            'a' => trim($data['a'] ?? ''),
            'icon' => !empty($data['icon']) ? trim($data['icon']) : 'bi-question-circle',
        ];

        $all[] = $new;
        Setting::set('site_faqs', json_encode($all, JSON_UNESCAPED_UNICODE));
        return $new;
    }

    /**
     * Update FAQ yang sudah ada
     */
    public static function update(string $id, array $data): bool
    {
        $all = self::all();
        $found = false;

        foreach ($all as $k => $item) {
            if (($item['id'] ?? '') === $id) {
                $role = $data['role'] ?? $item['role'];
                if ($role === 'semua') $role = 'publik';
                $all[$k]['role'] = in_array($role, ['publik', 'siswa', 'guru', 'admin']) ? $role : $item['role'];
                $all[$k]['q'] = trim($data['q'] ?? $item['q']);
                $all[$k]['a'] = trim($data['a'] ?? $item['a']);
                if (isset($data['icon']) && trim($data['icon']) !== '') {
                    $all[$k]['icon'] = trim($data['icon']);
                }
                $found = true;
                break;
            }
        }

        if ($found) {
            Setting::set('site_faqs', json_encode($all, JSON_UNESCAPED_UNICODE));
        }

        return $found;
    }

    /**
     * Hapus FAQ
     */
    public static function delete(string $id): bool
    {
        $all = self::all();
        $new = array_values(array_filter($all, fn ($item) => ($item['id'] ?? '') !== $id));

        if (count($new) !== count($all)) {
            Setting::set('site_faqs', json_encode($new, JSON_UNESCAPED_UNICODE));
            return true;
        }

        return false;
    }

    /**
     * Reset ke FAQ bawaan
     */
    public static function resetToDefault(): void
    {
        Setting::set('site_faqs', json_encode(self::defaults(), JSON_UNESCAPED_UNICODE));
    }
}
