<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $no = 0;

    public function collection()
    {
        return User::where('role', 'siswa')
            ->with(['jurusan', 'kelas'])
            ->orderBy('name', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Jurusan',
            'Tanggal Lahir',
            'Status Akun',
        ];
    }

    public function map($siswa): array
    {
        $this->no++;
        $kelasRel = $siswa->relationLoaded('kelas') ? $siswa->getRelation('kelas') : null;
        if ($kelasRel && $kelasRel->isNotEmpty()) {
            $first = $kelasRel->first();
            $kelasName = $first->nama_kelas . ($first->tingkat ? ' Kelas ' . $first->tingkat : '');
        } elseif (is_string($siswa->kelas) && !empty($siswa->kelas)) {
            $kelasName = $siswa->kelas;
        } else {
            $kelasName = '-';
        }

        return [
            $this->no,
            "'" . ($siswa->nis ?? '-'),
            $siswa->name,
            $kelasName,
            $siswa->jurusan?->nama_jurusan ?? '-',
            $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d-m-Y') : '-',
            $siswa->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function title(): string
    {
        return 'Data Siswa';
    }
}
