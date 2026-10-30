<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiPeriodeOtomatisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_kalkulasi_status_otomatis_berdasarkan_tanggal()
    {
        // 1. Masa depan -> terjadwal
        $statusFuture = Periode::calculateStatusFromDates('2026-11-01 00:00:00', '2026-12-31 23:59:59');
        $this->assertEquals('terjadwal', $statusFuture);

        // 2. Masa sekarang -> aktif
        $statusCurrent = Periode::calculateStatusFromDates('2026-10-01 00:00:00', '2026-10-31 23:59:59');
        $this->assertEquals('aktif', $statusCurrent);

        // 3. Masa lalu -> selesai
        $statusPast = Periode::calculateStatusFromDates('2026-01-01 00:00:00', '2026-06-30 23:59:59');
        $this->assertEquals('selesai', $statusPast);
    }

    public function test_sinkronisasi_otomatis_mengaktifkan_periode_terjadwal_dan_menyelesaikan_periode_lampau()
    {
        $past = Periode::create([
            'nama_periode' => 'Semester Genap 2025/2026',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'genap',
            'tanggal_mulai' => '2026-01-01 00:00:00',
            'tanggal_selesai' => '2026-06-30 23:59:59',
            'status' => 'aktif',
        ]);

        $current = Periode::create([
            'nama_periode' => 'Semester Ganjil 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'tanggal_mulai' => '2026-07-01 00:00:00',
            'tanggal_selesai' => '2026-12-31 23:59:59',
            'status' => 'terjadwal',
        ]);

        $future = Periode::create([
            'nama_periode' => 'Semester Genap 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'genap',
            'tanggal_mulai' => '2027-01-01 00:00:00',
            'tanggal_selesai' => '2027-06-30 23:59:59',
            'status' => 'terjadwal',
        ]);

        Periode::syncAllStatuses();

        $past->refresh();
        $current->refresh();
        $future->refresh();

        $this->assertEquals('selesai', $past->status);
        $this->assertEquals('aktif', $current->status);
        $this->assertEquals('terjadwal', $future->status);

        $activePeriode = Periode::getActivePeriode();
        $this->assertNotNull($activePeriode);
        $this->assertEquals($current->id, $activePeriode->id);
    }

    public function test_artisan_command_periode_sync()
    {
        $current = Periode::create([
            'nama_periode' => 'Semester Berjalan',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'tanggal_mulai' => '2026-08-01 00:00:00',
            'tanggal_selesai' => '2026-12-31 23:59:59',
            'status' => 'terjadwal',
        ]);

        $this->artisan('periode:sync')->assertExitCode(0);

        $current->refresh();
        $this->assertEquals('aktif', $current->status);
    }

    public function test_admin_simpan_periode_dengan_mode_otomatis()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('admin.pengaturan.periode'), [
            'nama_periode' => 'Semester Terjadwal Mendatang',
            'tahun_ajaran' => '2027/2028',
            'semester' => 'ganjil',
            'tanggal_mulai' => '2027-07-01 08:00:00',
            'tanggal_selesai' => '2027-12-31 16:00:00',
            'status' => 'otomatis',
        ]);

        $response->assertRedirect(route('admin.pengaturan.index') . '#tabPeriode');

        $periode = Periode::where('nama_periode', 'Semester Terjadwal Mendatang')->first();
        $this->assertNotNull($periode);
        $this->assertEquals('terjadwal', $periode->status);
    }

    public function test_admin_aktifkan_periode_secara_manual()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $periode = Periode::create([
            'nama_periode' => 'Periode Khusus',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'tanggal_mulai' => '2027-01-01 00:00:00',
            'tanggal_selesai' => '2027-06-30 23:59:59',
            'status' => 'terjadwal',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.pengaturan.periode.aktifkan', $periode));
        $response->assertRedirect(route('admin.pengaturan.index') . '#tabPeriode');

        $periode->refresh();
        $this->assertEquals('aktif', $periode->status);
    }

    public function test_riwayat_penilaian_multi_periode_tersimpan_permanen_sebagai_arsip()
    {
        $jurusan = Jurusan::create(['nama_jurusan' => 'PPLG', 'kode_jurusan' => 'PPLG']);
        $kelas = Kelas::create(['nama_kelas' => '10 PPLG 1', 'tingkat' => 10, 'jurusan_id' => $jurusan->id]);
        $guru = Guru::factory()->create(['nama' => 'Guru Juara']);
        $siswa = User::factory()->create(['role' => 'siswa', 'jurusan_id' => $jurusan->id, 'is_active' => true]);

        // Periode 1 (Lalu)
        $p1 = Periode::create([
            'nama_periode' => 'Semester Lalu',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'genap',
            'tanggal_mulai' => '2026-01-01 00:00:00',
            'tanggal_selesai' => '2026-06-30 23:59:59',
            'status' => 'selesai',
        ]);

        // Periode 2 (Aktif)
        $p2 = Periode::create([
            'nama_periode' => 'Semester Ini',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'tanggal_mulai' => '2026-07-01 00:00:00',
            'tanggal_selesai' => '2026-12-31 23:59:59',
            'status' => 'aktif',
        ]);

        // Penilaian di Periode 1
        $penilaian1 = Penilaian::create([
            'siswa_id' => $siswa->id,
            'guru_id' => $guru->id,
            'periode_id' => $p1->id,
            'class_id' => $kelas->id,
            'kedisiplinan' => 5,
            'komunikasi' => 5,
            'tanggung_jawab' => 5,
            'kreativitas' => 5,
            'keramahan' => 5,
            'total_nilai' => 25,
            'kritik' => 'Hebat di semester lalu',
        ]);

        // Penilaian di Periode 2
        $penilaian2 = Penilaian::create([
            'siswa_id' => $siswa->id,
            'guru_id' => $guru->id,
            'periode_id' => $p2->id,
            'class_id' => $kelas->id,
            'kedisiplinan' => 4,
            'komunikasi' => 4,
            'tanggung_jawab' => 4,
            'kreativitas' => 4,
            'keramahan' => 4,
            'total_nilai' => 20,
            'kritik' => 'Bagus di semester sekarang',
        ]);

        $this->assertDatabaseHas('penilaian', ['id' => $penilaian1->id, 'periode_id' => $p1->id]);
        $this->assertDatabaseHas('penilaian', ['id' => $penilaian2->id, 'periode_id' => $p2->id]);

        // Cek arsip periode
        $arsip = Periode::whereIn('status', ['selesai', 'nonaktif'])
            ->whereHas('penilaian', fn($q) => $q->where('guru_id', $guru->id))
            ->get();

        $this->assertCount(1, $arsip);
        $this->assertEquals($p1->id, $arsip->first()->id);
    }
}
