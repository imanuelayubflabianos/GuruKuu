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
        return Guru::leaderboardFor('rating', null, $periodeAktif?->id);
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
            'Status Leaderboard',
        ];
    }

    public function map($guru): array
    {
        $persen = round(($guru->rata_rata_nilai / 5) * 100);
        $rankText = $guru->leaderboard_rank ? '#' . $guru->leaderboard_rank : 'Belum cukup data (<' . \App\Models\Guru::MIN_PENILAIAN_LEADERBOARD . ' penilaian)';

        return [
            $rankText,
            "'" . $guru->nip,
            $guru->nama,
            $guru->jurusan?->nama_jurusan ?? 'Umum / Terbuka',
            $persen . '%',
            number_format($guru->rata_rata_nilai, 2),
            $guru->total_penilaian . ' penilaian',
            $guru->leaderboard_rank ? 'Masuk Leaderboard' : 'Belum Cukup Data',
        ];
    }

    public function title(): string
    {
        return 'Leaderboard Guru';
    }
}
