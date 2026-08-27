<?php
// app/Exports/PenilaianExport.php

namespace App\Exports;

use App\Models\Penilaian;
use App\Models\Periode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PenilaianExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        $periodeAktif = Periode::getActivePeriode();
        return Penilaian::with(['siswa', 'guru.jurusan', 'periode'])
            ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'No', 'Tanggal', 'NIS Siswa', 'Nama Siswa', 'Kelas',
            'NIP Guru', 'Nama Guru', 'Kategori', 'Jurusan',
            'Kedisiplinan', 'Cara Mengajar', 'Komunikasi',
            'Tanggung Jawab', 'Kreativitas', 'Keramahan',
            'Total Nilai', 'Kritik', 'Saran', 'Periode'
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->created_at->format('d-m-Y'),
            $row->siswa->nis,
            $row->siswa->name,
            $row->siswa->kelas,
            $row->guru->nip,
            $row->guru->nama,
            $row->guru->kategori_label,
            $row->guru->jurusan?->nama_jurusan,
            $row->kedisiplinan,
            $row->cara_mengajar,
            $row->komunikasi,
            $row->tanggung_jawab,
            $row->kreativitas,
            $row->keramahan,
            number_format($row->total_nilai, 2),
            $row->kritik,
            $row->saran,
            $row->periode->nama_periode,
        ];
    }

    public function title(): string
    {
        return 'Penilaian Guru';
    }
}