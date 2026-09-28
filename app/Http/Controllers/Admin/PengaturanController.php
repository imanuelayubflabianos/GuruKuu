<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PengaturanController extends Controller
{
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();

        $settings = [
            'site_title'        => Setting::get('site_title', 'GuruKuu'),
            'site_title_part1'  => Setting::get('site_title_part1', 'Guru'),
            'site_title_part2'  => Setting::get('site_title_part2', 'Kuu'),
            'site_title_color1' => Setting::get('site_title_color1', '#003366'),
            'site_title_color2' => Setting::get('site_title_color2', '#FFC107'),
            'site_logo'         => Setting::get('site_logo', ''),

            // Hero Slide 1 (Utama)
            'hero_badge'        => Setting::get('hero_badge', 'SMK NEGERI 1 BANGSRI • JUARA'),
            'hero_image'        => Setting::get('hero_image', 'https://images.unsplash.com/photo-1562774053-701939374585?w=1920'),
            'hero_title'        => Setting::get('hero_title', 'Bangun Sekolah yang Lebih Baik Melalui Penilaian Guru yang Objektif'),
            'hero_subtitle'     => Setting::get('hero_subtitle', 'Suarakan aspirasimu secara aman untuk meningkatkan kualitas pengajaran dan menciptakan lingkungan belajar yang inspiratif.'),
            'hero_cta_text'     => Setting::get('hero_cta_text', 'Siap Memulai?'),
            'hero_cta_url'      => Setting::get('hero_cta_url', '/login'),

            // Hero Slide 2
            'hero_image_2'      => Setting::get('hero_image_2', ''),
            'hero_title_2'      => Setting::get('hero_title_2', 'Suara Siswa untuk Kemajuan Bersama'),
            'hero_subtitle_2'   => Setting::get('hero_subtitle_2', 'Memberikan apresiasi dan masukan konstruktif bagi para pendidik SMK Negeri 1 Bangsri.'),
            'hero_cta_text_2'   => Setting::get('hero_cta_text_2', 'Lihat Leaderboard'),
            'hero_cta_url_2'    => Setting::get('hero_cta_url_2', '/leaderboard'),

            // Hero Slide 3
            'hero_image_3'      => Setting::get('hero_image_3', ''),
            'hero_title_3'      => Setting::get('hero_title_3', 'Anonim, Terpercaya, dan Terbuka'),
            'hero_subtitle_3'   => Setting::get('hero_subtitle_3', 'Seluruh data evaluasi terenkripsi aman untuk menjaga objektivitas dan kejujuran penilaian.'),
            'hero_cta_text_3'   => Setting::get('hero_cta_text_3', 'Pelajari Panduan'),
            'hero_cta_url_3'    => Setting::get('hero_cta_url_3', '/#panduan'),

            // Statistik Section
            'stats_label'       => Setting::get('stats_label', 'DATA SEKOLAH'),
            'stats_title'       => Setting::get('stats_title', 'Sekolah Kami dalam Angka'),
            'stats_subtitle'    => Setting::get('stats_subtitle', 'Statistik real-time dari sistem penilaian kinerja GuruKuu'),

            // Leaderboard Section
            'leaderboard_label'    => Setting::get('leaderboard_label', 'PENCAPAIAN TERBAIK'),
            'leaderboard_title'    => Setting::get('leaderboard_title', 'Guru dengan Partisipasi Tertinggi'),
            'leaderboard_subtitle' => Setting::get('leaderboard_subtitle', 'Guru dengan persentase kepuasan dan partisipasi penilaian tertinggi dari siswa'),

            // Panduan Section
            'panduan_label'        => Setting::get('panduan_label', 'PANDUAN PENGGUNAAN'),
            'panduan_title'        => Setting::get('panduan_title', 'Bagaimana Cara Memberi Penilaian?'),
            'panduan_subtitle'     => Setting::get('panduan_subtitle', 'Hanya butuh 3 langkah mudah untuk berkontribusi bagi sekolahmu'),
            'panduan_step1_title'  => Setting::get('panduan_step1_title', 'Login NIS'),
            'panduan_step1_desc'   => Setting::get('panduan_step1_desc', 'Masuk dengan akun NIS & tanggal lahir resmi terverifikasi.'),
            'panduan_step2_title'  => Setting::get('panduan_step2_title', 'Beri Nilai'),
            'panduan_step2_desc'   => Setting::get('panduan_step2_desc', 'Pilih guru Normada/Produktif, beri nilai (1-5) pada 5 kriteria.'),
            'panduan_step3_title'  => Setting::get('panduan_step3_title', 'Kirim Anonim'),
            'panduan_step3_desc'   => Setting::get('panduan_step3_desc', 'Data tersimpan aman & anonim untuk perbaikan pengajaran.'),

            // Tentang & Profil Section
            'about_label'       => Setting::get('about_label', 'TENTANG KAMI'),
            'about_title'       => Setting::get('about_title', 'Mengapa GuruKuu Ada?'),
            'about_subtitle'    => Setting::get('about_subtitle', 'Platform evaluasi terintegrasi untuk SMK yang membangun jembatan komunikasi positif antara siswa, guru, dan manajemen sekolah.'),
            'visi_text'         => Setting::get('visi_text', 'Menjadi standar nasional dalam evaluasi pengajaran berbasis data untuk menciptakan ekosistem pendidikan yang responsif, transparan, dan berkelanjutan di seluruh SMK Indonesia.'),
            'misi_text'         => Setting::get('misi_text', "Memberikan saluran aspirasi yang aman dan anonim bagi siswa.\nMenyediakan data analitik yang dapat ditindaklanjuti oleh manajemen sekolah.\nMendorong pengembangan profesional guru secara berkelanjutan."),

            // 3 Fitur Unggulan
            'feature1_title'    => Setting::get('feature1_title', 'Anonimitas'),
            'feature1_desc'     => Setting::get('feature1_desc', 'Identitas siswa aman dengan enkripsi tanpa tekanan.'),
            'feature2_title'    => Setting::get('feature2_title', 'Berbasis Data'),
            'feature2_desc'     => Setting::get('feature2_desc', 'Data statistik valid & terukur untuk setiap apresiasi.'),
            'feature3_title'    => Setting::get('feature3_title', 'Kolaboratif'),
            'feature3_desc'     => Setting::get('feature3_desc', 'Membangun komunikasi positif siswa, guru, & sekolah.'),

            // Footer & Legal
            'footer_about'      => Setting::get('footer_about', 'Sistem Manajemen Penilaian Guru Berbasis Siswa untuk SMK N 1 Bangsri.'),
            'footer_copyright'  => Setting::get('footer_copyright', '© ' . date('Y') . ' GuruKuu. All rights reserved.'),
            'kebijakan_privasi' => Setting::get('kebijakan_privasi', "1. Pengumpulan Data\nKami hanya mengumpulkan data yang diperlukan untuk proses penilaian, yaitu NIS, nama, dan kelas siswa. Data pribadi seperti tanggal lahir hanya digunakan untuk verifikasi identitas saat login.\n\n2. Anonimitas Penilaian\nSeluruh penilaian yang diberikan siswa bersifat anonim. Guru dan pihak lain tidak dapat mengetahui identitas siswa yang memberikan nilai tertentu. Ini menjamin kejujuran dan objektivitas dalam setiap penilaian.\n\n3. Penyimpanan Data\nSemua data disimpan di server yang aman dengan enkripsi standar industri. Password pengguna di-hash menggunakan algoritma bcrypt yang tidak dapat dibaca kembali.\n\n4. Penggunaan Data\nData penilaian hanya digunakan untuk keperluan internal sekolah, seperti evaluasi kinerja guru dan pengambilan keputusan oleh manajemen. Data tidak akan dibagikan kepada pihak ketiga tanpa persetujuan."),
            'syarat_ketentuan'  => Setting::get('syarat_ketentuan', "1. Eligibilitas\nPlatform ini hanya dapat digunakan oleh siswa dan guru yang terdaftar resmi di sekolah. Akun harus diaktifkan oleh administrator sekolah sebelum dapat digunakan.\n\n2. Tanggung Jawab Pengguna\nSiswa wajib memberikan penilaian secara jujur dan objektif. Dilarang memberikan penilaian berdasarkan dendam pribadi, SARA, atau konten yang tidak pantas.\n\n3. Keamanan Akun\nPengguna bertanggung jawab penuh atas kerahasiaan password akun mereka. Dilarang membagikan password kepada orang lain.\n\n4. Kontak & Pengaduan\nJika Anda menemukan pelanggaran atau memiliki keluhan, silakan hubungi administrator sekolah melalui fitur Chat Admin yang tersedia di footer website ini."),
            'profanity_words'   => Setting::get('profanity_words', ''),
        ];

        // Moderasi data kata
        $defaultBadWords = \App\Services\ProfanityFilterService::getDefaultBadWords();
        $customBadWords = \App\Services\ProfanityFilterService::getCustomBadWords();
        $allBadWords = \App\Services\ProfanityFilterService::getBadWords();

        // FAQ data
        $faqs = \App\Services\FaqService::all();

        $semuaPeriode = Periode::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();
        $jumlahPenilaian = Penilaian::count();

        return view('admin.pengaturan.index', compact(
            'periodeAktif',
            'semuaPeriode',
            'jumlahPenilaian',
            'settings',
            'defaultBadWords',
            'customBadWords',
            'allBadWords',
            'faqs'
        ));
    }

    public function updateLanding(Request $request)
    {
        $request->validate([
            'site_title'        => 'nullable|string|max:100',
            'site_title_part1'  => 'nullable|string|max:50',
            'site_title_part2'  => 'nullable|string|max:50',
            'site_title_color1' => 'nullable|string|max:20',
            'site_title_color2' => 'nullable|string|max:20',
            'site_logo_file'    => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:10240',
            'site_logo_url'     => 'nullable|string|max:1000',
            
            // Hero Slide 1
            'hero_badge'        => 'nullable|string|max:100',
            'hero_title'        => 'nullable|string|max:255',
            'hero_subtitle'     => 'nullable|string|max:1000',
            'hero_cta_text'     => 'nullable|string|max:50',
            'hero_cta_url'      => 'nullable|string|max:255',
            'hero_image_file'   => 'nullable|file|mimes:jpeg,png,jpg,webp|max:15360',
            'hero_image_url'    => 'nullable|string|max:1000',

            // Hero Slide 2
            'hero_title_2'      => 'nullable|string|max:255',
            'hero_subtitle_2'   => 'nullable|string|max:1000',
            'hero_cta_text_2'   => 'nullable|string|max:50',
            'hero_cta_url_2'    => 'nullable|string|max:255',
            'hero_image_file_2' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:15360',
            'hero_image_url_2'  => 'nullable|string|max:1000',

            // Hero Slide 3
            'hero_title_3'      => 'nullable|string|max:255',
            'hero_subtitle_3'   => 'nullable|string|max:1000',
            'hero_cta_text_3'   => 'nullable|string|max:50',
            'hero_cta_url_3'    => 'nullable|string|max:255',
            'hero_image_file_3' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:15360',
            'hero_image_url_3'  => 'nullable|string|max:1000',

            // Sections
            'stats_label'       => 'nullable|string|max:50',
            'stats_title'       => 'nullable|string|max:150',
            'stats_subtitle'    => 'nullable|string|max:255',

            'leaderboard_label'    => 'nullable|string|max:50',
            'leaderboard_title'    => 'nullable|string|max:150',
            'leaderboard_subtitle' => 'nullable|string|max:255',

            'panduan_label'        => 'nullable|string|max:50',
            'panduan_title'        => 'nullable|string|max:150',
            'panduan_subtitle'     => 'nullable|string|max:255',
            'panduan_step1_title'  => 'nullable|string|max:100',
            'panduan_step1_desc'   => 'nullable|string|max:255',
            'panduan_step2_title'  => 'nullable|string|max:100',
            'panduan_step2_desc'   => 'nullable|string|max:255',
            'panduan_step3_title'  => 'nullable|string|max:100',
            'panduan_step3_desc'   => 'nullable|string|max:255',

            'about_label'       => 'nullable|string|max:50',
            'about_title'       => 'nullable|string|max:150',
            'about_subtitle'    => 'nullable|string|max:255',
            'visi_text'         => 'nullable|string|max:2000',
            'misi_text'         => 'nullable|string|max:3000',
            'feature1_title'    => 'nullable|string|max:100',
            'feature1_desc'     => 'nullable|string|max:255',
            'feature2_title'    => 'nullable|string|max:100',
            'feature2_desc'     => 'nullable|string|max:255',
            'feature3_title'    => 'nullable|string|max:100',
            'feature3_desc'     => 'nullable|string|max:255',

            'footer_about'      => 'nullable|string|max:1000',
            'footer_copyright'  => 'nullable|string|max:255',
            'kebijakan_privasi' => 'nullable|string',
            'syarat_ketentuan'  => 'nullable|string',
            'profanity_words'   => 'nullable|string|max:10000',
        ], [
            'site_logo_file.mimes' => 'Format file logo harus berupa JPG, PNG, WEBP, atau SVG.',
            'site_logo_file.max'   => 'Ukuran file logo maksimal adalah 10 MB.',
            'hero_image_file.mimes'=> 'Format file banner hero slide 1 harus berupa JPG, PNG, atau WEBP.',
            'hero_image_file.max'  => 'Ukuran file banner hero slide 1 maksimal adalah 15 MB.',
            'hero_image_file_2.mimes'=> 'Format file banner hero slide 2 harus berupa JPG, PNG, atau WEBP.',
            'hero_image_file_2.max'  => 'Ukuran file banner hero slide 2 maksimal adalah 15 MB.',
            'hero_image_file_3.mimes'=> 'Format file banner hero slide 3 harus berupa JPG, PNG, atau WEBP.',
            'hero_image_file_3.max'  => 'Ukuran file banner hero slide 3 maksimal adalah 15 MB.',
        ]);

        // Helper untuk upload gambar
        $handleUpload = function ($fileInputName, $subDir, $prefix) use ($request) {
            if (!$request->hasFile($fileInputName)) {
                return null;
            }
            $file = $request->file($fileInputName);
            $ext = strtolower($file->guessExtension() ?? 'jpg');
            if (!in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'svg'])) {
                $ext = 'jpg';
            }
            $filename = $prefix . '_' . \Illuminate\Support\Str::random(24) . '.' . $ext;
            Storage::disk('public')->putFileAs('uploads/' . $subDir, $file, $filename);

            try {
                $publicDir = public_path('uploads/' . $subDir);
                if (!File::exists($publicDir)) {
                    @File::makeDirectory($publicDir, 0775, true, true);
                }
                $src = storage_path('app/public/uploads/' . $subDir . '/' . $filename);
                if (File::exists($src) && is_dir($publicDir) && is_writable($publicDir)) {
                    @copy($src, $publicDir . DIRECTORY_SEPARATOR . $filename);
                }
            } catch (\Throwable $e) {}

            return '/uploads/' . $subDir . '/' . $filename;
        };

        // Helper untuk menghapus file lama agar tidak menumpuk di server
        $deleteOldFile = function ($oldPath) {
            if (empty($oldPath)) return;
            $cleanPath = ltrim(parse_url($oldPath, PHP_URL_PATH) ?? $oldPath, '/');
            if (str_starts_with($cleanPath, 'uploads/')) {
                $publicFile = public_path($cleanPath);
                if (File::exists($publicFile) && is_file($publicFile)) {
                    @unlink($publicFile);
                }
                $storageFile = storage_path('app/public/' . $cleanPath);
                if (File::exists($storageFile) && is_file($storageFile)) {
                    @unlink($storageFile);
                }
            }
        };

        // 1. Logo
        $oldLogo = Setting::get('site_logo');
        if ($request->has('remove_logo') && $request->remove_logo == '1') {
            $deleteOldFile($oldLogo);
            Setting::set('site_logo', '');
        } elseif ($logoPath = $handleUpload('site_logo_file', 'logo', 'logo')) {
            $deleteOldFile($oldLogo);
            Setting::set('site_logo', $logoPath);
        } elseif ($request->filled('site_logo_url')) {
            if ($oldLogo && $oldLogo !== trim($request->site_logo_url)) {
                $deleteOldFile($oldLogo);
            }
            Setting::set('site_logo', trim($request->site_logo_url));
        }

        // 2. Hero Slide 1
        $oldHero1 = Setting::get('hero_image');
        if ($hero1Path = $handleUpload('hero_image_file', 'hero', 'hero_1')) {
            $deleteOldFile($oldHero1);
            Setting::set('hero_image', $hero1Path);
        } elseif ($request->filled('hero_image_url')) {
            if ($oldHero1 && $oldHero1 !== trim($request->hero_image_url)) {
                $deleteOldFile($oldHero1);
            }
            Setting::set('hero_image', trim($request->hero_image_url));
        }

        // 3. Hero Slide 2
        $oldHero2 = Setting::get('hero_image_2');
        if ($request->has('remove_hero_2') && $request->remove_hero_2 == '1') {
            $deleteOldFile($oldHero2);
            Setting::set('hero_image_2', '');
        } elseif ($hero2Path = $handleUpload('hero_image_file_2', 'hero', 'hero_2')) {
            $deleteOldFile($oldHero2);
            Setting::set('hero_image_2', $hero2Path);
        } elseif ($request->filled('hero_image_url_2')) {
            if ($oldHero2 && $oldHero2 !== trim($request->hero_image_url_2)) {
                $deleteOldFile($oldHero2);
            }
            Setting::set('hero_image_2', trim($request->hero_image_url_2));
        }

        // 4. Hero Slide 3
        $oldHero3 = Setting::get('hero_image_3');
        if ($request->has('remove_hero_3') && $request->remove_hero_3 == '1') {
            $deleteOldFile($oldHero3);
            Setting::set('hero_image_3', '');
        } elseif ($hero3Path = $handleUpload('hero_image_file_3', 'hero', 'hero_3')) {
            $deleteOldFile($oldHero3);
            Setting::set('hero_image_3', $hero3Path);
        } elseif ($request->filled('hero_image_url_3')) {
            if ($oldHero3 && $oldHero3 !== trim($request->hero_image_url_3)) {
                $deleteOldFile($oldHero3);
            }
            Setting::set('hero_image_3', trim($request->hero_image_url_3));
        }

        // Teks Hero Slides
        $simpleFields = [
            'site_title', 'site_title_part1', 'site_title_part2', 'site_title_color1', 'site_title_color2',
            'hero_badge', 'hero_title', 'hero_subtitle', 'hero_cta_text', 'hero_cta_url',
            'hero_title_2', 'hero_subtitle_2', 'hero_cta_text_2', 'hero_cta_url_2',
            'hero_title_3', 'hero_subtitle_3', 'hero_cta_text_3', 'hero_cta_url_3',
            'stats_label', 'stats_title', 'stats_subtitle',
            'leaderboard_label', 'leaderboard_title', 'leaderboard_subtitle',
            'panduan_label', 'panduan_title', 'panduan_subtitle',
            'panduan_step1_title', 'panduan_step1_desc',
            'panduan_step2_title', 'panduan_step2_desc',
            'panduan_step3_title', 'panduan_step3_desc',
            'about_label', 'about_title', 'about_subtitle',
            'visi_text', 'misi_text',
            'feature1_title', 'feature1_desc',
            'feature2_title', 'feature2_desc',
            'feature3_title', 'feature3_desc',
            'footer_about', 'footer_copyright',
            'kebijakan_privasi', 'syarat_ketentuan'
        ];

        foreach ($simpleFields as $field) {
            if ($request->has($field)) {
                Setting::set($field, trim((string)$request->input($field)));
            }
        }

        if ($request->filled('profanity_words')) {
            $existing = collect(preg_split('/[\r\n,]+/', (string) Setting::get('profanity_words', ''), -1, PREG_SPLIT_NO_EMPTY));
            $new = collect(preg_split('/[\r\n,]+/', (string) $request->profanity_words, -1, PREG_SPLIT_NO_EMPTY));
            $words = $existing->concat($new)
                ->map(fn ($word) => mb_strtolower(trim($word), 'UTF-8'))
                ->filter(fn ($word) => mb_strlen($word) >= 2)
                ->unique()
                ->values()
                ->implode("\n");
            Setting::set('profanity_words', $words);

            // Jalankan sensor retroaktif untuk pesan-pesan lama di database
            \App\Services\ProfanityFilterService::retroactiveFilter();
        }

        // Bersihkan cache aplikasi & view
        try {
            \Illuminate\Support\Facades\Cache::flush();
            \Illuminate\Support\Facades\Artisan::call('view:clear');
        } catch (\Throwable $e) {}

        $tab = $request->input('active_tab', '#tabBrand');
        return redirect()->to(route('admin.pengaturan.index') . $tab)
            ->with('success', 'Seluruh konten dan konfigurasi publik berhasil diperbarui, cache diperbarui!');
    }

    public function updateProfanityWords(Request $request)
    {
        $request->validate(['profanity_words' => 'nullable|string|max:10000']);

        if ($request->filled('profanity_words')) {
            $existing = collect(preg_split('/[\r\n,]+/', (string) Setting::get('profanity_words', ''), -1, PREG_SPLIT_NO_EMPTY));
            $new = collect(preg_split('/[\r\n,]+/', (string) $request->profanity_words, -1, PREG_SPLIT_NO_EMPTY));
            $words = $existing->concat($new)
                ->map(fn ($word) => mb_strtolower(trim($word), 'UTF-8'))
                ->filter(fn ($word) => mb_strlen($word) >= 2)
                ->unique()
                ->values()
                ->implode("\n");

            Setting::set('profanity_words', $words);

            // Sensor retroaktif untuk ulasan dan pesan lama di database
            \App\Services\ProfanityFilterService::retroactiveFilter();
        }

        // Bersihkan cache aplikasi & view
        try {
            \Illuminate\Support\Facades\Cache::flush();
            \Illuminate\Support\Facades\Artisan::call('view:clear');
        } catch (\Throwable $e) {}

        return redirect()->to(route('admin.pengaturan.index') . '#tabModerasi')
            ->with('success', 'Kata terlarang berhasil disimpan dan pesan lama yang mengandung kata tersebut telah otomatis disensor!');
    }

    public function deleteProfanityWord(Request $request)
    {
        $wordToDelete = mb_strtolower(trim((string)$request->input('word')), 'UTF-8');
        if ($wordToDelete !== '') {
            $existing = collect(preg_split('/[\r\n,]+/', (string) Setting::get('profanity_words', ''), -1, PREG_SPLIT_NO_EMPTY))
                ->map(fn ($w) => mb_strtolower(trim($w), 'UTF-8'))
                ->filter(fn ($w) => $w !== $wordToDelete && mb_strlen($w) >= 2)
                ->unique()
                ->values()
                ->implode("\n");
            Setting::set('profanity_words', $existing);
        }

        return redirect()->to(route('admin.pengaturan.index') . '#tabModerasi')
            ->with('success', "Kata '{$wordToDelete}' berhasil dihapus dari daftar kustom!");
    }

    // FAQ Management
    public function storeFaq(Request $request)
    {
        $request->validate([
            'q' => 'required|string|max:255',
            'a' => 'required|string|max:2000',
            'role' => 'required|in:publik,siswa,guru,admin',
            'icon' => 'nullable|string|max:50',
        ]);

        \App\Services\FaqService::add([
            'q' => $request->q,
            'a' => $request->a,
            'role' => $request->role,
            'icon' => $request->icon ?: 'bi-question-circle',
        ]);

        return redirect()->to(route('admin.pengaturan.index') . '#tabFaq')
            ->with('success', 'FAQ baru berhasil ditambahkan!');
    }

    public function updateFaq(Request $request, string $id)
    {
        $request->validate([
            'q' => 'required|string|max:255',
            'a' => 'required|string|max:2000',
            'role' => 'required|in:publik,siswa,guru,admin',
            'icon' => 'nullable|string|max:50',
        ]);

        \App\Services\FaqService::update($id, [
            'q' => $request->q,
            'a' => $request->a,
            'role' => $request->role,
            'icon' => $request->icon ?: 'bi-question-circle',
        ]);

        return redirect()->to(route('admin.pengaturan.index') . '#tabFaq')
            ->with('success', 'FAQ berhasil diperbarui!');
    }

    public function destroyFaq(string $id)
    {
        \App\Services\FaqService::delete($id);
        return redirect()->to(route('admin.pengaturan.index') . '#tabFaq')
            ->with('success', 'FAQ berhasil dihapus!');
    }

    public function resetFaq()
    {
        \App\Services\FaqService::resetToDefault();
        return redirect()->to(route('admin.pengaturan.index') . '#tabFaq')
            ->with('success', 'Seluruh FAQ berhasil dikembalikan ke standar awal!');
    }

    public function resetLandingHero()
    {
        // Hapus file lama jika ada sebelum reset
        $deleteOldFile = function ($oldPath) {
            if (empty($oldPath)) return;
            $cleanPath = ltrim(parse_url($oldPath, PHP_URL_PATH) ?? $oldPath, '/');
            if (str_starts_with($cleanPath, 'uploads/')) {
                $publicFile = public_path($cleanPath);
                if (File::exists($publicFile) && is_file($publicFile)) {
                    @unlink($publicFile);
                }
                $storageFile = storage_path('app/public/' . $cleanPath);
                if (File::exists($storageFile) && is_file($storageFile)) {
                    @unlink($storageFile);
                }
            }
        };

        $deleteOldFile(Setting::get('site_logo'));
        $deleteOldFile(Setting::get('hero_image'));
        $deleteOldFile(Setting::get('hero_image_2'));
        $deleteOldFile(Setting::get('hero_image_3'));

        Setting::set('site_title', 'GuruKuu');
        Setting::set('site_logo', '');
        Setting::set('hero_badge', 'SMK NEGERI 1 BANGSRI • JUARA');
        Setting::set('hero_image', 'https://images.unsplash.com/photo-1562774053-701939374585?w=1920');
        Setting::set('hero_title', 'Bangun Sekolah yang Lebih Baik Melalui Penilaian Guru yang Objektif');
        Setting::set('hero_subtitle', 'Suarakan aspirasimu secara aman untuk meningkatkan kualitas pengajaran dan menciptakan lingkungan belajar yang inspiratif.');
        Setting::set('hero_cta_text', 'Siap Memulai?');
        Setting::set('hero_cta_url', '/login');
        Setting::set('hero_image_2', '');
        Setting::set('hero_image_3', '');

        Setting::set('stats_label', 'DATA SEKOLAH');
        Setting::set('stats_title', 'Sekolah Kami dalam Angka');
        Setting::set('stats_subtitle', 'Statistik real-time dari sistem penilaian kinerja GuruKuu');

        Setting::set('leaderboard_label', 'PENCAPAIAN TERBAIK');
        Setting::set('leaderboard_title', 'Guru dengan Partisipasi Tertinggi');
        Setting::set('leaderboard_subtitle', 'Guru dengan persentase kepuasan dan partisipasi penilaian tertinggi dari siswa');

        Setting::set('panduan_label', 'PANDUAN PENGGUNAAN');
        Setting::set('panduan_title', 'Bagaimana Cara Memberi Penilaian?');
        Setting::set('panduan_subtitle', 'Hanya butuh 3 langkah mudah untuk berkontribusi bagi sekolahmu');
        Setting::set('panduan_step1_title', 'Login NIS');
        Setting::set('panduan_step1_desc', 'Masuk dengan akun NIS & tanggal lahir resmi terverifikasi.');
        Setting::set('panduan_step2_title', 'Beri Nilai');
        Setting::set('panduan_step2_desc', 'Pilih guru Normada/Produktif, beri nilai (1-5) pada 5 kriteria.');
        Setting::set('panduan_step3_title', 'Kirim Anonim');
        Setting::set('panduan_step3_desc', 'Data tersimpan aman & anonim untuk perbaikan pengajaran.');

        Setting::set('about_label', 'TENTANG KAMI');
        Setting::set('about_title', 'Mengapa GuruKuu Ada?');
        Setting::set('about_subtitle', 'Platform evaluasi terintegrasi untuk SMK yang membangun jembatan komunikasi positif antara siswa, guru, dan manajemen sekolah.');
        Setting::set('visi_text', 'Menjadi standar nasional dalam evaluasi pengajaran berbasis data untuk menciptakan ekosistem pendidikan yang responsif, transparan, dan berkelanjutan di seluruh SMK Indonesia.');
        Setting::set('misi_text', "Memberikan saluran aspirasi yang aman dan anonim bagi siswa.\nMenyediakan data analitik yang dapat ditindaklanjuti oleh manajemen sekolah.\nMendorong pengembangan profesional guru secara berkelanjutan.");
        Setting::set('feature1_title', 'Anonimitas');
        Setting::set('feature1_desc', 'Identitas siswa aman dengan enkripsi tanpa tekanan.');
        Setting::set('feature2_title', 'Berbasis Data');
        Setting::set('feature2_desc', 'Data statistik valid & terukur untuk setiap apresiasi.');
        Setting::set('feature3_title', 'Kolaboratif');
        Setting::set('feature3_desc', 'Membangun komunikasi positif siswa, guru, & sekolah.');

        Setting::set('footer_about', 'Sistem Manajemen Penilaian Guru Berbasis Siswa untuk SMK N 1 Bangsri.');
        Setting::set('footer_copyright', '© ' . date('Y') . ' GuruKuu. All rights reserved.');
        Setting::set('kebijakan_privasi', '');
        Setting::set('syarat_ketentuan', '');

        // Bersihkan cache aplikasi & view
        try {
            \Illuminate\Support\Facades\Cache::flush();
            \Illuminate\Support\Facades\Artisan::call('view:clear');
        } catch (\Throwable $e) {}

        return back()->with('success', 'Tampilan dan konten publik berhasil direset ke standar bawaan dan file lama telah dibersihkan!');
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_confirmation' => ['required', 'string', 'in:HAPUS PENILAIAN'],
            'reset_acknowledged' => ['accepted'],
            'reset_current_password' => ['required', 'string'],
        ], [
            'reset_confirmation.required' => 'Ketik HAPUS PENILAIAN untuk melanjutkan.',
            'reset_confirmation.in' => 'Teks konfirmasi tidak sesuai.',
            'reset_acknowledged.accepted' => 'Anda harus menyetujui peringatan penghapusan.',
            'reset_current_password.required' => 'Password administrator wajib diisi.',
        ]);

        $redirect = redirect()->to(route('admin.pengaturan.index') . '#tabAkun');

        if ($validator->fails()) {
            return $redirect->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->input('reset_current_password'), $request->user()->password)) {
            return $redirect
                ->withErrors(['reset_current_password' => 'Password administrator tidak sesuai.'])
                ->withInput();
        }

        try {
            DB::transaction(function () {
                // DELETE menghormati foreign key: balasan terhapus via cascade dan log
                // pelanggaran dipertahankan sebagai audit dengan penilaian_id menjadi null.
                Penilaian::query()->delete();

                Guru::query()->update([
                    'rata_rata_nilai' => 0,
                    'total_penilaian' => 0,
                ]);
            });
        } catch (\Throwable $e) {
            report($e);

            return $redirect->with('error', 'Reset tidak dapat diselesaikan. Tidak ada data yang diubah.');
        }

        return $redirect->with('success', 'Seluruh data penilaian telah dihapus. Log pelanggaran tetap tersimpan sebagai audit.');
    }

    public function gantiPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 6 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
        ]);

        /** @var \App\Models\User $user */
        $user = User::find(auth()->id());
        if (!$user) {
            return back()->with('error', 'Sesi pengguna tidak valid.');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->with('error', 'Gagal memperbarui password: Password lama salah.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password akun Admin berhasil diperbarui! Silakan gunakan password baru ini untuk login berikutnya.');
    }

    public function simpanPeriode(Request $request)
    {
        $request->validate([
            'nama_periode'    => 'required|string|max:255',
            'tahun_ajaran'    => 'required|string|max:20',
            'semester'        => 'required|in:ganjil,genap',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status'          => 'required|in:aktif,nonaktif',
        ]);

        $periodeId = $request->input('periode_id');
        if ($periodeId) {
            $periode = Periode::findOrFail($periodeId);
            $periode->update([
                'nama_periode'    => trim($request->nama_periode),
                'tahun_ajaran'    => trim($request->tahun_ajaran),
                'semester'        => $request->semester,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status'          => $request->status,
            ]);
        } else {
            $periode = Periode::create([
                'nama_periode'    => trim($request->nama_periode),
                'tahun_ajaran'    => trim($request->tahun_ajaran),
                'semester'        => $request->semester,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status'          => $request->status,
            ]);
        }

        if ($request->status === 'aktif') {
            Periode::where('id', '!=', $periode->id)->update(['status' => 'nonaktif']);
            Guru::recalculateAll($periode->id);
            $pesan = 'Periode ' . $periode->nama_periode . ' berhasil diaktifkan! Statistik leaderboard semester baru kini berjalan aktif (data periode sebelumnya tersimpan rapi sebagai histori).';
        } else {
            $pesan = 'Pengaturan periode ' . $periode->nama_periode . ' berhasil disimpan!';
        }

        return back()->with('success', $pesan);
    }

    public function aktifkanPeriode(Periode $periode)
    {
        Periode::where('id', '!=', $periode->id)->update(['status' => 'nonaktif']);
        $periode->update(['status' => 'aktif']);
        Guru::recalculateAll($periode->id);

        return back()->with('success', "Periode '{$periode->nama_periode}' sekarang aktif! Seluruh statistik & leaderboard telah disinkronkan ke periode ini.");
    }
}
