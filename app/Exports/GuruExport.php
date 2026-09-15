<?php

namespace App\Exports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class GuruExport implements FromCollection, WithHeadings, WithMapping, WithTitle
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
            'Total Ulasan',
            'Rating Kepuasan (%)',
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
            $guru->total_penilaian,
            $persen . '%',
        ];
    }

    public function title(): string
    {
        return 'Data Guru';
    }
}
