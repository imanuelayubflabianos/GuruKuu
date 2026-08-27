<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jurusan
        $jurusanData = [
            ['nama_jurusan' => 'Teknik Otomotif', 'kode_jurusan' => 'TO'],
            ['nama_jurusan' => 'Pengembangan Perangkat Lunak dan Gim', 'kode_jurusan' => 'PPLG'],
            ['nama_jurusan' => 'Manajemen Perkantoran dan Layanan Bisnis', 'kode_jurusan' => 'MPLB'],
            ['nama_jurusan' => 'Pemasaran', 'kode_jurusan' => 'PM'],
            ['nama_jurusan' => 'Akuntansi dan Keuangan Lembaga', 'kode_jurusan' => 'AKL'],
        ];
        $jurusanMap = [];
        foreach ($jurusanData as $j) {
            $jurusan = Jurusan::firstOrCreate(['kode_jurusan' => $j['kode_jurusan']], $j);
            $jurusanMap[$j['kode_jurusan']] = $jurusan->id;
        }

        // 2. Admin
        $imanuel = User::create([
            'name' => 'Imanuel Ayub Flabianos', 'nis' => '4669',
            'email' => 'imanuel@gurukuu.com', 'password' => Hash::make('admin123'),
            'role' => 'admin', 'tanggal_lahir' => '2009-01-03', 'is_active' => true,
        ]);
        $rico = User::create([
            'name' => 'Rico Dwi Prasetya', 'nis' => '4686',
            'email' => 'rico@gurukuu.com', 'password' => Hash::make('admin123'),
            'role' => 'admin', 'tanggal_lahir' => '2008-11-30', 'is_active' => true,
        ]);

        // 3. Kelas (33 kelas)
        $kelasData = [];
        $jurusanConfig = ['TO' => [1,2], 'PPLG' => [1,2], 'MPLB' => [1,2,3], 'PM' => [1,2], 'AKL' => [1,2]];
        foreach ($jurusanConfig as $kode => $nums) {
            foreach ($nums as $num) {
                foreach (['10','11','12'] as $tingkat) {
                    $kelas = Kelas::create([
                        'nama_kelas' => "$kode $num",
                        'jurusan_id' => $jurusanMap[$kode],
                        'tingkat' => $tingkat,
                        'jumlah_siswa' => 36,
                    ]);
                    $kelasData["{$tingkat}-{$kode}-{$num}"] = $kelas;
                }
            }
        }

        // 4. ✅ GURU: 40 Normada + 30 Produktif = 70 Total
        $guruNormada = [];
        $guruProduktifPerJurusan = [];
        
        // Array nama untuk 40 guru normada
        $namaDepan = ['Budi','Siti','Ahmad','Dewi','Rudi','Rina','Joko','Maya','Andi','Lina','Bambang','Fitri','Hendra','Nina','Dedi','Ratna','Agus','Yuni','Tono','Wulan','Eko','Diana','Heru','Sri','Iwan','Tina','Bobby','Lia','Yanto','Mega','Fajar','Anisa','Rizky','Putri','Dimas','Siska','Arif','Nadia','Bayu','Citra'];
        $namaBelakang = ['Santoso','Aminah','Hidayat','Lestari','Hermawan','Wati','Susilo','Sari','Pratama','Marlina','Suryadi','Handayani','Gunawan','Kartika','Kurniawan','Dewi','Salim','Astuti','Supriyadi','Wulandari'];

        // ✅ 40 Guru Normada (Bisa mengajar di berbagai jurusan)
        for ($i = 0; $i < 40; $i++) {
            $guruNormada[] = Guru::create([
                'nip' => '19' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . '0101011' . rand(1000, 9999),
                'nama' => $namaDepan[$i] . ' ' . $namaBelakang[$i % 20],
                'kategori' => 'normada', 
                'jurusan_id' => null,
                'bio' => 'Guru profesional dengan pengalaman mengajar lebih dari ' . rand(5, 20) . ' tahun.',
            ]);
        }

        // ✅ 30 Guru Produktif (6 per jurusan, mengajar di SEMUA tingkat jurusan tersebut)
        $produktifCounter = 0;
        foreach ($jurusanMap as $kode => $jurusanId) {
            $guruProduktifPerJurusan[$kode] = [];
            for ($i = 0; $i < 6; $i++) {
                $idx = $produktifCounter++;
                $guruProduktifPerJurusan[$kode][] = Guru::create([
                    'nip' => '19' . str_pad(41 + $idx, 4, '0', STR_PAD_LEFT) . '0101012' . rand(1000, 9999),
                    'nama' => $namaDepan[$idx % 40] . ' ' . $namaBelakang[$idx % 20],
                    'kategori' => 'produktif', 
                    'jurusan_id' => $jurusanId,
                    'bio' => 'Guru profesional dengan pengalaman mengajar lebih dari ' . rand(5, 20) . ' tahun.',
                ]);
            }
        }

        // 5. Assign Guru ke Kelas (12 per kelas: 6 normada + 6 produktif)
        foreach ($kelasData as $key => $kelas) {
            $kodeJurusan = explode('-', $key)[1];
            
            // 6 Guru Normada (diambil acak dari 40 guru normada)
            $normadaUntukKelas = collect($guruNormada)->random(6);
            foreach ($normadaUntukKelas as $guru) {
                $guru->kelas()->attach($kelas->id, ['mata_pelajaran' => $this->mapelNormada()]);
            }

            // 6 Guru Produktif (SEMUA 6 guru dari jurusan ini, agar sama di semua tingkat)
            foreach ($guruProduktifPerJurusan[$kodeJurusan] as $guru) {
                $guru->kelas()->attach($kelas->id, ['mata_pelajaran' => $this->mapelProduktif($kodeJurusan)]);
            }
        }

        // 6. Siswa (36 per kelas) - DENGAN BATCH PROCESSING
        $siswaList = [];
        $namaSiswaDepan = ['Ahmad','Budi','Citra','Dewi','Eko','Fitri','Galih','Hana','Ivan','Joko','Kartika','Lina','Muhammad','Nina','Oscar','Putri','Qori','Rizky','Sinta','Taufik'];
        $namaSiswaBelakang = ['Pratama','Sari','Wijaya','Lestari','Kurniawan','Handayani','Setiawan','Puspita','Rahman','Permata','Saputra','Kirana','Aditya','Aulia','Anggara','Dewi','Budiman','Salsabila','Putri','Fals'];
        $nisCounter = 1000;

        $this->command->info('Membuat data siswa...');
        $batchCount = 0;
        
        foreach ($kelasData as $key => $kelas) {
            for ($i = 0; $i < 36; $i++) {
                $nama = $namaSiswaDepan[array_rand($namaSiswaDepan)] . ' ' . $namaSiswaBelakang[array_rand($namaSiswaBelakang)];
                $siswa = User::create([
                    'name' => $nama,
                    'nis' => str_pad($nisCounter++, 5, '0', STR_PAD_LEFT),
                    'email' => null, 'password' => null, 'role' => 'siswa',
                    'kelas' => $kelas->nama_kelas . ' Tingkat ' . $kelas->tingkat,
                    'jurusan_id' => $kelas->jurusan_id,
                    'tanggal_lahir' => now()->subYears(rand(15, 18))->format('Y-m-d'),
                    'is_active' => true,
                ]);
                $siswa->kelas()->attach($kelas->id, ['tahun_ajaran' => '2026/2027']);
                $siswaList[] = $siswa;
                $batchCount++;
                
                // Flush memory setiap 100 siswa
                if ($batchCount % 100 === 0) {
                    $this->command->info("  ✓ $batchCount siswa dibuat...");
                    DB::connection()->reconnect();
                }
            }
        }
        $this->command->info("✓ Total $batchCount siswa berhasil dibuat!");

        // 7. Imanuel & Rico ke 12 PPLG 1
        $kelas12PPLG1 = $kelasData['12-PPLG-1'];
        $imanuel->kelas()->attach($kelas12PPLG1->id, ['tahun_ajaran' => '2026/2027']);
        $rico->kelas()->attach($kelas12PPLG1->id, ['tahun_ajaran' => '2026/2027']);

        // 8. Periode 2026/2027
        $periodeAktif = Periode::firstOrCreate(
            ['tahun_ajaran' => '2026/2027', 'semester' => 'ganjil'],
            ['nama_periode' => 'Semester Ganjil 2026/2027', 'status' => 'aktif', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2026-12-31']
        );

        // 9. Generate Penilaian - DENGAN BATCH PROCESSING
        $this->command->info('Membuat data penilaian...');
        $penilaianCount = 0;
        
        foreach ($siswaList as $siswa) {
            $kelasSiswa = $siswa->kelas()->wherePivot('tahun_ajaran', '2026/2027')->first();
            if (!$kelasSiswa) continue;
            
            $gn = $kelasSiswa->guru()->where('kategori', 'normada')->get();
            $gp = $kelasSiswa->guru()->where('kategori', 'produktif')->get();
            
            $guruUntukDinilai = collect();
            if ($gn->count() >= 3) $guruUntukDinilai = $guruUntukDinilai->merge($gn->random(3));
            if ($gp->count() >= 3) $guruUntukDinilai = $guruUntukDinilai->merge($gp->random(3));
            
            foreach ($guruUntukDinilai as $guru) {
                Penilaian::create([
                    'siswa_id' => $siswa->id, 'guru_id' => $guru->id, 'periode_id' => $periodeAktif->id,
                    'kedisiplinan' => rand(3,5), 'cara_mengajar' => rand(3,5), 'komunikasi' => rand(3,5),
                    'tanggung_jawab' => rand(3,5), 'kreativitas' => rand(3,5), 'keramahan' => rand(3,5),
                    'kritik' => fake()->optional(0.3)->sentence(), 'saran' => fake()->optional(0.3)->sentence(),
                    'total_nilai' => rand(18,30),
                ]);
                $penilaianCount++;
                
                // Flush memory setiap 500 penilaian
                if ($penilaianCount % 500 === 0) {
                    $this->command->info("  ✓ $penilaianCount penilaian dibuat...");
                    DB::connection()->reconnect();
                }
            }
        }
        $this->command->info("✓ Total $penilaianCount penilaian berhasil dibuat!");

        // 10. Update rata-rata guru
        $this->command->info('Menghitung rata-rata guru...');
        Guru::all()->each->updateRataRata();
        
        $this->command->info('✅ Seeding selesai!');
    }

    private function mapelNormada() {
        $m = ['Matematika','Bahasa Indonesia','Bahasa Inggris','Pendidikan Agama','PKN','Sejarah','Penjaskes','Seni Budaya'];
        return $m[array_rand($m)];
    }

    private function mapelProduktif($kode) {
        $m = [
            'TO' => ['Mesin Otomotif','Kelistrikan Kendaraan','Sistem Bahan Bakar'],
            'PPLG' => ['Pemrograman Web','Basis Data','Algoritma'],
            'MPLB' => ['Administrasi Perkantoran','Korespondensi','Kearsipan'],
            'PM' => ['Pemasaran Digital','Marketing Strategy','Branding'],
            'AKL' => ['Akuntansi Dasar','Pajak','Audit'],
        ];
        return $m[$kode][array_rand($m[$kode])];
    }
}