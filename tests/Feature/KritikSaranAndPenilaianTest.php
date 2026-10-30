<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KritikSaranAndPenilaianTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan periode aktif
        Periode::create([
            'nama_periode' => 'Semester Ganjil 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'status' => 'aktif',
            'tanggal_mulai' => now()->startOfYear(),
            'tanggal_selesai' => now()->endOfYear(),
        ]);
    }

    public function test_admin_can_see_all_votes_by_default_including_star_only_ratings()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Pengembangan Perangkat Lunak',
            'kode_jurusan' => 'PPLG',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'PPLG 1',
            'jurusan_id' => $jurusan->id,
            'tingkat' => 12,
            'jumlah_siswa' => 30,
        ]);

        $guru = Guru::create([
            'nama' => 'Pak Guru Test',
            'nip' => '198701012010011001',
            'kategori' => 'produktif',
            'jurusan_id' => $jurusan->id,
        ]);

        $periode = Periode::where('status', 'aktif')->first();

        $siswa1 = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $siswa2 = User::factory()->create(['role' => 'siswa', 'is_active' => true]);

        // Penilaian 1: Dengan ulasan teks
        Penilaian::create([
            'siswa_id' => $siswa1->id,
            'guru_id' => $guru->id,
            'periode_id' => $periode->id,
            'class_id' => $kelas->id,
            'kedisiplinan' => 5,
            'komunikasi' => 5,
            'tanggung_jawab' => 5,
            'kreativitas' => 5,
            'keramahan' => 5,
            'total_nilai' => 25,
            'kritik' => 'Guru sangat baik dan disiplin.',
            'saran' => 'Pertahankan cara mengajarnya.',
        ]);

        // Penilaian 2: Rating bintang saja tanpa kritik/saran
        Penilaian::create([
            'siswa_id' => $siswa2->id,
            'guru_id' => $guru->id,
            'periode_id' => $periode->id,
            'class_id' => $kelas->id,
            'kedisiplinan' => 4,
            'komunikasi' => 4,
            'tanggung_jawab' => 4,
            'kreativitas' => 4,
            'keramahan' => 4,
            'total_nilai' => 20,
            'kritik' => null,
            'saran' => null,
        ]);

        // Akses halaman Kritik & Saran oleh Admin
        $response = $this->actingAs($admin)->get(route('admin.kritik-saran.index'));
        $response->assertStatus(200);
        // Kedua penilaian harus masuk secara default (total 2 penilaian)
        $response->assertViewHas('feedbacks', function ($feedbacks) {
            return $feedbacks->total() === 2;
        });

        // Filter: Hanya ada teks
        $responseTeks = $this->actingAs($admin)->get(route('admin.kritik-saran.index', ['cakupan' => 'ada_teks']));
        $responseTeks->assertStatus(200);
        $responseTeks->assertViewHas('feedbacks', function ($feedbacks) {
            return $feedbacks->total() === 1;
        });

        // Filter: Hanya bintang
        $responseBintang = $this->actingAs($admin)->get(route('admin.kritik-saran.index', ['cakupan' => 'hanya_bintang']));
        $responseBintang->assertStatus(200);
        $responseBintang->assertViewHas('feedbacks', function ($feedbacks) {
            return $feedbacks->total() === 1;
        });
    }

    public function test_admin_can_filter_evaluations_by_class()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Akuntansi',
            'kode_jurusan' => 'AK',
        ]);

        $kelasA = Kelas::create(['nama_kelas' => 'AK 1', 'jurusan_id' => $jurusan->id, 'tingkat' => 10, 'jumlah_siswa' => 30]);
        $kelasB = Kelas::create(['nama_kelas' => 'AK 2', 'jurusan_id' => $jurusan->id, 'tingkat' => 10, 'jumlah_siswa' => 30]);

        $guru = Guru::create(['nama' => 'Bu Guru AK', 'nip' => '199001012015012001', 'kategori' => 'normada']);
        $periode = Periode::where('status', 'aktif')->first();

        $siswaA = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $siswaB = User::factory()->create(['role' => 'siswa', 'is_active' => true]);

        Penilaian::create([
            'siswa_id' => $siswaA->id,
            'guru_id' => $guru->id,
            'periode_id' => $periode->id,
            'class_id' => $kelasA->id,
            'kedisiplinan' => 5, 'komunikasi' => 5, 'tanggung_jawab' => 5, 'kreativitas' => 5, 'keramahan' => 5,
            'total_nilai' => 25,
        ]);

        Penilaian::create([
            'siswa_id' => $siswaB->id,
            'guru_id' => $guru->id,
            'periode_id' => $periode->id,
            'class_id' => $kelasB->id,
            'kedisiplinan' => 3, 'komunikasi' => 3, 'tanggung_jawab' => 3, 'kreativitas' => 3, 'keramahan' => 3,
            'total_nilai' => 15,
        ]);

        // Filter Kelas A
        $responseA = $this->actingAs($admin)->get(route('admin.kritik-saran.index', ['kelas_id' => $kelasA->id]));
        $responseA->assertStatus(200);
        $responseA->assertViewHas('feedbacks', function ($feedbacks) use ($kelasA) {
            return $feedbacks->total() === 1 && $feedbacks->first()->class_id === $kelasA->id;
        });

        // Filter Kelas B
        $responseB = $this->actingAs($admin)->get(route('admin.kritik-saran.index', ['kelas_id' => $kelasB->id]));
        $responseB->assertStatus(200);
        $responseB->assertViewHas('feedbacks', function ($feedbacks) use ($kelasB) {
            return $feedbacks->total() === 1 && $feedbacks->first()->class_id === $kelasB->id;
        });
    }

    public function test_siswa_voting_stores_class_id_and_prevents_duplicate_vote()
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'PPLG',
            'kode_jurusan' => 'PPLG',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'PPLG 1',
            'jurusan_id' => $jurusan->id,
            'tingkat' => 12,
            'jumlah_siswa' => 30,
        ]);

        $guru = Guru::create([
            'nama' => 'Pak Guru Hebat',
            'nip' => '198501012010011002',
            'kategori' => 'produktif',
            'jurusan_id' => $jurusan->id,
        ]);

        // Hubungkan guru dengan kelas
        $guru->kelas()->attach($kelas->id, ['mata_pelajaran' => 'Pemrograman']);

        $periode = Periode::where('status', 'aktif')->first();

        $siswa = User::factory()->create([
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $siswa->kelas()->attach($kelas->id, ['tahun_ajaran' => $periode->tahun_ajaran]);

        // Siswa submit vote pertama kali
        $response = $this->actingAs($siswa)->post(route('siswa.penilaian.store', $guru), [
            'kedisiplinan' => 5,
            'komunikasi' => 4,
            'tanggung_jawab' => 5,
            'kreativitas' => 5,
            'keramahan' => 4,
            'kritik' => 'Bagus sekali',
            'saran' => 'Semoga semakin sukses',
        ]);

        $response->assertRedirect(route('siswa.guru.show', $guru));
        $this->assertDatabaseHas('penilaian', [
            'siswa_id' => $siswa->id,
            'guru_id' => $guru->id,
            'class_id' => $kelas->id,
            'total_nilai' => 23,
        ]);

        // Siswa submit vote kedua kali (mencoba duplikat / concurrent double submit)
        $responseDuplikat = $this->actingAs($siswa)->post(route('siswa.penilaian.store', $guru), [
            'kedisiplinan' => 5,
            'komunikasi' => 5,
            'tanggung_jawab' => 5,
            'kreativitas' => 5,
            'keramahan' => 5,
        ]);

        // Harus dicegah secara graceful tanpa error 500
        $this->assertEquals(1, Penilaian::where('siswa_id', $siswa->id)->where('guru_id', $guru->id)->count());
    }

    public function test_guru_ulasan_page_hides_detail_class_and_only_shows_jurusan()
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Manajemen Perkantoran dan Layanan Bisnis',
            'kode_jurusan' => 'MPLB',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'MPLB 2',
            'jurusan_id' => $jurusan->id,
            'tingkat' => 11,
            'jumlah_siswa' => 35,
        ]);

        $guruUser = User::factory()->create([
            'role' => 'guru',
            'nis' => '199502272025212026',
            'name' => 'Fitria Diana Kumala Sari',
            'email' => 'fitria@smkn1bangsri.sch.id',
            'is_active' => true,
        ]);

        $guru = Guru::create([
            'nama' => 'Fitria Diana Kumala Sari',
            'nip' => '199502272025212026',
            'email' => 'fitria@smkn1bangsri.sch.id',
            'kategori' => 'produktif',
            'jurusan_id' => $jurusan->id,
        ]);

        $periode = Periode::where('status', 'aktif')->first();

        $siswa = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Rahasia Siswa',
            'nis' => '12345',
            'is_active' => true,
        ]);

        Penilaian::create([
            'siswa_id' => $siswa->id,
            'guru_id' => $guru->id,
            'periode_id' => $periode->id,
            'class_id' => $kelas->id,
            'kedisiplinan' => 5,
            'komunikasi' => 5,
            'tanggung_jawab' => 5,
            'kreativitas' => 5,
            'keramahan' => 4,
            'total_nilai' => 24,
            'kritik' => 'Kritik konstruktif',
            'saran' => 'Saran konstruktif',
        ]);

        // Login sebagai Guru dan akses halaman ulasan
        $response = $this->actingAs($guruUser)->get(route('guru.ulasan'));
        $response->assertStatus(200);

        // Harus menampilkan badge Siswa (Anonim) dan Jurusan MPLB
        $response->assertSee('Siswa (Anonim)');
        $response->assertSee('Jurusan MPLB');

        // TIDAK BOLEH membocorkan nama kelas detail (MPLB 2 Kelas 11) maupun identitas siswa
        $response->assertDontSee('MPLB 2 Kelas 11');
        $response->assertDontSee('Rahasia Siswa');
        $response->assertDontSee('12345');
    }

    public function test_user_leaderboard_caps_top_10_and_top_5_while_admin_sees_all()
    {
        $periode = Periode::where('status', 'aktif')->first();

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer',
            'kode_jurusan' => 'TKJ',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'TKJ 1',
            'jurusan_id' => $jurusan->id,
            'tingkat' => 10,
            'jumlah_siswa' => 40,
        ]);

        // Buat 32 Guru dengan penilaian eligible (masing-masing 5 penilaian siswa)
        $gurus = [];
        for ($i = 1; $i <= 32; $i++) {
            $g = Guru::create([
                'nama' => "Guru Nomor {$i}",
                'nip' => "1990000000000000" . str_pad($i, 2, '0', STR_PAD_LEFT),
                'kategori' => 'produktif',
                'jurusan_id' => $jurusan->id,
            ]);
            $g->kelas()->attach($kelas->id, ['mata_pelajaran' => "Mapel {$i}"]);

            // Buat 5 penilaian per guru agar eligible leaderboard
            for ($s = 1; $s <= 5; $s++) {
                $siswa = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
                Penilaian::create([
                    'siswa_id' => $siswa->id,
                    'guru_id' => $g->id,
                    'periode_id' => $periode->id,
                    'class_id' => $kelas->id,
                    'kedisiplinan' => min(5, max(1, 6 - $i * 0.1)),
                    'komunikasi' => min(5, max(1, 6 - $i * 0.1)),
                    'tanggung_jawab' => min(5, max(1, 6 - $i * 0.1)),
                    'kreativitas' => min(5, max(1, 6 - $i * 0.1)),
                    'keramahan' => min(5, max(1, 6 - $i * 0.1)),
                    'total_nilai' => max(5, 25 - $i),
                ]);
            }
            $gurus[] = $g;
        }

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $siswaUser = User::factory()->create(['role' => 'siswa', 'is_active' => true]);

        // 1. Cek Admin Leaderboard (Mode Semua Guru): Admin melihat semua 32 guru
        $resAdmin = $this->actingAs($admin)->get(route('admin.leaderboard.index', ['mode' => 'rating']));
        $resAdmin->assertStatus(200);
        $resAdmin->assertViewHas('leaderboard', function ($lb) {
            return $lb->count() === 32;
        });

        // 2. Cek Siswa Leaderboard (Mode Semua Guru): Siswa dibatasi Top 30 saja
        $resSiswaRating = $this->actingAs($siswaUser)->get(route('siswa.leaderboard.index', ['mode' => 'rating']));
        $resSiswaRating->assertStatus(200);
        $resSiswaRating->assertViewHas('leaderboard', function ($lb) {
            return $lb->count() === 30;
        });

        // 3. Cek Siswa Leaderboard (Mode Kelas): Siswa dibatasi Top 5 saja
        $resSiswaKelas = $this->actingAs($siswaUser)->get(route('siswa.leaderboard.index', [
            'mode' => 'partisipasi',
            'kelas_id' => $kelas->id
        ]));
        $resSiswaKelas->assertStatus(200);
        $resSiswaKelas->assertViewHas('leaderboard', function ($lb) {
            return $lb->count() === 5;
        });

        // 4. Guru Dashboard & Leaderboard: Menampilkan peringkat pribadi guru
        $targetGuru = $gurus[30]; // Guru Nomor 31 (di luar Top 30)
        $guruUser = User::factory()->create([
            'role' => 'guru',
            'nis' => $targetGuru->nip,
            'is_active' => true,
        ]);

        $resGuruDash = $this->actingAs($guruUser)->get(route('guru.dashboard'));
        $resGuruDash->assertStatus(200);
        $resGuruDash->assertSee('PERINGKAT PRIBADI ANDA');
        $resGuruDash->assertSee('Peringkat #31');

        $resGuruLb = $this->actingAs($guruUser)->get(route('guru.leaderboard', ['mode' => 'rating']));
        $resGuruLb->assertStatus(200);
        // Pada tabel umum dibatasi Top 30 guru
        $resGuruLb->assertViewHas('leaderboard', function ($lb) {
            return $lb->count() === 30;
        });
        // Namun di banner privat, guru ini tetap tahu dia peringkat #31
        $resGuruLb->assertSee('Peringkat #31');
        $resGuruLb->assertSee('STATUS PRIBADI ANDA');
    }

    public function test_guru_with_nip_zero_can_login_using_email_or_updated_nip()
    {
        Http::fake([
            '*/api/v1/auth/verify-credentials' => Http::response([
                'valid' => true,
                'status' => 'valid',
                'password_hash' => bcrypt('password123'),
            ], 200),
        ]);

        $guru = Guru::create([
            'nama' => 'Rosma azizah',
            'nip' => '0',
            'email' => 'emarosma20@gmail.com',
            'kategori' => 'normada',
        ]);

        $user = User::create([
            'name' => 'Rosma azizah',
            'nis' => '0',
            'email' => 'emarosma20@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'guru',
            'is_active' => true,
        ]);

        // 1. Login menggunakan email guru (karena NIP = 0)
        $resLoginEmail = $this->post(route('login.post'), [
            'login_role' => 'guru',
            'nis' => 'emarosma20@gmail.com',
            'password' => 'password123',
        ]);

        $resLoginEmail->assertRedirect(route('guru.dashboard'));
        $this->assertAuthenticatedAs($user);

        auth()->logout();

        // 2. Admin memperbarui NIP dari '0' menjadi NIP baru (misal NIP sementara 199801012025012001)
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $newNip = '199801012025012001';

        $resUpdate = $this->actingAs($admin)->put(route('admin.guru.update', $guru), [
            'nip' => $newNip,
            'nama' => 'Rosma azizah, S.Pd',
            'email' => 'emarosma20@gmail.com',
            'password' => 'secretbaru123',
        ]);

        $resUpdate->assertRedirect(route('admin.guru.index'));

        // Pastikan tabel guru & tabel users tersinkronisasi NIP barunya
        $this->assertDatabaseHas('guru', [
            'id' => $guru->id,
            'nip' => $newNip,
            'nama' => 'Rosma azizah, S.Pd',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nis' => $newNip,
            'name' => 'Rosma azizah, S.Pd',
        ]);

        auth()->logout();

        // 3. Guru dapat login menggunakan NIP barunya
        $resLoginNewNip = $this->post(route('login.post'), [
            'login_role' => 'guru',
            'nis' => $newNip,
            'password' => 'secretbaru123',
        ]);

        $resLoginNewNip->assertRedirect(route('guru.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_sipintu_sync_preserves_custom_nip_when_gateway_returns_zero()
    {
        $guru = Guru::create([
            'nama' => 'Rosma azizah',
            'nip' => '199801012025012001',
            'email' => 'emarosma20@gmail.com',
            'kategori' => 'normada',
        ]);

        $sipintu = app(\App\Services\SiPintuService::class);

        // Simulasi data incoming dari SiPintu yang NIP-nya masih '0'
        $incomingData = [
            'nip' => '0',
            'nama' => 'Rosma azizah',
            'email' => 'emarosma20@gmail.com',
            'hp' => '082298650743',
        ];

        $res = $sipintu->syncTeacherToLocal($incomingData);
        $this->assertTrue($res['success']);

        // Pastikan NIP tidak ditimpa kembali menjadi '0' dan tidak membuat duplikat
        $this->assertEquals(1, Guru::where('email', 'emarosma20@gmail.com')->count());
        $this->assertEquals('199801012025012001', Guru::where('email', 'emarosma20@gmail.com')->first()->nip);
    }

    public function test_search_finds_teacher_by_partial_or_tokenized_name_in_admin_and_siswa()
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer dan Jaringan',
            'kode_jurusan' => 'TKJ',
        ]);

        $guru = Guru::create([
            'nama' => 'M. Abdul Latif, S.Pd.',
            'nip' => '198901012020011002',
            'email' => 'abdullatif@smkn1bangsri.sch.id',
            'kategori' => 'normada',
            'jurusan_id' => $jurusan->id,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 1. Admin search "latif"
        $resAdminLatif = $this->actingAs($admin)->get(route('admin.guru.index', ['search' => 'latif']));
        $resAdminLatif->assertOk();
        $resAdminLatif->assertSee('Abdul Latif');

        // 2. Admin search multi-word "abdul latif"
        $resAdminMulti = $this->actingAs($admin)->get(route('admin.guru.index', ['search' => 'abdul latif']));
        $resAdminMulti->assertOk();
        $resAdminMulti->assertSee('Abdul Latif');

        // 3. Siswa search "latif" (meskipun Abdul Latif tidak mengajar kelas siswa)
        $siswa = User::factory()->create([
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'PPLG 1',
            'jurusan_id' => $jurusan->id,
            'tingkat' => 12,
            'jumlah_siswa' => 30,
        ]);
        $siswa->kelas()->attach($kelas->id, ['tahun_ajaran' => '2026/2027']);

        $resSiswaLatif = $this->actingAs($siswa)->get(route('siswa.guru.index', ['search' => 'latif']));
        $resSiswaLatif->assertOk();
        $resSiswaLatif->assertSee('Abdul Latif');
    }

    public function test_criteria_stars_and_percentages_render_in_guru_dashboard_ulasan_and_detail()
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Akuntansi',
            'kode_jurusan' => 'AKL',
        ]);

        $guru = Guru::create([
            'nama' => 'Ibu Guru Akuntansi',
            'nip' => '199201012022012003',
            'email' => 'guruakuntansi@smkn1bangsri.sch.id',
            'kategori' => 'produktif',
            'jurusan_id' => $jurusan->id,
        ]);

        $userGuru = User::factory()->create([
            'name' => 'Ibu Guru Akuntansi',
            'nis' => '199201012022012003',
            'email' => 'guruakuntansi@smkn1bangsri.sch.id',
            'role' => 'guru',
            'is_active' => true,
        ]);

        $periode = Periode::where('status', 'aktif')->first();

        $siswa = User::factory()->create([
            'role' => 'siswa',
            'is_active' => true,
        ]);

        // Buat penilaian dengan nilai spesifik (5, 4, 5, 4, 5)
        Penilaian::create([
            'siswa_id' => $siswa->id,
            'guru_id' => $guru->id,
            'periode_id' => $periode->id,
            'kedisiplinan' => 5,
            'tanggung_jawab' => 4,
            'komunikasi' => 5,
            'keramahan' => 4,
            'kreativitas' => 5,
            'total_nilai' => 23,
            'kritik' => 'Bagus sekali',
            'saran' => 'Pertahankan',
        ]);

        // 1. Cek di /guru/dashboard
        $resDash = $this->actingAs($userGuru)->get(route('guru.dashboard'));
        $resDash->assertOk();
        $resDash->assertSee('DETAIL KRITERIA EVALUASI');
        $resDash->assertSee('Ketepatan Waktu');
        $resDash->assertSee('Kehadiran di Kelas');

        // 2. Cek di /guru/ulasan
        $resUlasan = $this->actingAs($userGuru)->get(route('guru.ulasan'));
        $resUlasan->assertOk();
        $resUlasan->assertSee('Ringkasan Kriteria Evaluasi Siswa');
        $resUlasan->assertSee('Ketepatan Waktu');

        // 3. Cek di /guru/detail-guru/{id}
        $resDetail = $this->actingAs($userGuru)->get(route('guru.detail', $guru));
        $resDetail->assertOk();
        $resDetail->assertSee('SKOR KESELURUHAN:');
        $resDetail->assertSee('Ketepatan Waktu');
    }

    public function test_leaderboard_strict_period_isolation_and_class_ranking()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeLampau = Periode::create([
            'nama_periode' => 'Semester Genap 2025/2026',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Genap',
            'status' => 'nonaktif',
            'tanggal_mulai' => now()->subYear()->startOfYear(),
            'tanggal_selesai' => now()->subYear()->endOfYear(),
        ]);

        $guruA = Guru::create([
            'nama' => 'Guru Isolasi A',
            'nip' => '199111111111111101',
            'kategori' => 'normada',
        ]);
        $guruB = Guru::create([
            'nama' => 'Guru Isolasi B',
            'nip' => '199111111111111102',
            'kategori' => 'normada',
        ]);

        $jurusan = Jurusan::firstOrCreate(['kode_jurusan' => 'PPLG'], ['nama_jurusan' => 'PPLG']);
        $kelas = Kelas::create([
            'nama_kelas' => 'PPLG 1 Kelas 12',
            'tingkat' => 12,
            'jurusan_id' => $jurusan->id,
            'jumlah_siswa' => 36,
        ]);
        $guruA->kelas()->attach($kelas->id, ['mata_pelajaran' => 'PBO']);

        $siswa1 = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $siswa2 = User::factory()->create(['role' => 'siswa', 'is_active' => true]);

        // Penilaian Periode Lampau untuk Guru B (seharusnya TIDAK masuk leaderboard periode aktif)
        Penilaian::create([
            'siswa_id' => $siswa1->id,
            'guru_id' => $guruB->id,
            'periode_id' => $periodeLampau->id,
            'class_id' => $kelas->id,
            'kedisiplinan' => 5,
            'tanggung_jawab' => 5,
            'komunikasi' => 5,
            'keramahan' => 5,
            'kreativitas' => 5,
            'total_nilai' => 25,
        ]);

        // Penilaian Periode Aktif untuk Guru A (hanya 2 ulasan, < 5)
        Penilaian::create([
            'siswa_id' => $siswa1->id,
            'guru_id' => $guruA->id,
            'periode_id' => $periodeAktif->id,
            'class_id' => $kelas->id,
            'kedisiplinan' => 5,
            'tanggung_jawab' => 5,
            'komunikasi' => 5,
            'keramahan' => 5,
            'kreativitas' => 5,
            'total_nilai' => 25,
            'kritik' => 'Kritik konstruktif',
            'saran' => 'Saran baik',
        ]);
        Penilaian::create([
            'siswa_id' => $siswa2->id,
            'guru_id' => $guruA->id,
            'periode_id' => $periodeAktif->id,
            'class_id' => $kelas->id,
            'kedisiplinan' => 4,
            'tanggung_jawab' => 4,
            'komunikasi' => 4,
            'keramahan' => 4,
            'kreativitas' => 4,
            'total_nilai' => 20,
        ]);

        // 1. Leaderboard aktif: Guru B TIDAK boleh ada (karena di periode lampau)
        // Guru A juga TIDAK boleh ada pada mode rating karena ulasan baru 2 (< 5 ulasan wajib untuk Top 30 Sekolah)
        $lbAktif = Guru::leaderboardFor('rating', null, $periodeAktif->id);
        $this->assertFalse($lbAktif->contains('id', $guruB->id));
        $this->assertFalse($lbAktif->contains('id', $guruA->id));

        // 2. Leaderboard kelas: Guru A langsung tampil dan memiliki peringkat (#1) karena siswa dari kelas ini telah menilai
        $lbKelas = Guru::leaderboardFor('partisipasi', $kelas->id, $periodeAktif->id);
        $entryA = $lbKelas->firstWhere('id', $guruA->id);
        $this->assertNotNull($entryA);
        $this->assertEquals(1, $entryA->leaderboard_rank);
        $this->assertEquals(2, $entryA->total_penilaian);

        // Tambah 3 ulasan lagi untuk Guru A agar totalnya mencapai minimal 5 ulasan (syarat masuk leaderboard rating sekolah)
        for ($k = 3; $k <= 5; $k++) {
            $siswaK = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
            Penilaian::create([
                'siswa_id' => $siswaK->id,
                'guru_id' => $guruA->id,
                'periode_id' => $periodeAktif->id,
                'class_id' => $kelas->id,
                'kedisiplinan' => 5,
                'tanggung_jawab' => 5,
                'komunikasi' => 5,
                'keramahan' => 5,
                'kreativitas' => 5,
                'total_nilai' => 25,
            ]);
        }

        Guru::clearLeaderboardCache();
        $lbAktifEligible = Guru::leaderboardFor('rating', null, $periodeAktif->id);
        $this->assertTrue($lbAktifEligible->contains('id', $guruA->id));

        $lbKelasEligible = Guru::leaderboardFor('partisipasi', $kelas->id, $periodeAktif->id);
        $entryAEligible = $lbKelasEligible->firstWhere('id', $guruA->id);
        $this->assertNotNull($entryAEligible);
        $this->assertEquals(1, $entryAEligible->leaderboard_rank);
        $this->assertEquals(5, $entryAEligible->total_penilaian);

        // 3. Cek UI guru.detail: tombol laporkan harus ada di pojok bawah & arsip menampilkan rincian aspek
        $userGuruA = User::factory()->create(['role' => 'guru', 'nis' => $guruA->nip, 'is_active' => true]);
        $resGuruDetail = $this->actingAs($userGuruA)->get(route('guru.detail', $guruA));
        $resGuruDetail->assertOk();
        $resGuruDetail->assertSee('Laporkan');
        $resGuruDetail->assertSee('RINCIAN NILAI PER ASPEK:');

        // 4. Uji voting siswa dengan string kelas di users (tanpa pivot) tetap mencatat class_id & masuk leaderboard kelas
        $siswaBaru = User::factory()->create([
            'role' => 'siswa',
            'kelas' => $kelas->nama_kelas,
            'is_active' => true,
        ]);
        $resVote = $this->actingAs($siswaBaru)->post(route('siswa.penilaian.store', $guruA), [
            'kedisiplinan' => 5,
            'komunikasi' => 5,
            'tanggung_jawab' => 5,
            'kreativitas' => 5,
            'keramahan' => 5,
        ]);
        $resVote->assertRedirect();
        
        $voteBaru = Penilaian::where('siswa_id', $siswaBaru->id)->where('guru_id', $guruA->id)->first();
        $this->assertNotNull($voteBaru);
        $this->assertEquals($kelas->id, $voteBaru->class_id);

        // Leaderboard kelas terupdate dengan vote baru (total kini 6)
        Guru::clearLeaderboardCache();
        $lbKelasUpdated = Guru::leaderboardFor('partisipasi', $kelas->id, $periodeAktif->id);
        $entryAUpdated = $lbKelasUpdated->firstWhere('id', $guruA->id);
        $this->assertEquals(6, $entryAUpdated->total_penilaian);
    }
}
