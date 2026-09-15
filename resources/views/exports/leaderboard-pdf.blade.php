<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Leaderboard Guru - GuruKuu</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #1a1a2e; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #003366; padding-bottom: 12px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #003366; text-transform: uppercase; margin-bottom: 4px; }
        .subtitle { font-size: 11px; color: #64748b; margin-bottom: 4px; }
        .meta { font-size: 10px; color: #64748b; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #003366; color: white; font-weight: bold; text-align: left; padding: 7px 10px; font-size: 10px; text-transform: uppercase; }
        td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 3px 6px; border-radius: 4px; font-weight: bold; font-size: 9px; }
        .badge-rank1 { background-color: #fef08a; color: #854d0e; }
        .badge-rank2 { background-color: #e2e8f0; color: #334155; }
        .badge-rank3 { background-color: #ffedd5; color: #9a3412; }
        .footer { margin-top: 30px; text-align: right; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Laporan Peringkat Leaderboard Guru</div>
        <div class="subtitle">SMK Negeri 1 Bangsri - Sistem Penilaian Guru GuruKuu</div>
        <div class="meta">Periode: {{ $periodeAktif->nama_periode ?? 'Semua Periode' }} | Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 50px;">Rank</th>
                <th style="width: 110px;">NIP</th>
                <th>Nama Guru</th>
                <th>Jurusan / Keahlian</th>
                <th class="text-center" style="width: 90px;">Kepuasan (%)</th>
                <th class="text-center" style="width: 80px;">Rata-rata</th>
                <th class="text-center" style="width: 80px;">Total Ulasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaderboard as $index => $g)
            @php $pct = round(($g->rata_rata_nilai / 5) * 100); @endphp
            <tr>
                <td class="text-center">
                    @if($index === 0) <span class="badge badge-rank1">#1</span>
                    @elseif($index === 1) <span class="badge badge-rank2">#2</span>
                    @elseif($index === 2) <span class="badge badge-rank3">#3</span>
                    @else #{{ $index + 1 }}
                    @endif
                </td>
                <td>{{ $g->nip }}</td>
                <td><strong>{{ $g->nama }}</strong></td>
                <td>{{ $g->jurusan?->nama_jurusan ?? 'Umum' }}</td>
                <td class="text-center" style="font-weight: bold; color: #003366;">{{ $pct }}%</td>
                <td class="text-center">{{ number_format($g->rata_rata_nilai, 2) }}/5</td>
                <td class="text-center">{{ $g->total_penilaian }} ulasan</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px;">Belum ada data peringkat guru.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini di-generate secara otomatis oleh Sistem GuruKuu - SMK Negeri 1 Bangsri
    </div>
</body>
</html>
