<?php

namespace App\Exports;

use App\Models\Guru;
use App\Models\Periode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LeaderboardExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithColumnWidths
{
    public function collection()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        return Guru::leaderboardFor('rating', null, $periodeAktif?->id);
    }

    public function headings(): array
    {
        return [
            'Peringkat',
            'NIP',
            'Nama Guru',
            'Jurusan / Keahlian',
            'Kepuasan (%)',
            'Rata-rata Skor',
            'Total Penilaian',
        ];
    }

    public function map($guru): array
    {
        $persen = round(($guru->rata_rata_nilai / 5) * 100);
        $rankText = $guru->leaderboard_rank 
            ? '#' . $guru->leaderboard_rank 
            : 'Belum cukup data (' . $guru->total_penilaian . '/' . Guru::MIN_PENILAIAN_LEADERBOARD . ')';

        return [
            $rankText,
            "'" . $guru->nip,
            $guru->nama,
            $guru->jurusan?->nama_jurusan ?? 'Umum / Terbuka',
            $persen . '%',
            number_format($guru->rata_rata_nilai, 2) . ' / 5.00',
            $guru->total_penilaian . ' ulasan',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,  // Peringkat (Belum cukup data (X/5) / #1)
            'B' => 24,  // NIP
            'C' => 35,  // Nama Guru
            'D' => 28,  // Jurusan / Keahlian
            'E' => 18,  // Kepuasan (%)
            'F' => 20,  // Rata-rata Skor
            'G' => 20,  // Total Penilaian
        ];
    }

    public function title(): string
    {
        return 'Leaderboard Guru';
    }
}
