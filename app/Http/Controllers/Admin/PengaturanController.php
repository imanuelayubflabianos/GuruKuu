<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PengaturanController extends Controller
{
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();

        $settings = [
            'site_title'        => Setting::get('site_title', 'GuruKuu'),
            'site_logo'         => Setting::get('site_logo', ''),
            'hero_image'        => Setting::get('hero_image', 'https://images.unsplash.com/photo-1562774053-701939374585?w=1920'),
            'hero_title'        => Setting::get('hero_title', 'Bangun Sekolah yang Lebih Baik Melalui Penilaian Guru yang Objektif'),
            'hero_subtitle'     => Setting::get('hero_subtitle', 'Suarakan aspirasimu secara aman untuk meningkatkan kualitas pengajaran dan menciptakan lingkungan belajar yang inspiratif.'),
            'hero_cta_text'     => Setting::get('hero_cta_text', 'Siap Memulai?'),
            'hero_cta_url'      => Setting::get('hero_cta_url', '/login'),
            'visi_text'         => Setting::get('visi_text', 'Menjadi standar nasional dalam evaluasi pengajaran berbasis data untuk menciptakan ekosistem pendidikan yang responsif, transparan, dan berkelanjutan di seluruh SMK Indonesia.'),
            'misi_text'         => Setting::get('misi_text', "Memberikan saluran aspirasi yang aman dan anonim bagi siswa.\nMenyediakan data analitik yang dapat ditindaklanjuti oleh manajemen sekolah.\nMendorong pengembangan profesional guru secara berkelanjutan."),
            'footer_about'      => Setting::get('footer_about', 'Sistem Manajemen Penilaian Guru Berbasis Siswa untuk SMK unggulan di seluruh Indonesia.'),
            'footer_copyright'  => Setting::get('footer_copyright', 'All rights reserved.'),
            'kebijakan_privasi' => Setting::get('kebijakan_privasi', ''),
            'syarat_ketentuan'  => Setting::get('syarat_ketentuan', ''),
        ];

        $semuaPeriode = Periode::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();

        return view('admin.pengaturan.index', compact('periodeAktif', 'semuaPeriode', 'settings'));
    }

    public function updateLanding(Request $request)
    {
        $request->validate([
            'site_title'        => 'required|string|max:100',
            'site_logo_file'    => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'site_logo_url'     => 'nullable|string|max:1000',
            'hero_title'        => 'required|string|max:255',
            'hero_subtitle'     => 'required|string|max:1000',
            'hero_cta_text'     => 'required|string|max:50',
            'hero_image_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'hero_image_url'    => 'nullable|string|max:1000',
            'visi_text'         => 'nullable|string|max:2000',
            'misi_text'         => 'nullable|string|max:3000',
            'footer_about'      => 'nullable|string|max:1000',
            'footer_copyright'  => 'nullable|string|max:255',
            'kebijakan_privasi' => 'nullable|string',
            'syarat_ketentuan'  => 'nullable|string',
        ]);

        // 1. Logo
        if ($request->has('remove_logo') && $request->remove_logo == '1') {
            Setting::set('site_logo', '');
        } elseif ($request->hasFile('site_logo_file')) {
            $file = $request->file('site_logo_file');
            $uploadDir = public_path('uploads/logo');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            Setting::set('site_logo', asset('uploads/logo/' . $filename));
        } elseif ($request->filled('site_logo_url')) {
            Setting::set('site_logo', trim($request->site_logo_url));
        }

        // 2. Thumbnail Hero
        if ($request->hasFile('hero_image_file')) {
            $file = $request->file('hero_image_file');
            $uploadDir = public_path('uploads/hero');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            $filename = 'hero_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            Setting::set('hero_image', asset('uploads/hero/' . $filename));
        } elseif ($request->filled('hero_image_url')) {
            Setting::set('hero_image', trim($request->hero_image_url));
        }

        // 3. Teks & Konten Beranda
        Setting::set('site_title', trim($request->site_title));
        Setting::set('hero_title', trim($request->hero_title));
        Setting::set('hero_subtitle', trim($request->hero_subtitle));
        Setting::set('hero_cta_text', trim($request->hero_cta_text));
        if ($request->filled('hero_cta_url')) {
            Setting::set('hero_cta_url', trim($request->hero_cta_url));
        }

        if ($request->has('visi_text')) {
            Setting::set('visi_text', trim($request->visi_text));
        }
        if ($request->has('misi_text')) {
            Setting::set('misi_text', trim($request->misi_text));
        }
        if ($request->has('footer_about')) {
            Setting::set('footer_about', trim($request->footer_about));
        }
        if ($request->has('footer_copyright')) {
            Setting::set('footer_copyright', trim($request->footer_copyright));
        }
        if ($request->has('kebijakan_privasi')) {
            Setting::set('kebijakan_privasi', trim($request->kebijakan_privasi));
        }
        if ($request->has('syarat_ketentuan')) {
            Setting::set('syarat_ketentuan', trim($request->syarat_ketentuan));
        }

        return back()->with('success', 'Seluruh konten dan identitas tampilan beranda berhasil diperbarui!');
    }

    public function resetLandingHero()
    {
        Setting::set('site_title', 'GuruKuu');
        Setting::set('site_logo', '');
        Setting::set('hero_image', 'https://images.unsplash.com/photo-1562774053-701939374585?w=1920');
        Setting::set('hero_title', 'Bangun Sekolah yang Lebih Baik Melalui Penilaian Guru yang Objektif');
        Setting::set('hero_subtitle', 'Suarakan aspirasimu secara aman untuk meningkatkan kualitas pengajaran dan menciptakan lingkungan belajar yang inspiratif.');
        Setting::set('hero_cta_text', 'Siap Memulai?');
        Setting::set('hero_cta_url', '/login');
        Setting::set('visi_text', 'Menjadi standar nasional dalam evaluasi pengajaran berbasis data untuk menciptakan ekosistem pendidikan yang responsif, transparan, dan berkelanjutan di seluruh SMK Indonesia.');
        Setting::set('misi_text', "Memberikan saluran aspirasi yang aman dan anonim bagi siswa.\nMenyediakan data analitik yang dapat ditindaklanjuti oleh manajemen sekolah.\nMendorong pengembangan profesional guru secara berkelanjutan.");
        Setting::set('footer_about', 'Sistem Manajemen Penilaian Guru Berbasis Siswa untuk SMK unggulan di seluruh Indonesia.');
        Setting::set('footer_copyright', 'All rights reserved.');
        Setting::set('kebijakan_privasi', '');
        Setting::set('syarat_ketentuan', '');

        return back()->with('success', 'Tampilan dan konten beranda berhasil direset ke pengaturan standar bawaan!');
    }

    public function reset(Request $request)
    {
        Penilaian::truncate();
        Guru::query()->update([
            'rata_rata_nilai' => 0,
            'total_penilaian' => 0,
        ]);

        return back()->with('success', 'Seluruh data penilaian siswa berhasil direset.');
    }

    public function gantiPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->with('error', 'Gagal memperbarui password: Password lama salah.');
        }

        auth()->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success', 'Password akun Admin berhasil diperbarui!');
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