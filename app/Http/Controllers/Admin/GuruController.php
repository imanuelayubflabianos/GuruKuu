<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::with('jurusan');

        // Pencarian nama, NIP, email, atau keahlian/jurusan (mendukung multi-kata / token)
        if ($request->filled('search')) {
            $terms = preg_split('/\s+/', trim($request->search), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($terms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('nama', 'like', "%{$term}%")
                      ->orWhere('nip', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%")
                      ->orWhereHas('jurusan', fn($j) => $j->where('nama_jurusan', 'like', "%{$term}%"));
                });
            }
        }
        
        // Filter kategori (normada / produktif)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        // Filter kelas yang diajar
        $kelasId = $request->input('kelas_id', $request->input('kelas'));
        if (!empty($kelasId)) {
            $classMatches = (clone $query)->whereHas('kelas', function ($kelas) use ($kelasId) {
                $kelas->whereKey($kelasId);
            });
            // Jika admin melakukan pencarian kata kunci dan guru tidak ada di kelas filter, tetap tampilkan agar tidak hilang
            if (!$request->filled('search') || $classMatches->exists()) {
                $query->whereHas('kelas', function ($kelas) use ($kelasId) {
                    $kelas->whereKey($kelasId);
                });
            }
        }

        // Filter status akun aktif / nonaktif
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $activeNips = User::where('role', 'guru')->where('is_active', true)->pluck('nis')->filter()->toArray();
                $activeEmails = User::where('role', 'guru')->where('is_active', true)->pluck('email')->filter()->toArray();
                $query->where(function ($q) use ($activeNips, $activeEmails) {
                    $q->whereIn('nip', $activeNips)->orWhereIn('email', $activeEmails);
                });
            } elseif ($request->status === 'nonaktif') {
                $inactiveNips = User::where('role', 'guru')->where('is_active', false)->pluck('nis')->filter()->toArray();
                $inactiveEmails = User::where('role', 'guru')->where('is_active', false)->pluck('email')->filter()->toArray();
                $query->where(function ($q) use ($inactiveNips, $inactiveEmails) {
                    $q->whereIn('nip', $inactiveNips)->orWhereIn('email', $inactiveEmails);
                });
            }
        }
        
        $guru = $query->with('kelas.jurusan')->latest()->paginate(15)->withQueryString();
        $this->loadLinkedUsers($guru->getCollection());
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        return view('admin.guru.index', compact('guru', 'kelasList'));
    }

    /**
     * Memuat akun guru dalam satu query untuk menghindari query tambahan per baris tabel.
     */
    private function loadLinkedUsers($gurus): void
    {
        $nips = $gurus->pluck('nip')->filter()->values();
        $emails = $gurus->pluck('email')->filter()->values();

        if ($nips->isEmpty() && $emails->isEmpty()) {
            return;
        }

        $users = User::where('role', 'guru')
            ->where(function ($query) use ($nips, $emails) {
                if ($nips->isNotEmpty()) {
                    $query->whereIn('nis', $nips);
                }

                if ($emails->isNotEmpty()) {
                    $query->orWhereIn('email', $emails);
                }
            })
            ->get();

        $usersByNip = $users->keyBy('nis');
        $usersByEmail = $users->filter(fn ($user) => filled($user->email))->keyBy('email');

        $gurus->each(function ($guru) use ($usersByNip, $usersByEmail) {
            $guru->setRelation(
                'linkedUser',
                $usersByNip->get($guru->nip) ?? $usersByEmail->get($guru->email)
            );
        });
    }

    public function create()
    {
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $badges = \App\Models\Badge::orderBy('nama_badge')->get();
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('admin.guru.create', compact('jurusans', 'badges', 'kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip'        => 'required|string|max:50|unique:guru,nip',
            'nama'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255|unique:guru,email',
            'phone'      => 'nullable|string|max:50',
            'kategori'   => 'nullable|in:normada,produktif',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'kelas_ids'  => 'nullable|array',
            'kelas_ids.*'=> 'exists:kelas,id',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'badge_ids'  => 'nullable|array',
            'badge_ids.*'=> 'exists:badge,id',
        ]);

        if (!\App\Services\ProfanityFilterService::isClean($request->nama) || ($request->filled('bio') && !\App\Services\ProfanityFilterService::isClean($request->bio))) {
            return back()->withInput()->with('error', 'Nama atau deskripsi diri guru mengandung kata yang melanggar etika moderasi bahasa.');
        }

        $data = $request->except(['photo', 'badge_ids', 'kelas_ids']);
        $data['kategori'] = $request->input('kategori') ?: 'normada';

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('guru', 'public');
            $data['photo'] = $path;

            try {
                $publicGuruDir = public_path('storage/guru');
                if (!\Illuminate\Support\Facades\File::exists($publicGuruDir)) {
                    @\Illuminate\Support\Facades\File::makeDirectory($publicGuruDir, 0775, true, true);
                }
                $src = storage_path('app/public/' . $path);
                if (\Illuminate\Support\Facades\File::exists($src) && is_dir($publicGuruDir) && is_writable($publicGuruDir)) {
                    @copy($src, public_path('storage/' . $path));
                }
            } catch (\Throwable $e) {}
        }

        $guru = Guru::create($data);
        $guru->kelas()->sync($request->input('kelas_ids', []));

        if ($request->filled('badge_ids')) {
            $periodeAktif = \App\Models\Periode::where('status', 'aktif')->first() ?? \App\Models\Periode::first();
            foreach ($request->input('badge_ids', []) as $badgeId) {
                \App\Models\Penghargaan::firstOrCreate([
                    'guru_id'    => $guru->id,
                    'badge_id'   => $badgeId,
                    'periode_id' => $periodeAktif?->id,
                ]);
            }
        }

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan!');
    }

    /**
     * Import Jadwal dan Pembagian Jam Mengajar Guru dari Spreadsheet Excel / CSV
     */
    public function importJadwal(Request $request, \App\Services\GuruJadwalImportService $service)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:15360',
            'replace_existing' => 'nullable',
            'create_if_not_found' => 'nullable',
        ], [
            'file.required' => 'Silakan pilih file spreadsheet (.xlsx, .xls, .csv).',
            'file.mimes'    => 'Format file harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'file.max'      => 'Ukuran file maksimal adalah 15 MB.',
        ]);

        $replaceExisting = $request->boolean('replace_existing', true);
        $createIfNotFound = $request->boolean('create_if_not_found', false);

        try {
            $result = $service->import($request->file('file'), $replaceExisting, $createIfNotFound);

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['message'] ?? 'Gagal memproses file.');
            }

            $msg = $result['message'];
            if (!empty($result['unmatched_teachers'])) {
                $unmatchedCount = count($result['unmatched_teachers']);
                $msg .= " Catatan: Terdapat {$unmatchedCount} nama guru di file yang belum terdaftar di GuruKuu.";
            }

            return redirect()->route('admin.guru.index')
                ->with('success', $msg)
                ->with('import_details', $result);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Import Jadwal Guru Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Download Template Spreadsheet (.xlsx) Matriks Pembagian Kelas Mengajar Guru
     * Format matriks SK Kurikulum Sekolah (NO, KODE GURU, NAMA GURU, MATA PELAJARAN, Kolom Kelas Mengajar)
     */
    public function downloadTemplateJadwal()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Matriks Pembagian Mengajar');

        // 🏫 Ambil seluruh rombel kelas terdaftar di database sekolah
        $allKelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        if ($allKelas->isEmpty()) {
            $sampleClassNames = [
                'X TO 1', 'X TO 2', 'XI TO 1', 'XI TO 2', 'XII TO 1', 'XII TO 2',
                'X PPLG 1', 'X PPLG 2', 'XI PPLG 1', 'XI PPLG 2', 'XII PPLG 1', 'XII PPLG 2',
                'X MPLB 1', 'X MPLB 2', 'X MPLB 3', 'XI MPLB 1', 'XI MPLB 2', 'XI MPLB 3', 'XII MPLB 1', 'XII MPLB 2', 'XII MPLB 3',
                'X PM 1', 'X PM 2', 'XI PM 1', 'XI PM 2', 'XII PM 1', 'XII PM 2',
                'X AKL 1', 'X AKL 2', 'XI AKL 1', 'XI AKL 2', 'XII AKL 1', 'XII AKL 2'
            ];
            $classList = collect($sampleClassNames)->map(fn($n) => (object)['id' => null, 'nama_lengkap' => $n]);
        } else {
            $classList = $allKelas->map(function($k) {
                $fullName = str_starts_with(strtoupper($k->nama_kelas), $k->tingkat)
                    ? $k->nama_kelas
                    : $k->tingkat . ' ' . $k->nama_kelas;
                return (object)['id' => $k->id, 'nama_lengkap' => $fullName];
            });
        }

        $totalKelas = $classList->count();
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(4 + $totalKelas);

        // 🌟 1. Judul Header Dokumen SK (Langsung mulai dari Baris 1, efisien tanpa membuang ruang atas)
        $sheet->setCellValue('A1', 'PEMBAGIAN KELAS BELAJAR MENGAJAR');
        $sheet->setCellValue('A2', 'SMK NEGERI 1 BANGSRI - TAHUN AJARAN 2026/2027');

        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->mergeCells("A2:{$lastColLetter}2");

        $titleStyle = [
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '003366']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];
        $sheet->getStyle('A1:A2')->applyFromArray($titleStyle);
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(8); // Spacer row

        // 🌟 2. Header Tabel Matriks (Baris 4 & 5)
        $sheet->mergeCells('A4:A5')->setCellValue('A4', 'NO');
        $sheet->mergeCells('B4:B5')->setCellValue('B4', 'KODE GURU / NIP');
        $sheet->mergeCells('C4:C5')->setCellValue('C4', 'NAMA GURU');
        $sheet->mergeCells('D4:D5')->setCellValue('D4', 'MATA PELAJARAN');

        // Merged header di atas kolom rombel kelas (Tanpa kolom kalkulasi jam kurikulum)
        $sheet->mergeCells("E4:{$lastColLetter}4")->setCellValue('E4', 'PEMBAGIAN KELAS MENGAJAR (Cukup beri angka 1 pada kelas yang diajar)');

        // Baris 5: Nama-nama rombel kelas
        $cIndex = 5;
        foreach ($classList as $cls) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIndex);
            $sheet->setCellValue($colLetter . '5', $cls->nama_lengkap);
            $sheet->getColumnDimension($colLetter)->setWidth(11);
            $cIndex++;
        }

        // Header Style Utama (Baris 4)
        $headerTableStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '003366']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
        $sheet->getStyle("A4:{$lastColLetter}4")->applyFromArray($headerTableStyle);
        $sheet->getStyle('A4:D5')->applyFromArray($headerTableStyle);

        // Subheader Rombel Kelas (Baris 5)
        $classHeaderStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0284C7']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle("E5:{$lastColLetter}5")->applyFromArray($classHeaderStyle);

        $sheet->getRowDimension(4)->setRowHeight(26);
        $sheet->getRowDimension(5)->setRowHeight(28);

        // Lebar kolom utama
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(22); // Lebar cukup untuk NIP 18 digit
        $sheet->getColumnDimension('C')->setWidth(34);
        $sheet->getColumnDimension('D')->setWidth(26);

        // 🌟 3. Data Baris Guru (Mulai Baris 6)
        $teachers = Guru::with('kelas')->orderBy('nama')->get();
        $startRow = 6;

        if ($teachers->isNotEmpty()) {
            foreach ($teachers as $idx => $g) {
                $currRow = $startRow + $idx;
                $nipStr = (string)($g->nip ?: ('G' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT)));

                $sheet->setCellValue('A' . $currRow, $idx + 1);
                // Set explicit text agar NIP 18 digit tidak diubah menjadi notasi ilmiah (e.g. 1.97803E+17)
                $sheet->setCellValueExplicit('B' . $currRow, $nipStr, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $currRow, $g->nama);
                $sheet->setCellValue('D' . $currRow, $g->kategori ? ucfirst($g->kategori) : 'Produktif / Normada');

                $guruClassIds = $g->kelas->pluck('id')->toArray();
                $colIdx = 5;
                foreach ($classList as $cls) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                    if ($cls->id && in_array($cls->id, $guruClassIds)) {
                        $sheet->setCellValue($colLetter . $currRow, 1);
                        $sheet->getStyle($colLetter . $currRow)->getFont()->setBold(true);
                    }
                    $colIdx++;
                }
                $sheet->getRowDimension($currRow)->setRowHeight(22);
            }
            $endRow = $startRow + $teachers->count() - 1;
        } else {
            // Contoh baris guru sesuai format SK mengajar di screenshot
            $sampleData = [
                ['BG', 'Muhammad Tsiqotuddin, S.Pd.I', 'Pendidikan Agama Islam', [0, 1]],
                ['BM', 'Rosma Azizah, S.Pd.I', 'Pendidikan Agama Islam', [2, 6]],
                ['BN', "M. Ni'amir Rohman, S.Pd.I", 'Pendidikan Pancasila', [6, 9]],
                ['CM', 'Harsoyo, S.Pd.I', 'Pendidikan Agama Islam', [12, 13]],
                ['AF', 'Devitta Nia Wulandari, S.Pd', 'Bahasa Indonesia', [6, 7]],
            ];

            foreach ($sampleData as $idx => $row) {
                $currRow = $startRow + $idx;
                $sheet->setCellValue('A' . $currRow, $idx + 1);
                $sheet->setCellValueExplicit('B' . $currRow, (string)$row[0], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $currRow, $row[1]);
                $sheet->setCellValue('D' . $currRow, $row[2]);

                foreach ($row[3] as $targetIndex) {
                    if ($targetIndex < $totalKelas) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(5 + $targetIndex);
                        $sheet->setCellValue($colLetter . $currRow, 1);
                        $sheet->getStyle($colLetter . $currRow)->getFont()->setBold(true);
                    }
                }
                $sheet->getRowDimension($currRow)->setRowHeight(22);
            }
            $endRow = $startRow + count($sampleData) - 1;
        }

        // Format kolom NIP/Kode Guru sebagai Text murni
        $sheet->getStyle("B4:B{$endRow}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Alignment kolom NO, Kode Guru, dan seluruh sel kelas di tengah (Center)
        $sheet->getStyle("A6:B{$endRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E6:{$lastColLetter}{$endRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Border tipis seluruh tabel
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ];
        $sheet->getStyle("A4:{$lastColLetter}{$endRow}")->applyFromArray($borderStyle);

        // Freeze pane di E6 sehingga kolom NO, Kode, Nama Guru, dan Mapel tetap tampak saat digeser
        $sheet->freezePane('E6');

        // 🌟 Sheet 2: Petunjuk Format Pengisian
        $sheetHelp = $spreadsheet->createSheet();
        $sheetHelp->setTitle('Petunjuk Format');
        $sheetHelp->setCellValue('A1', 'PETUNJUK FORMAT PEMBAGIAN KELAS MENGAJAR GURUKUU');
        $sheetHelp->mergeCells('A1:C1');
        $sheetHelp->getStyle('A1')->getFont()->setBold(true)->setSize(12)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('003366'));

        $instructions = [
            '1. Template ini menggunakan format Matriks SK Jadwal / Pembagian Kelas Mengajar.',
            '2. Cukup berikan angka 1 (atau tanda v) pada kolom kelas yang diajar oleh guru yang bersangkutan.',
            '3. Kolom kelas yang tidak diajar oleh guru cukup dibiarkan kosong.',
            '4. Bagian jumlah jam pelajaran mingguan / total jam tidak perlu diisi (cukup penandaan kelas).',
            '5. Gelar akademik guru (S.Pd, M.Pd, S.T, dll) otomatis dibersihkan saat pencocokan ke database GuruKuu.',
        ];

        foreach ($instructions as $hIdx => $text) {
            $sheetHelp->setCellValue('A' . ($hIdx + 3), $text);
        }
        $sheetHelp->getColumnDimension('A')->setWidth(90);

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'Template_SK_Pembagian_Kelas_Mengajar_' . date('Ymd') . '.xlsx';

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function show(Guru $guru)
    {
        $periodeAktif = \App\Models\Periode::where('status', 'aktif')->first();
        $periodeId = $periodeAktif?->id;

        $allPenilaian = \App\Models\Penilaian::where('guru_id', $guru->id)
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->get();

        $stats = [
            'total_penilaian' => $allPenilaian->count(),
            'rata_kedisiplinan' => $allPenilaian->avg('kedisiplinan') ?? 0,
            'rata_komunikasi' => $allPenilaian->avg('komunikasi') ?? 0,
            'rata_tanggung_jawab' => $allPenilaian->avg('tanggung_jawab') ?? 0,
            'rata_kreativitas' => $allPenilaian->avg('kreativitas') ?? 0,
            'rata_keramahan' => $allPenilaian->avg('keramahan') ?? 0,
        ];

        $semuaFeedback = \App\Models\Penilaian::with('siswa')
            ->where('guru_id', $guru->id)
            ->where(function($q) {
                $q->where('is_censored', false)->orWhereNull('is_censored');
            })
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->latest()
            ->get();

        $arsipPeriode = \App\Models\Periode::where('id', '!=', $periodeId)
            ->whereHas('penilaian', function($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->with(['penilaian' => function($q) use ($guru) {
                $q->where('guru_id', $guru->id)->latest();
            }])
            ->latest('tanggal_mulai')
            ->get();

        $guru->load(['kelas.jurusan', 'penghargaan.badge']);
        return view('admin.guru.show', compact('guru', 'periodeAktif', 'stats', 'semuaFeedback', 'arsipPeriode'));
    }

    public function edit(Guru $guru)
    {
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $badges = \App\Models\Badge::orderBy('nama_badge')->get();
        $guru->load(['kelas', 'penghargaan']);
        $assignedBadgeIds = $guru->penghargaan->pluck('badge_id')->toArray();
        return view('admin.guru.edit', compact('guru', 'kelasList', 'badges', 'assignedBadgeIds'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nip'        => 'required|string|max:50|unique:guru,nip,' . $guru->id,
            'nama'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255|unique:guru,email,' . $guru->id,
            'phone'      => 'nullable|string|max:50',
            'password'   => 'nullable|min:6',
            'kategori'   => 'nullable|in:normada,produktif',
            'kelas_ids'  => 'nullable|array',
            'kelas_ids.*' => 'exists:kelas,id',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'badge_ids'  => 'nullable|array',
            'badge_ids.*'=> 'exists:badge,id',
        ]);

        if (!\App\Services\ProfanityFilterService::isClean($request->nama) || ($request->filled('bio') && !\App\Services\ProfanityFilterService::isClean($request->bio))) {
            return back()->withInput()->with('error', 'Nama atau deskripsi diri guru mengandung kata yang melanggar etika moderasi bahasa.');
        }

        $oldNip = $guru->nip;
        $oldEmail = $guru->email;

        $data = $request->except(['photo', 'kelas_ids', 'badge_ids', 'password']);
        $data['kategori'] = $request->input('kategori') ?: ($guru->kategori ?: 'normada');

        if ($request->hasFile('photo')) {
            if ($guru->photo && Storage::disk('public')->exists($guru->photo)) {
                Storage::disk('public')->delete($guru->photo);
            }
            $path = $request->file('photo')->store('guru', 'public');
            $data['photo'] = $path;

            try {
                $publicGuruDir = public_path('storage/guru');
                if (!\Illuminate\Support\Facades\File::exists($publicGuruDir)) {
                    @\Illuminate\Support\Facades\File::makeDirectory($publicGuruDir, 0775, true, true);
                }
                $src = storage_path('app/public/' . $path);
                if (\Illuminate\Support\Facades\File::exists($src) && is_dir($publicGuruDir) && is_writable($publicGuruDir)) {
                    @copy($src, public_path('storage/' . $path));
                }
            } catch (\Throwable $e) {}
        }

        $guru->update($data);
        $guru->kelas()->sync($request->input('kelas_ids', []));

        // Sync Badges
        $periodeAktif = \App\Models\Periode::where('status', 'aktif')->first() ?? \App\Models\Periode::first();
        \App\Models\Penghargaan::where('guru_id', $guru->id)->delete();
        if ($request->filled('badge_ids')) {
            foreach ($request->input('badge_ids', []) as $badgeId) {
                \App\Models\Penghargaan::create([
                    'guru_id'    => $guru->id,
                    'badge_id'   => $badgeId,
                    'periode_id' => $periodeAktif?->id,
                ]);
            }
        }

        // Sinkronkan ke akun User login sistem
        $linkedUser = \App\Models\User::where('role', 'guru')
            ->where(function($q) use ($oldNip, $oldEmail, $guru) {
                if ($oldNip !== null && $oldNip !== '') {
                    $q->where('nis', (string) $oldNip);
                }
                if (!empty($oldEmail)) {
                    $q->orWhere('email', $oldEmail);
                }
                if ($guru->nip !== null && $guru->nip !== '') {
                    $q->orWhere('nis', (string) $guru->nip);
                }
                if (!empty($guru->email)) {
                    $q->orWhere('email', $guru->email);
                }
            })
            ->first();

        if ($linkedUser) {
            $userUpdate = [
                'name' => $guru->nama,
                'nis'  => (string) $guru->nip,
            ];
            if (!empty($data['photo'])) {
                $userUpdate['photo'] = $data['photo'];
            }
            if (!empty($data['email'])) {
                $userUpdate['email'] = $data['email'];
            }
            if ($request->filled('password')) {
                $userUpdate['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }
            $linkedUser->update($userUpdate);
        } else {
            \App\Models\User::create([
                'name' => $guru->nama,
                'nis'  => (string) $guru->nip,
                'email' => $guru->email ?: ($guru->nip . '@gurukuu.local'),
                'password' => \Illuminate\Support\Facades\Hash::make($request->filled('password') ? $request->password : (string) $guru->nip),
                'role' => 'guru',
                'is_active' => true,
            ]);
        }

        \App\Models\Guru::clearLeaderboardCache();

        return redirect()->route('admin.guru.index')->with('success', 'Data guru dan sinkronisasi akun login berhasil diperbarui!');
    }

    public function destroy(Guru $guru)
    {
        if ($guru->photo && Storage::disk('public')->exists($guru->photo)) {
            Storage::disk('public')->delete($guru->photo);
        }
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus!');
    }

    public function resetPassword(Request $request, Guru $guru)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $linkedUser = \App\Models\User::where('nis', $guru->nip)
            ->orWhere('email', $guru->email)
            ->where('role', 'guru')
            ->first();

        if ($linkedUser) {
            $linkedUser->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);
            return back()->with('success', "Password akun login guru {$guru->nama} berhasil direset!");
        }

        return back()->with('error', "Akun login pengguna untuk guru {$guru->nama} belum terdaftar.");
    }

    public function toggleStatus(Request $request, Guru $guru)
    {
        $linkedUser = \App\Models\User::where('nis', $guru->nip)
            ->orWhere('email', $guru->email)
            ->where('role', 'guru')
            ->first();

        if (!$linkedUser) {
            return back()->with('error', "Akun login pengguna untuk guru {$guru->nama} belum terdaftar di sistem.");
        }

        if ($linkedUser->is_active) {
            $deactivationType = $request->input('deactivation_type', 'permanen');
            $reason = $request->input('deactivated_reason', 'Akun dinonaktifkan oleh Admin / Operator Sekolah.');
            $deactivatedUntil = null;

            if ($deactivationType === 'berkala') {
                if ($request->input('duration_days') === '5_hours') {
                    $deactivatedUntil = now()->addHours(5);
                } elseif ($request->filled('custom_until')) {
                    $deactivatedUntil = \Carbon\Carbon::parse($request->input('custom_until'))->endOfDay();
                } else {
                    $days = (int) $request->input('duration_days', 3);
                    $deactivatedUntil = now()->addDays($days);
                }
            }

            $linkedUser->update([
                'is_active' => false,
                'deactivation_type' => $deactivationType,
                'deactivated_until' => $deactivatedUntil,
                'deactivated_reason' => $reason,
            ]);

            $typeLabel = $deactivationType === 'berkala'
                ? "secara berkala hingga " . $deactivatedUntil->format('d M Y H:i')
                : "secara permanen";

            return back()->with('success', "Akun login guru {$guru->nama} berhasil dinonaktifkan {$typeLabel}.");
        } else {
            // Aktifkan kembali
            $linkedUser->update([
                'is_active' => true,
                'deactivation_type' => null,
                'deactivated_until' => null,
                'deactivated_reason' => null,
            ]);

            return back()->with('success', "Akun login guru {$guru->nama} berhasil diaktifkan kembali!");
        }
    }
}
