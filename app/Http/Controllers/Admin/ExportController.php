<?php
// app/Http/Controllers/Admin/ExportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\Periode;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenilaianExport;

class ExportController extends Controller
{
    public function exportPdf()
    {
        $periodeAktif = Periode::getActivePeriode();
        $penilaian = Penilaian::with(['siswa', 'guru', 'periode'])
            ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
            ->latest()
            ->get();

        $pdf = Pdf::loadView('admin.export.penilaian-pdf', compact('penilaian', 'periodeAktif'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('penilaian-gurukuu-'.date('Y-m-d').'.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new PenilaianExport, 'penilaian-gurukuu-'.date('Y-m-d').'.xlsx');
    }
}