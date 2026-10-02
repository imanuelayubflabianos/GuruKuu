<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GuruJadwalImportService
{
    /**
     * Common academic titles to clean when matching teacher names
     */
    protected array $titles = [
        's.pd.i', 's.pd', 'm.pd.i', 'm.pd', 's.t', 'm.t', 's.si', 'm.si',
        's.kom', 'm.kom', 's.psi', 'm.psi', 's.e', 'm.m', 's.ag', 'm.ag',
        'drs.', 'dra.', 'dr.', 'prof.', 'h.', 'hj.', 'lc.', 'b.a', 'm.a',
        's.sos', 'm.sos', 's.sn', 'm.sn', 's.pt', 's.p', 's.kh',
    ];

    /**
     * Import and map teachers to classes from an uploaded spreadsheet file (.xlsx, .xls, .csv)
     *
     * @param UploadedFile $file
     * @param bool $replaceExisting
     * @param bool $createIfNotFound
     * @return array
     */
    public function import(UploadedFile $file, bool $replaceExisting = true, bool $createIfNotFound = false): array
    {
        $filePath = $file->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows)) {
            return [
                'success' => false,
                'message' => 'File spreadsheet kosong atau tidak dapat dibaca.',
            ];
        }

        // Cache all existing classes and teachers
        $allKelas = Kelas::with('jurusan')->get();
        $allGurus = Guru::with('kelas')->get();

        // 1. Detect if this is a Matrix schedule sheet (like SK Pembagian Jam Belajar Mengajar)
        $matrixDetection = $this->detectMatrixSchedule($rows, $allKelas);

        if ($matrixDetection['is_matrix']) {
            return $this->processMatrixSchedule(
                $rows,
                $matrixDetection,
                $allKelas,
                $allGurus,
                $replaceExisting,
                $createIfNotFound
            );
        }

        // 2. Otherwise process as standard list format (Nama, Kelas, Mapel)
        return $this->processListSchedule(
            $rows,
            $allKelas,
            $allGurus,
            $replaceExisting,
            $createIfNotFound
        );
    }

    /**
     * Detect if spreadsheet uses matrix format with class headers in a specific row
     */
    protected function detectMatrixSchedule(array $rows, Collection $allKelas): array
    {
        $bestRow = null;
        $bestClassCols = [];
        $maxMatches = 0;
        $headerTeacherCol = null;
        $headerMapelCol = null;
        $headerCodeCol = null;

        // Scan rows 1 to 25 to find the row with the most class column matches
        foreach (array_slice($rows, 0, 25, true) as $rowIndex => $row) {
            $classCols = [];
            foreach ($row as $colKey => $cellValue) {
                if (is_null($cellValue) || trim((string)$cellValue) === '') {
                    continue;
                }
                $matchedKelas = $this->matchKelas((string)$cellValue, $allKelas);
                if ($matchedKelas) {
                    $classCols[$colKey] = [
                        'kelas' => $matchedKelas,
                        'raw_header' => trim((string)$cellValue),
                    ];
                }
            }

            if (count($classCols) > $maxMatches) {
                $maxMatches = count($classCols);
                $bestRow = $rowIndex;
                $bestClassCols = $classCols;
            }
        }

        // A matrix schedule typically has at least 3 class columns
        if ($maxMatches >= 3 && $bestRow !== null) {
            // Find teacher name, mapel, and code columns by inspecting the header rows (current row & row above)
            $searchRows = array_filter([$bestRow - 1, $bestRow, $bestRow + 1], fn($r) => isset($rows[$r]));
            foreach ($searchRows as $rIdx) {
                foreach ($rows[$rIdx] as $colKey => $val) {
                    $norm = strtolower(trim((string)$val));
                    if (!$headerTeacherCol && (str_contains($norm, 'nama guru') || $norm === 'nama' || str_contains($norm, 'guru pengajar'))) {
                        $headerTeacherCol = $colKey;
                    }
                    if (!$headerMapelCol && (str_contains($norm, 'mata pelajaran') || str_contains($norm, 'mapel') || $norm === 'pelajaran')) {
                        $headerMapelCol = $colKey;
                    }
                    if (!$headerCodeCol && (str_contains($norm, 'kode guru') || $norm === 'kode' || $norm === 'kd' || str_contains($norm, 'nip'))) {
                        $headerCodeCol = $colKey;
                    }
                }
            }

            // Defaults if headers were slightly offset or merged
            if (!$headerTeacherCol) $headerTeacherCol = 'C';
            if (!$headerMapelCol) $headerMapelCol = 'D';
            if (!$headerCodeCol) $headerCodeCol = 'B';

            return [
                'is_matrix' => true,
                'header_row' => $bestRow,
                'class_columns' => $bestClassCols,
                'teacher_col' => $headerTeacherCol,
                'mapel_col' => $headerMapelCol,
                'code_col' => $headerCodeCol,
            ];
        }

        return ['is_matrix' => false];
    }

    /**
     * Process matrix schedule (SK Pembagian Jam Mengajar per Kelas)
     */
    protected function processMatrixSchedule(
        array $rows,
        array $detection,
        Collection $allKelas,
        Collection $allGurus,
        bool $replaceExisting,
        bool $createIfNotFound
    ): array {
        $headerRow = $detection['header_row'];
        $classCols = $detection['class_columns'];
        $teacherCol = $detection['teacher_col'];
        $mapelCol = $detection['mapel_col'];
        $codeCol = $detection['code_col'];

        $teacherClasses = [];
        $lastTeacherName = null;
        $lastTeacherCode = null;

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex <= $headerRow) {
                continue;
            }

            $rawTeacherName = isset($row[$teacherCol]) ? trim((string)$row[$teacherCol]) : '';
            $rawMapel = isset($row[$mapelCol]) ? trim((string)$row[$mapelCol]) : '';
            $rawCode = isset($row[$codeCol]) ? trim((string)$row[$codeCol]) : '';

            // Handle multi-row subjects for the same teacher (empty name inherits previous teacher)
            if ($rawTeacherName === '' && ($rawMapel !== '' || $rawCode !== '')) {
                $rawTeacherName = $lastTeacherName;
                if (!$rawCode) $rawCode = $lastTeacherCode;
            }

            if ($rawTeacherName === '') {
                continue;
            }

            // Skip table footers / summaries (e.g. "JUMLAH JAM", "TOTAL", etc.)
            $lowerName = strtolower($rawTeacherName);
            if (str_starts_with($lowerName, 'jumlah') || str_starts_with($lowerName, 'total') || str_starts_with($lowerName, 'mengetahui')) {
                continue;
            }

            $lastTeacherName = $rawTeacherName;
            $lastTeacherCode = $rawCode;

            // Find all classes in this row that have hours/values > 0
            $matchedClassIds = [];
            foreach ($classCols as $colKey => $colInfo) {
                $cellVal = isset($row[$colKey]) ? trim((string)$row[$colKey]) : '';
                // Check if numeric > 0 or has indicator
                if ($cellVal !== '' && $cellVal !== '-' && (is_numeric($cellVal) && (float)$cellVal > 0 || !is_numeric($cellVal))) {
                    $matchedClassIds[] = $colInfo['kelas']->id;
                }
            }

            if (!empty($matchedClassIds)) {
                $key = $rawTeacherName;
                if (!isset($teacherClasses[$key])) {
                    $teacherClasses[$key] = [
                        'raw_name' => $rawTeacherName,
                        'code' => $rawCode,
                        'mapel' => $rawMapel,
                        'class_ids' => [],
                    ];
                }
                $teacherClasses[$key]['class_ids'] = array_unique(array_merge($teacherClasses[$key]['class_ids'], $matchedClassIds));
                if ($rawMapel && empty($teacherClasses[$key]['mapel'])) {
                    $teacherClasses[$key]['mapel'] = $rawMapel;
                }
            }
        }

        return $this->applyTeacherClassAssignments($teacherClasses, $allGurus, $replaceExisting, $createIfNotFound);
    }

    /**
     * Process tabular list schedule (Format baris: Nama Guru, Kelas, Mapel)
     */
    protected function processListSchedule(
        array $rows,
        Collection $allKelas,
        Collection $allGurus,
        bool $replaceExisting,
        bool $createIfNotFound
    ): array {
        // Detect header row in first 5 rows
        $teacherCol = null;
        $kelasCol = null;
        $mapelCol = null;
        $startRow = 1;

        foreach (array_slice($rows, 0, 5, true) as $rowIndex => $row) {
            foreach ($row as $colKey => $val) {
                $norm = strtolower(trim((string)$val));
                if (str_contains($norm, 'guru') || $norm === 'nama') $teacherCol = $colKey;
                if (str_contains($norm, 'kelas') || str_contains($norm, 'rombel')) $kelasCol = $colKey;
                if (str_contains($norm, 'mapel') || str_contains($norm, 'pelajaran')) $mapelCol = $colKey;
            }
            if ($teacherCol && $kelasCol) {
                $startRow = $rowIndex + 1;
                break;
            }
        }

        if (!$teacherCol || !$kelasCol) {
            // Default to col A (teacher) and col B (class)
            $teacherCol = 'A';
            $kelasCol = 'B';
            $mapelCol = 'C';
        }

        $teacherClasses = [];

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex < $startRow) continue;

            $rawTeacherName = isset($row[$teacherCol]) ? trim((string)$row[$teacherCol]) : '';
            $rawKelasString = isset($row[$kelasCol]) ? trim((string)$row[$kelasCol]) : '';
            $rawMapel = isset($row[$mapelCol]) ? trim((string)$row[$mapelCol]) : '';

            if ($rawTeacherName === '' || $rawKelasString === '') continue;

            // Classes can be comma, semicolon, or slash separated
            $parts = preg_split('/[,;\/\|\n]+/', $rawKelasString);
            $matchedClassIds = [];

            foreach ($parts as $part) {
                $part = trim($part);
                if ($part === '') continue;
                $k = $this->matchKelas($part, $allKelas);
                if ($k) {
                    $matchedClassIds[] = $k->id;
                }
            }

            if (!empty($matchedClassIds)) {
                $key = $rawTeacherName;
                if (!isset($teacherClasses[$key])) {
                    $teacherClasses[$key] = [
                        'raw_name' => $rawTeacherName,
                        'code' => '',
                        'mapel' => $rawMapel,
                        'class_ids' => [],
                    ];
                }
                $teacherClasses[$key]['class_ids'] = array_unique(array_merge($teacherClasses[$key]['class_ids'], $matchedClassIds));
            }
        }

        return $this->applyTeacherClassAssignments($teacherClasses, $allGurus, $replaceExisting, $createIfNotFound);
    }

    /**
     * Apply collected class IDs to Guru models in database
     */
    protected function applyTeacherClassAssignments(
        array $teacherClasses,
        Collection $allGurus,
        bool $replaceExisting,
        bool $createIfNotFound
    ): array {
        $updatedCount = 0;
        $totalClassesAssigned = 0;
        $unmatchedTeachers = [];
        $processedList = [];

        foreach ($teacherClasses as $item) {
            $rawName = $item['raw_name'];
            $classIds = $item['class_ids'];
            $mapel = $item['mapel'];

            $guru = $this->findGuru($rawName, $allGurus);

            if (!$guru && $createIfNotFound) {
                // Auto create teacher if enabled
                $guru = Guru::create([
                    'nama' => $rawName,
                    'nip' => '0',
                    'kategori' => 'normada',
                    'bio' => $mapel ? 'Pengajar ' . $mapel : null,
                ]);
                $allGurus->push($guru);
            }

            if ($guru) {
                if ($replaceExisting) {
                    $guru->kelas()->sync($classIds);
                } else {
                    $guru->kelas()->syncWithoutDetaching($classIds);
                }

                $updatedCount++;
                $totalClassesAssigned += count($classIds);

                $processedList[] = [
                    'guru_id' => $guru->id,
                    'nama' => $guru->nama,
                    'raw_name' => $rawName,
                    'classes_count' => count($classIds),
                    'classes_names' => Kelas::whereIn('id', $classIds)->pluck('nama_kelas')->toArray(),
                    'status' => 'success',
                ];
            } else {
                $unmatchedTeachers[] = [
                    'raw_name' => $rawName,
                    'classes_count' => count($classIds),
                ];
            }
        }

        return [
            'success' => true,
            'updated_count' => $updatedCount,
            'total_classes_assigned' => $totalClassesAssigned,
            'unmatched_teachers' => $unmatchedTeachers,
            'processed_list' => $processedList,
            'message' => "Berhasil memetakan {$updatedCount} guru dengan total {$totalClassesAssigned} rombel kelas mengajar!",
        ];
    }

    /**
     * Match a header or cell string to a Kelas model
     */
    protected function matchKelas(string $raw, Collection $allKelas): ?Kelas
    {
        $str = strtoupper(trim($raw));
        $str = preg_replace('/\s+/', ' ', $str);

        // Normalize Roman numerals to numbers: X -> 10, XI -> 11, XII -> 12
        $normalized = preg_replace('/\bXII\b/', '12', $str);
        $normalized = preg_replace('/\bXI\b/', '11', $normalized);
        $normalized = preg_replace('/\bX\b/', '10', $normalized);
        $normalized = trim(str_ireplace('KELAS', '', $normalized));
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // Remove spaces for compact comparison (e.g. "10TO1" vs "10 TO 1")
        $compactNorm = str_replace(' ', '', $normalized);

        foreach ($allKelas as $k) {
            $tingkat = (string)$k->tingkat;
            $nama = strtoupper((string)$k->nama_kelas);
            $kodeJurusan = strtoupper((string)($k->jurusan?->kode_jurusan ?? ''));

            // Format variants for this class
            $combos = [
                "{$tingkat} {$nama}",
                "{$tingkat}{$nama}",
                "KELAS {$tingkat} {$nama}",
            ];

            if ($kodeJurusan) {
                // If nama_kelas is e.g. "TO 1", or "1"
                $combos[] = "{$tingkat} {$kodeJurusan} {$nama}";
                $combos[] = "{$tingkat}{$kodeJurusan}{$nama}";
            }

            foreach ($combos as $combo) {
                $cleanCombo = str_replace(' ', '', strtoupper($combo));
                if ($compactNorm === $cleanCombo) {
                    return $k;
                }
            }

            // Also check if raw contains label_singkat or similar
            if ($compactNorm === str_replace(' ', '', strtoupper($k->label_singkat))) {
                return $k;
            }
        }

        return null;
    }

    /**
     * Find a Guru from DB by name with smart title-stripping and fuzzy matching
     */
    protected function findGuru(string $rawName, Collection $allGurus): ?Guru
    {
        $cleanRaw = $this->cleanName($rawName);

        // 1. Exact match (case insensitive)
        foreach ($allGurus as $g) {
            if (strcasecmp(trim($g->nama), trim($rawName)) === 0) {
                return $g;
            }
        }

        // 2. Cleaned name match (without titles like S.Pd, M.Pd, etc.)
        foreach ($allGurus as $g) {
            $cleanG = $this->cleanName($g->nama);
            if ($cleanRaw === $cleanG) {
                return $g;
            }
        }

        // 3. Substring / contains match
        foreach ($allGurus as $g) {
            $cleanG = $this->cleanName($g->nama);
            if (strlen($cleanRaw) >= 4 && strlen($cleanG) >= 4) {
                if (str_contains($cleanG, $cleanRaw) || str_contains($cleanRaw, $cleanG)) {
                    return $g;
                }
            }
        }

        // 4. Fuzzy match using similar_text (> 85%)
        $bestGuru = null;
        $highestPercent = 0;
        foreach ($allGurus as $g) {
            $cleanG = $this->cleanName($g->nama);
            similar_text($cleanRaw, $cleanG, $percent);
            if ($percent > $highestPercent && $percent >= 85) {
                $highestPercent = $percent;
                $bestGuru = $g;
            }
        }

        return $bestGuru;
    }

    /**
     * Clean teacher name by removing academic titles, commas, dots, and extra spaces
     */
    protected function cleanName(string $name): string
    {
        $clean = strtolower($name);

        foreach ($this->titles as $title) {
            $clean = str_replace($title, ' ', $clean);
        }

        // Remove punctuation
        $clean = preg_replace('/[.,\-_()\'"]+/', ' ', $clean);
        // Normalize whitespace
        $clean = trim(preg_replace('/\s+/', ' ', $clean));

        return $clean;
    }
}
