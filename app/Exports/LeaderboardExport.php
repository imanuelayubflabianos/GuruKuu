<?php

namespace App\Exports;

use App\Models\Guru;
use App\Models\Periode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LeaderboardExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $no = 0;

    public function collection()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        
        $query = Guru::with('jurusan')
            ->withRatings()
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->get();

        return $query;
    }

    public function headings(): array
    {
        return [
            'Ranking',
            'NIP',
            'Nama Guru',
            'Jurusan / Keahlian',
            'Kepuasan (%)',
            'Rata-rata Skor (Skala 5)',
            'Total Ulasan Siswa',
            'Status Mengajar',
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
            $guru->jurusan?->nama_jurusan ?? 'Umum / Terbuka',
            $persen . '%',
            number_format($guru->rata_rata_nilai, 2),
            $guru->total_penilaian,
            'Aktif',
        ];
    }

    public function title(): string
    {
        return 'Leaderboard Guru';
    }
}
