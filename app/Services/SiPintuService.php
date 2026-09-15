<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SiPintuService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected int $timeout;
    protected bool $verifySsl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id'), '/');
        $this->clientId = config('services.sipintu.client_id');
        $this->clientSecret = config('services.sipintu.client_secret');
        $this->timeout = (int) config('services.sipintu.timeout', 60);
        $this->verifySsl = (bool) config('services.sipintu.verify_ssl', false);

        if (!$this->clientId || !$this->clientSecret) {
            Log::error('SiPintu Config Missing: SIPINTU_CLIENT_ID atau SIPINTU_CLIENT_SECRET belum diatur di .env');
            throw new \RuntimeException('Konfigurasi SiPintu tidak lengkap. Periksa file .env Anda.');
        }
    }

    /**
     * Detect if GuruKuu and SiPintu are targeting the exact same host and port
     */
    protected function isSelfReferencing(): bool
    {
        if (!app()->runningInConsole() && request()) {
            $currentHost = request()->getHost();
            $currentPort = (int) request()->getPort();
            $targetUrl = parse_url($this->baseUrl);
            $targetHost = $targetUrl['host'] ?? 'localhost';
            $targetPort = isset($targetUrl['port']) ? (int) $targetUrl['port'] : ($targetUrl['scheme'] === 'https' ? 443 : 80);

            $isSameHost = in_array($targetHost, ['localhost', '127.0.0.1']) && in_array($currentHost, ['localhost', '127.0.0.1']);
            $isSamePort = $targetPort === $currentPort;

            return $isSameHost && $isSamePort;
        }
        return false;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    protected function client()
    {
        $http = Http::withHeaders([
            'X-Client-ID'     => $this->clientId,
            'X-Client-Secret' => $this->clientSecret,
            'Accept'          => 'application/json',
        ])->timeout($this->timeout);

        if (!$this->verifySsl) {
            $http = $http->withoutVerifying();
        }

        return $http;
    }

    public function ping(): array
    {
        if ($this->isSelfReferencing()) {
            return [
                'success'     => false,
                'status_code' => 0,
                'latency_ms'  => 0,
                'data'        => null,
                'message'     => 'Konflik Port: GuruKuu dan SiPintu berjalan di host & port yang sama. Jalankan di port berbeda atau ubah SIPINTU_BASE_URL.',
            ];
        }

        $startTime = microtime(true);

        try {
            $http = Http::timeout($this->timeout)->acceptJson();
            if (!$this->verifySsl) {
                $http = $http->withoutVerifying();
            }

            $response = $http->get("{$this->baseUrl}/api/v1/ping", [
                'client_id' => $this->clientId,
            ]);

            $latency = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->successful()) {
                return [
                    'success'    => true,
                    'status_code'=> $response->status(),
                    'latency_ms' => $latency,
                    'data'       => $response->json(),
                    'message'    => 'Koneksi Gateway SiPintu Aktif (Online)',
                ];
            }

            return [
                'success'    => false,
                'status_code'=> $response->status(),
                'latency_ms' => $latency,
                'data'       => $response->json() ?? [],
                'message'    => "Gateway merespons dengan HTTP status: {$response->status()}",
            ];
        } catch (\Exception $e) {
            $latency = round((microtime(true) - $startTime) * 1000, 2);
            Log::warning("SiPintu Ping Error: " . $e->getMessage());

            return [
                'success'    => false,
                'status_code'=> 0,
                'latency_ms' => $latency,
                'data'       => null,
                'message'    => "Gagal terhubung ke Gateway SiPintu: " . $e->getMessage(),
            ];
        }
    }

    public function validateClient(): array
    {
        if ($this->isSelfReferencing()) {
            return [
                'success'     => false,
                'status_code' => 0,
                'data'        => null,
                'message'     => 'Konflik Port: SiPintu dan GuruKuu berjalan di port yang sama.',
            ];
        }

        try {
            $http = Http::timeout($this->timeout)->acceptJson();
            if (!$this->verifySsl) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post("{$this->baseUrl}/api/v1/validate-client", [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->successful()) {
                return [
                    'success'    => true,
                    'status_code'=> $response->status(),
                    'data'       => $response->json(),
                    'message'    => 'Kredensial Client ID & Secret Valid!',
                ];
            }

            return [
                'success'    => false,
                'status_code'=> $response->status(),
                'data'       => $response->json() ?? [],
                'message'    => 'Kredensial tidak valid atau ditolak oleh SiPintu Gateway.',
            ];
        } catch (\Exception $e) {
            Log::warning("SiPintu Validate Client Error: " . $e->getMessage());

            return [
                'success'    => false,
                'status_code'=> 0,
                'data'       => null,
                'message'    => 'Gagal menghubungi endpoint validasi: ' . $e->getMessage(),
            ];
        }
    }

    public function getTeachers(array $params = []): array
    {
        if ($this->isSelfReferencing()) {
            return ['success' => false, 'status_code' => 0, 'data' => [], 'total' => 0, 'raw' => null, 'message' => 'Konflik Port detected.'];
        }

        try {
            $forceRefresh = !empty($params['refresh']);
            $cacheKey = 'sipintu_teachers_raw';

            if ($forceRefresh) {
                Cache::forget($cacheKey);
            }

            $rawTeachers = Cache::remember($cacheKey, 300, function () {
                $response = $this->client()->get("{$this->baseUrl}/api/v1/sijuna/teachers");
                if ($response->successful()) {
                    return $this->extractDataArray($response->json(), 'teachers');
                }
                return null;
            });

            if ($rawTeachers === null) {
                $response = $this->client()->get("{$this->baseUrl}/api/v1/sijuna/teachers");
                if (!$response->successful()) {
                    return ['success' => false, 'status_code' => $response->status(), 'data' => [], 'total' => 0, 'raw' => $response->json(), 'message' => "Gagal mengambil data guru. Status: {$response->status()}"];
                }
                $rawTeachers = $this->extractDataArray($response->json(), 'teachers');
            }

            $teachers = array_map(function ($t) {
                $nip = (string) ($t['nip'] ?? $t['nik'] ?? $t['nip_guru'] ?? '');
                $nama = $t['nama'] ?? $t['name'] ?? $t['nama_guru'] ?? 'Tanpa Nama';
                $email = $t['user']['email'] ?? $t['email'] ?? null;
                $phone = $t['hp'] ?? $t['phone'] ?? $t['telepon'] ?? $t['no_hp'] ?? null;
                $bio = $t['bio'] ?? $t['alamat'] ?? $t['mapel'] ?? null;
                $kategori = strtolower($t['kategori'] ?? $t['category'] ?? 'normada');
                $photo = $t['photo'] ?? $t['foto'] ?? "https://ui-avatars.com/api/?name=" . urlencode($nama) . "&background=003366&color=fff";
                $status = (int) ($t['status'] ?? 1);

                return array_merge($t, [
                    'nip' => $nip, 'nama' => $nama, 'name' => $nama, 'email' => $email,
                    'phone' => $phone, 'hp' => $phone, 'bio' => $bio, 'alamat' => $bio,
                    'kategori' => $kategori, 'photo' => $photo, 'status' => $status,
                ]);
            }, $rawTeachers);

            if (!empty($params['nip'])) {
                $nipSearch = trim((string) $params['nip']);
                $teachers = array_values(array_filter($teachers, fn($t) => str_contains($t['nip'], $nipSearch)));
            }

            if (!empty($params['search'])) {
                $query = strtolower(trim((string) $params['search']));
                $teachers = array_values(array_filter($teachers, function ($t) use ($query) {
                    return str_contains(strtolower($t['nama'] ?? ''), $query) ||
                           str_contains(strtolower($t['nip'] ?? ''), $query) ||
                           str_contains(strtolower($t['email'] ?? ''), $query);
                }));
            }

            return ['success' => true, 'status_code' => 200, 'data' => $teachers, 'total' => count($teachers), 'raw' => $rawTeachers, 'message' => 'Berhasil mengambil data guru.'];
        } catch (\Exception $e) {
            Log::error("SiPintu getTeachers Exception: " . $e->getMessage());
            return ['success' => false, 'status_code' => 0, 'data' => [], 'total' => 0, 'raw' => null, 'message' => "Error: " . $e->getMessage()];
        }
    }

    public function getStudents(array $params = []): array
    {
        if ($this->isSelfReferencing()) {
            return ['success' => false, 'status_code' => 0, 'data' => [], 'total' => 0, 'raw' => null, 'message' => 'Konflik Port detected.'];
        }

        try {
            $forceRefresh = !empty($params['refresh']);
            $cacheKey = 'sipintu_students_raw_list';

            if ($forceRefresh) {
                try { Cache::store('file')->forget($cacheKey); } catch (\Exception $e) { Cache::forget($cacheKey); }
            }

            $rawStudents = null;
            try {
                $rawStudents = Cache::store('file')->remember($cacheKey, 300, function () {
                    $response = $this->client()->get("{$this->baseUrl}/api/v1/sijuna/students");
                    if ($response->successful()) return $this->extractDataArray($response->json(), 'students');
                    return null;
                });
            } catch (\Exception $e) {
                $response = $this->client()->get("{$this->baseUrl}/api/v1/sijuna/students");
                if ($response->successful()) $rawStudents = $this->extractDataArray($response->json(), 'students');
            }

            if ($rawStudents === null) {
                $response = $this->client()->get("{$this->baseUrl}/api/v1/sijuna/students");
                if (!$response->successful()) {
                    return ['success' => false, 'status_code' => $response->status(), 'data' => [], 'total' => 0, 'raw' => $response->json(), 'message' => "Gagal mengambil data siswa. Status: {$response->status()}"];
                }
                $rawStudents = $this->extractDataArray($response->json(), 'students');
            }

            $onlyActive = !isset($params['only_active']) || filter_var($params['only_active'], FILTER_VALIDATE_BOOLEAN);
            $students = [];

            foreach ($rawStudents as $s) {
                $nis = (string) ($s['nis'] ?? $s['nisn'] ?? $s['nis_siswa'] ?? '');
                $nama = $s['nama'] ?? $s['name'] ?? $s['nama_siswa'] ?? 'Tanpa Nama';
                $email = $s['user']['email'] ?? $s['email'] ?? ($nis ? "{$nis}@smkn1bangsri.sch.id" : null);
                $phone = $s['hp'] ?? $s['phone'] ?? $s['telepon'] ?? $s['no_hp'] ?? null;
                $alamat = $s['alamat'] ?? $s['address'] ?? '-';
                $nisn = $s['nisn'] ?? null;
                $jk = ($s['jk'] ?? 1) == 2 ? 'Perempuan' : 'Laki-laki';
                $jkCode = $s['jk'] ?? 1;

                $classroom = $s['classroom'] ?? null;
                $classroomId = $s['classroom_id'] ?? ($classroom['id'] ?? null);
                $kelasName = is_array($classroom) ? ($classroom['name'] ?? $classroom['nama'] ?? null) : ($classroom ?? null);
                $classroomStatus = isset($classroom['status']) ? (int) $classroom['status'] : ($classroomId ? 1 : 0);

                $jurusanName = '-';
                if ($kelasName) {
                    if (preg_match('/(PPLG|RPL)/i', $kelasName)) $jurusanName = 'Pengembangan Perangkat Lunak dan Gim';
                    elseif (preg_match('/(TO|TKRO|TBSM)/i', $kelasName)) $jurusanName = 'Teknik Otomotif';
                    elseif (preg_match('/(MPLB|OTKP|AP)/i', $kelasName)) $jurusanName = 'Manajemen Perkantoran dan Layanan Bisnis';
                    elseif (preg_match('/(PM|BDP)/i', $kelasName)) $jurusanName = 'Pemasaran';
                    elseif (preg_match('/(AKL|AK)/i', $kelasName)) $jurusanName = 'Akuntansi dan Keuangan Lembaga';
                }

                $isActive = !empty($classroomId) && !empty($classroom) && $classroomStatus === 1 && empty($s['deleted_at']);
                if ($onlyActive && !$isActive) continue;

                $photo = $s['photo'] ?? $s['foto'] ?? "https://ui-avatars.com/api/?name=" . urlencode($nama) . "&background=00A86B&color=fff";

                $normalized = array_merge($s, [
                    'nis' => $nis, 'nisn' => $nisn, 'nama' => $nama, 'name' => $nama,
                    'email' => $email, 'phone' => $phone, 'hp' => $phone, 'alamat' => $alamat,
                    'jk_text' => $jk, 'jk' => $jkCode, 'kelas' => $kelasName, 'kelas_name' => $kelasName,
                    'jurusan' => $jurusanName, 'classroom' => $classroom, 'classroom_id' => $classroomId,
                    'is_active' => $isActive, 'photo' => $photo,
                ]);

                $students[] = $normalized;
            }

            if (!empty($params['nis'])) {
                $nisSearch = trim((string) $params['nis']);
                $students = array_values(array_filter($students, fn($s) => str_contains($s['nis'], $nisSearch)));
            }

            if (!empty($params['search'])) {
                $query = strtolower(trim((string) $params['search']));
                $students = array_values(array_filter($students, function ($s) use ($query) {
                    return str_contains(strtolower($s['nama'] ?? ''), $query) ||
                           str_contains(strtolower($s['nis'] ?? ''), $query) ||
                           str_contains(strtolower($s['kelas'] ?? ''), $query);
                }));
            }

            return ['success' => true, 'status_code' => 200, 'data' => $students, 'total' => count($students), 'total_raw' => count($rawStudents), 'raw' => $rawStudents, 'message' => 'Berhasil mengambil data siswa.'];
        } catch (\Exception $e) {
            Log::error("SiPintu getStudents Exception: " . $e->getMessage());
            return ['success' => false, 'status_code' => 0, 'data' => [], 'total' => 0, 'raw' => null, 'message' => "Error: " . $e->getMessage()];
        }
    }

    public function getTeacherByNip(string $nip): ?array
    {
        $result = $this->getTeachers(['nip' => $nip]);
        if (!$result['success'] || empty($result['data'])) return null;
        foreach ($result['data'] as $item) {
            if ((string)($item['nip'] ?? '') === (string)$nip) return $item;
        }
        return $result['data'][0] ?? null;
    }

    public function getStudentByNis(string $nis): ?array
    {
        $result = $this->getStudents(['nis' => $nis, 'only_active' => false]);
        if (!$result['success'] || empty($result['data'])) return null;
        foreach ($result['data'] as $item) {
            if ((string)($item['nis'] ?? '') === (string)$nis) return $item;
        }
        return $result['data'][0] ?? null;
    }

    protected function extractDataArray($payload, string $singularKey = 'data'): array
    {
        if (!is_array($payload)) return [];
        if (isset($payload[0]) && is_array($payload[0])) return $payload;
        if (isset($payload['data'])) {
            if (is_array($payload['data'])) {
                if (isset($payload['data'][0])) return $payload['data'];
                if (isset($payload['data']['data']) && is_array($payload['data']['data'])) return $payload['data']['data'];
                return [$payload['data']];
            }
        }
        if (isset($payload[$singularKey]) && is_array($payload[$singularKey])) {
            if (isset($payload[$singularKey][0])) return $payload[$singularKey];
            return [$payload[$singularKey]];
        }
        return [];
    }

    public function findLocalKelasId(string $rawClassName): ?int
    {
        $tingkat = null;
        $namaKelasClean = trim($rawClassName);

        if (preg_match('/^(XII|XI|X)\s+(.+)$/i', $namaKelasClean, $matches)) {
            $roman = strtoupper($matches[1]);
            $namaKelasClean = trim($matches[2]);
            $tingkat = match ($roman) { 'X' => 10, 'XI' => 11, 'XII' => 12, default => null };
        }

        if ($tingkat) {
            $matched = Kelas::where('tingkat', $tingkat)
                ->where(function ($q) use ($namaKelasClean) {
                    $q->where('nama_kelas', $namaKelasClean)->orWhere('nama_kelas', 'LIKE', "%{$namaKelasClean}%");
                })->first();
            if ($matched) return $matched->id;
        }

        $fallback = Kelas::where('nama_kelas', 'LIKE', "%{$rawClassName}%")
            ->orWhere('nama_kelas', 'LIKE', "%{$namaKelasClean}%")
            ->first();

        return $fallback?->id;
    }

    public function ensureDefaultJurusans(): void
    {
        $defaultJurusans = [
            ['nama_jurusan' => 'Pengembangan Perangkat Lunak dan Gim', 'kode_jurusan' => 'PPLG', 'deskripsi' => 'Jurusan Rekayasa Perangkat Lunak dan Gim'],
            ['nama_jurusan' => 'Teknik Otomotif', 'kode_jurusan' => 'TO', 'deskripsi' => 'Jurusan Teknik Kendaraan Ringan dan Sepeda Motor'],
            ['nama_jurusan' => 'Manajemen Perkantoran dan Layanan Bisnis', 'kode_jurusan' => 'MPLB', 'deskripsi' => 'Jurusan Manajemen Perkantoran dan Layanan Bisnis'],
            ['nama_jurusan' => 'Pemasaran', 'kode_jurusan' => 'PM', 'deskripsi' => 'Jurusan Bisnis Digital dan Pemasaran'],
            ['nama_jurusan' => 'Akuntansi dan Keuangan Lembaga', 'kode_jurusan' => 'AKL', 'deskripsi' => 'Jurusan Akuntansi dan Keuangan Lembaga'],
        ];

        foreach ($defaultJurusans as $dj) {
            Jurusan::firstOrCreate(['kode_jurusan' => $dj['kode_jurusan']], $dj);
        }
    }

    public function parseClassroomString(string $rawName): array
    {
        $tingkat = '10';
        $namaClean = trim($rawName);
        if (preg_match('/^(XII|XI|X)\s+(.+)$/i', $rawName, $m)) {
            $roman = strtoupper($m[1]);
            $tingkat = match ($roman) { 'X' => '10', 'XI' => '11', 'XII' => '12', default => '10' };
            $namaClean = trim($m[2]);
        }
        return [
            'tingkat' => $tingkat,
            'nama_kelas' => $namaClean,
        ];
    }

    public function syncTeacherToLocal(array $teacherData, ?int $jurusanId = null): array
    {
        try {
            $nip = $teacherData['nip'] ?? $teacherData['nik'] ?? null;
            $nama = $teacherData['nama'] ?? $teacherData['name'] ?? $teacherData['nama_guru'] ?? null;

            if (!$nip || !$nama) return ['success' => false, 'message' => 'Data guru tidak lengkap (NIP/Nama kosong).'];

            $email = $teacherData['user']['email'] ?? $teacherData['email'] ?? null;
            $phone = $teacherData['hp'] ?? $teacherData['phone'] ?? $teacherData['telepon'] ?? null;
            $bio = $teacherData['bio'] ?? $teacherData['alamat'] ?? $teacherData['mapel'] ?? null;
            $photo = $teacherData['photo'] ?? $teacherData['foto'] ?? null;
            $kategori = strtolower($teacherData['kategori'] ?? $teacherData['category'] ?? 'normada');
            if (!in_array($kategori, ['normada', 'produktif'])) $kategori = 'normada';

            if (!$jurusanId && !empty($teacherData['jurusan'])) {
                $jurusanName = is_array($teacherData['jurusan']) ? ($teacherData['jurusan']['nama_jurusan'] ?? '') : $teacherData['jurusan'];
                $matchedJurusan = Jurusan::where('nama_jurusan', 'LIKE', "%{$jurusanName}%")->orWhere('kode_jurusan', 'LIKE', "%{$jurusanName}%")->first();
                if ($matchedJurusan) $jurusanId = $matchedJurusan->id;
            }

            $guru = Guru::updateOrCreate(['nip' => (string) $nip], [
                'nama' => $nama,
                'email' => $email,
                'phone' => $phone,
                'photo' => $photo,
                'kategori' => $kategori,
                'jurusan_id' => $jurusanId,
                'bio' => $bio,
            ]);

            // Sync or create User account for Guru so teacher can log in
            $guruUserEmail = $email ?: ($nip . '@gurukuu.local');
            User::updateOrCreate(
                ['nis' => (string) $nip],
                [
                    'name' => $nama,
                    'email' => $guruUserEmail,
                    'role' => 'guru',
                    'password' => Hash::make((string) $nip),
                    'is_active' => true,
                ]
            );

            return ['success' => true, 'guru' => $guru, 'message' => "Guru {$guru->nama} berhasil disinkronkan."];
        } catch (\Exception $e) {
            Log::error("SiPintu syncTeacherToLocal Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal sinkron guru: ' . $e->getMessage()];
        }
    }

    public function syncStudentToLocal(array $studentData, ?int $kelasId = null): array
    {
        try {
            $nis = (string) ($studentData['nis'] ?? $studentData['nisn'] ?? $studentData['nis_siswa'] ?? '');
            $name = $studentData['nama'] ?? $studentData['name'] ?? $studentData['nama_siswa'] ?? null;

            if (!$nis || !$name) return ['success' => false, 'message' => 'Data siswa tidak lengkap (NIS/Nama kosong).'];

            $email = $studentData['user']['email'] ?? $studentData['email'] ?? ($nis . '@smkn1bangsri.sch.id');
            $tanggalLahir = $studentData['tanggal_lahir'] ?? $studentData['birth_date'] ?? $studentData['tgl_lahir'] ?? '2007-01-01';

            if (!$kelasId) {
                $rawKelasName = null;
                if (!empty($studentData['classroom'])) {
                    $rawKelasName = is_array($studentData['classroom']) ? ($studentData['classroom']['name'] ?? null) : $studentData['classroom'];
                } elseif (!empty($studentData['kelas'])) {
                    $rawKelasName = is_array($studentData['kelas']) ? ($studentData['kelas']['nama_kelas'] ?? null) : $studentData['kelas'];
                }
                if ($rawKelasName) $kelasId = $this->findLocalKelasId($rawKelasName);
            }

            $user = User::where('nis', $nis)->orWhere('email', $email)->first();

            if ($user) {
                $user->update([
                    'name' => $name,
                    'nis' => $nis,
                    'email' => $email,
                    'tanggal_lahir' => $tanggalLahir,
                    'role' => 'siswa',
                    'is_active' => true,
                ]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'nis' => $nis,
                    'email' => $email,
                    'password' => Hash::make($nis, ['rounds' => 4]),
                    'role' => 'siswa',
                    'tanggal_lahir' => $tanggalLahir,
                    'is_active' => true,
                ]);
            }

            if ($kelasId) {
                $tahunAjaran = now()->year . '/' . (now()->year + 1);
                $user->kelas()->syncWithoutDetaching([$kelasId => ['tahun_ajaran' => $tahunAjaran]]);
            }

            return ['success' => true, 'siswa' => $user, 'message' => "Siswa {$user->name} berhasil disinkronkan."];
        } catch (\Exception $e) {
            Log::error("SiPintu syncStudentToLocal Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal sinkron siswa: ' . $e->getMessage()];
        }
    }

    /**
     * Sinkronisasi menyeluruh dari SiPintu:
     * - Opsi membersihkan data lokal lama (menjaga akun Admin tetap utuh)
     * - Mengisi Jurusan & Kelas otomatis dari SiPintu
     * - Menarik dan menyinkronkan seluruh 71 Guru
     * - Menarik dan menyinkronkan seluruh Siswa Aktif (1.130 siswa)
     */
    public function syncAllFromSiPintu(bool $clean = false, bool $onlyActive = true): array
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '1024M');

        $this->ensureDefaultJurusans();

        if ($clean) {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            \App\Models\Penilaian::truncate();
            \Illuminate\Support\Facades\DB::table('siswa_kelas')->truncate();
            \Illuminate\Support\Facades\DB::table('guru_kelas')->truncate();
            
            // Hapus user role siswa dan guru, pertahankan admin
            User::where('role', '!=', 'admin')->delete();
            Guru::truncate();
            Kelas::truncate();
            Jurusan::truncate();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->ensureDefaultJurusans();

        // 1. Ambil data guru dari SiPintu
        $teachersRes = $this->getTeachers(['refresh' => true]);
        $teachers = $teachersRes['data'] ?? [];

        // 2. Ambil data siswa aktif dari SiPintu
        $studentsRes = $this->getStudents(['only_active' => $onlyActive, 'refresh' => true]);
        $students = $studentsRes['data'] ?? [];

        // 3. Ekstrak dan buat seluruh Kelas dari data siswa
        $allJurusans = Jurusan::all();

        foreach ($students as $s) {
            $rawKelas = null;
            if (!empty($s['classroom'])) {
                $rawKelas = is_array($s['classroom']) ? ($s['classroom']['name'] ?? null) : $s['classroom'];
            } elseif (!empty($s['kelas'])) {
                $rawKelas = is_array($s['kelas']) ? ($s['kelas']['nama_kelas'] ?? null) : $s['kelas'];
            }

            if ($rawKelas) {
                $parsed = $this->parseClassroomString($rawKelas);
                $tingkat = $parsed['tingkat'];
                $namaKelas = $parsed['nama_kelas'];

                $jurusanId = null;
                foreach ($allJurusans as $j) {
                    if (str_contains(strtoupper($rawKelas), strtoupper($j->kode_jurusan)) ||
                        str_contains(strtoupper($namaKelas), strtoupper($j->kode_jurusan))) {
                        $jurusanId = $j->id;
                        break;
                    }
                }
                if (!$jurusanId) {
                    $jurusanId = $allJurusans->first()?->id ?? 1;
                }

                Kelas::firstOrCreate(
                    ['nama_kelas' => $namaKelas, 'tingkat' => $tingkat],
                    ['jurusan_id' => $jurusanId, 'jumlah_siswa' => 36]
                );
            }
        }

        // Siapkan lookup map Kelas in-memory untuk kecepatan maksimal
        $kelasMap = [];
        foreach (Kelas::all() as $k) {
            $kelasMap[strtoupper($k->tingkat . '_' . $k->nama_kelas)] = $k->id;
            $kelasMap[strtoupper($k->nama_kelas)] = $k->id;
        }

        // 4. Sinkronisasi Guru
        $guruCount = 0;
        foreach ($teachers as $t) {
            $res = $this->syncTeacherToLocal($t);
            if ($res['success']) {
                $guruCount++;
            }
        }

        // 5. Sinkronisasi Siswa Aktif dengan In-Memory Map
        $siswaCount = 0;
        $tahunAjaran = now()->year . '/' . (now()->year + 1);
        $periodeAktif = \App\Models\Periode::where('status', 'aktif')->first();
        if ($periodeAktif) {
            $tahunAjaran = $periodeAktif->tahun_ajaran;
        }

        foreach ($students as $s) {
            $rawKelas = null;
            if (!empty($s['classroom'])) {
                $rawKelas = is_array($s['classroom']) ? ($s['classroom']['name'] ?? null) : $s['classroom'];
            } elseif (!empty($s['kelas'])) {
                $rawKelas = is_array($s['kelas']) ? ($s['kelas']['nama_kelas'] ?? null) : $s['kelas'];
            }

            $kelasId = null;
            if ($rawKelas) {
                $parsed = $this->parseClassroomString($rawKelas);
                $key = strtoupper($parsed['tingkat'] . '_' . $parsed['nama_kelas']);
                $kelasId = $kelasMap[$key] ?? ($kelasMap[strtoupper($parsed['nama_kelas'])] ?? null);
            }

            $res = $this->syncStudentToLocal($s, $kelasId);
            if ($res['success']) {
                $siswaCount++;
            }
        }

        return [
            'success'      => true,
            'guru_count'   => $guruCount,
            'siswa_count'  => $siswaCount,
            'jurusan_count'=> Jurusan::count(),
            'kelas_count'  => Kelas::count(),
            'message'      => "Sinkronisasi berhasil! {$guruCount} Guru, {$siswaCount} Siswa aktif, " . Kelas::count() . " Kelas, dan " . Jurusan::count() . " Jurusan tersinkronkan.",
        ];
    }
}