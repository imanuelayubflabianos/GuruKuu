<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
