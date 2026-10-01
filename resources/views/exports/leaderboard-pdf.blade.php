<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Leaderboard Guru - {{ $siteTitle ?? 'GuruKuu' }}</title>
    <style>
        @page {
            margin: 18mm 15mm 18mm 15mm;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10.5px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2.5px solid #003366;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }
        .meta {
            font-size: 9.5px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #003366;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 9px 10px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #002244;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 9.5px;
        }
        .badge-rank1 {
            background-color: #fef08a;
            color: #854d0e;
            border: 1px solid #fde047;
        }
        .badge-rank2 {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .badge-rank3 {
            background-color: #ffedd5;
            color: #9a3412;
            border: 1px solid #fdba74;
        }
        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Laporan Peringkat Leaderboard Guru</div>
        <div class="subtitle">{{ $schoolName ?? 'SMK Negeri 1 Bangsri' }} &bull; Sistem Penilaian Guru {{ $siteTitle ?? 'GuruKuu' }}</div>
        <div class="meta">Periode: {{ $periodeAktif->nama_periode ?? 'Semua Periode' }} &nbsp;|&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 140px;">Peringkat</th>
                <th style="width: 120px;">NIP</th>
                <th>Nama Guru</th>
                <th>Jurusan / Keahlian</th>
                <th class="text-center" style="width: 90px;">Kepuasan (%)</th>
                <th class="text-center" style="width: 90px;">Rata-rata Skor</th>
                <th class="text-center" style="width: 95px;">Total Penilaian</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaderboard as $index => $g)
            @php $pct = round(($g->rata_rata_nilai / 5) * 100); @endphp
            <tr>
                <td class="text-center">
                    @if($g->leaderboard_rank === 1) <span class="badge badge-rank1">Juara 1 (#1)</span>
                    @elseif($g->leaderboard_rank === 2) <span class="badge badge-rank2">Juara 2 (#2)</span>
                    @elseif($g->leaderboard_rank === 3) <span class="badge badge-rank3">Juara 3 (#3)</span>
                    @elseif($g->leaderboard_rank) <strong>#{{ $g->leaderboard_rank }}</strong>
                    @else <span style="color: #64748b; font-size: 8.5px; font-weight: 500;">Belum cukup data ({{ $g->total_penilaian }}/5)</span>
                    @endif
                </td>
                <td>{{ $g->nip }}</td>
                <td><strong>{{ $g->nama }}</strong></td>
                <td>{{ $g->jurusan?->nama_jurusan ?? 'Umum / Terbuka' }}</td>
                <td class="text-center" style="font-weight: bold; color: #003366;">{{ $pct }}%</td>
                <td class="text-center">{{ number_format($g->rata_rata_nilai, 2) }} / 5.00</td>
                <td class="text-center">{{ $g->total_penilaian }} ulasan</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 24px; color: #64748b;">Belum ada data peringkat guru pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini di-generate secara otomatis oleh Sistem {{ $siteTitle ?? 'GuruKuu' }} &bull; {{ $schoolName ?? 'SMK Negeri 1 Bangsri' }}
    </div>
</body>
</html>
