<?php

namespace App\Exports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class GuruExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithColumnWidths
{
    protected $no = 0;

    public function collection()
    {
        return Guru::with('jurusan')->orderBy('nama', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'Nama Guru',
            'Email',
            'Nomor Telepon',
            'Jurusan',
            'Kepuasan (%)',
            'Total Ulasan',
        ];
    }

    public function map($guru): array
    {
        $this->no++;
        $persen = round(($guru->rata_rata_nilai / 5) * 100);

        return [
            $this->no,
            "'" . $guru->nip,
            $guru->nama,
            $guru->email ?? '-',
            $guru->phone ? "'" . $guru->phone : '-',
            $guru->jurusan?->nama_jurusan ?? 'Umum',
            $persen . '%',
            $guru->total_penilaian . ' ulasan',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 24,  // NIP
            'C' => 35,  // Nama Guru
            'D' => 30,  // Email
            'E' => 20,  // Nomor Telepon
            'F' => 24,  // Jurusan
            'G' => 18,  // Kepuasan (%)
            'H' => 18,  // Total Ulasan
        ];
    }

    public function title(): string
    {
        return 'Data Guru';
    }
}
