<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GuruExport;
use App\Exports\LeaderboardExport;
use App\Exports\SiswaExport;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Periode;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class ExportController extends Controller
{
    public function leaderboardExcel()
    {
        $filename = 'leaderboard_guru_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new LeaderboardExport, $filename);
    }

    public function leaderboardPdf()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $leaderboard = Guru::with('jurusan')
            ->withRatings()
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->get();


        $pdf = Pdf::loadView('exports.leaderboard-pdf', compact('leaderboard', 'periodeAktif'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('leaderboard_guru_' . date('Y-m-d_His') . '.pdf');
    }

    public function guruExcel()
    {
        $filename = 'data_guru_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new GuruExport, $filename);
    }

    public function guruPdf()
    {
        $guru = Guru::with('jurusan')->orderBy('nama', 'asc')->get();
        $pdf = Pdf::loadView('exports.guru-pdf', compact('guru'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('data_guru_' . date('Y-m-d_His') . '.pdf');
    }

    public function siswaExcel()
    {
        $filename = 'data_siswa_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new SiswaExport, $filename);
    }

    public function siswaPdf()
    {
        $siswa = User::where('role', 'siswa')
            ->with(['jurusan', 'kelas'])
            ->orderBy('name', 'asc')
            ->get();

        $pdf = Pdf::loadView('exports.siswa-pdf', compact('siswa'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('data_siswa_' . date('Y-m-d_His') . '.pdf');
    }

    public function allZip()
    {
        $tempDir = storage_path('app/temp_export_' . time());
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $periodeAktif = Periode::where('status', 'aktif')->first();
        $leaderboard = Guru::with('jurusan')
            ->withRatings()
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->get();
        $guru = Guru::with('jurusan')->orderBy('nama', 'asc')->get();
        $siswa = User::where('role', 'siswa')->with(['jurusan', 'kelas'])->orderBy('name', 'asc')->get();

        // 1. Generate Excels
        Excel::store(new LeaderboardExport, 'temp_leaderboard.xlsx', 'local');
        Excel::store(new GuruExport, 'temp_guru.xlsx', 'local');
        Excel::store(new SiswaExport, 'temp_siswa.xlsx', 'local');

        $leadPath = \Illuminate\Support\Facades\Storage::disk('local')->path('temp_leaderboard.xlsx');
        $guruPath = \Illuminate\Support\Facades\Storage::disk('local')->path('temp_guru.xlsx');
        $siswaPath = \Illuminate\Support\Facades\Storage::disk('local')->path('temp_siswa.xlsx');

        if (file_exists($leadPath)) File::copy($leadPath, $tempDir . '/Leaderboard_Guru.xlsx');
        if (file_exists($guruPath)) File::copy($guruPath, $tempDir . '/Data_Guru.xlsx');
        if (file_exists($siswaPath)) File::copy($siswaPath, $tempDir . '/Data_Siswa.xlsx');

        \Illuminate\Support\Facades\Storage::disk('local')->delete(['temp_leaderboard.xlsx', 'temp_guru.xlsx', 'temp_siswa.xlsx']);

        // 2. Generate PDFs
        $pdfLeaderboard = Pdf::loadView('exports.leaderboard-pdf', compact('leaderboard', 'periodeAktif'))->setPaper('a4', 'portrait');
        $pdfLeaderboard->save($tempDir . '/Leaderboard_Guru.pdf');

        $pdfGuru = Pdf::loadView('exports.guru-pdf', compact('guru'))->setPaper('a4', 'portrait');
        $pdfGuru->save($tempDir . '/Data_Guru.pdf');

        $pdfSiswa = Pdf::loadView('exports.siswa-pdf', compact('siswa'))->setPaper('a4', 'portrait');
        $pdfSiswa->save($tempDir . '/Data_Siswa.pdf');

        // 3. Zip all files
        $zipFilename = 'Laporan_Lengkap_GuruKuu_' . date('Y-m-d_His') . '.zip';
        $zipPath = storage_path('app/' . $zipFilename);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = File::files($tempDir);
            foreach ($files as $file) {
                $zip->addFile($file->getRealPath(), $file->getFilename());
            }
            $zip->close();
        }

        // Clean temp dir
        File::deleteDirectory($tempDir);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}