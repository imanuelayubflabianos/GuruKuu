<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Guru - GuruKuu</title>
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
        .footer { margin-top: 30px; text-align: right; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Master Data Guru Pengajar</div>
        <div class="subtitle">SMK Negeri 1 Bangsri - Sistem Informasi GuruKuu</div>
        <div class="meta">Total: {{ $guru->count() }} Guru | Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">No</th>
                <th style="width: 110px;">NIP</th>
                <th>Nama Guru</th>
                <th>Email</th>
                <th>Kontak / HP</th>
                <th>Jurusan</th>
                <th class="text-center" style="width: 70px;">Kepuasan</th>
                <th class="text-center" style="width: 70px;">Ulasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guru as $index => $g)
            @php $pct = round(($g->rata_rata_nilai / 5) * 100); @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $g->nip }}</td>
                <td><strong>{{ $g->nama }}</strong></td>
                <td>{{ $g->email ?? '-' }}</td>
                <td>{{ $g->phone ?? '-' }}</td>
                <td>{{ $g->jurusan?->nama_jurusan ?? 'Umum' }}</td>
                <td class="text-center" style="font-weight: bold; color: #003366;">{{ $pct }}%</td>
                <td class="text-center">{{ $g->total_penilaian }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">Belum ada data guru.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen resmi di-generate oleh Sistem GuruKuu - SMK Negeri 1 Bangsri
    </div>
</body>
</html>
