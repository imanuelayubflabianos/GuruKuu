<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Siswa - {{ $siteTitle ?? 'GuruKuu' }}</title>
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
        <div class="title">Master Data Siswa Terdaftar</div>
        <div class="subtitle">{{ $schoolName ?? 'SMK Negeri 1 Bangsri' }} &bull; Sistem Evaluasi Guru {{ $siteTitle ?? 'GuruKuu' }}</div>
        <div class="meta">Total Data: {{ $siswa->count() }} Siswa &nbsp;|&nbsp; Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No</th>
                <th style="width: 110px;">NIS</th>
                <th>Nama Siswa</th>
                <th style="width: 120px;">Kelas</th>
                <th>Jurusan</th>
                <th class="text-center" style="width: 95px;">Tanggal Lahir</th>
                <th class="text-center" style="width: 80px;">Status Akun</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswa as $index => $s)
            @php
                $kelasRel = $s->relationLoaded('kelas') ? $s->getRelation('kelas') : null;
                if ($kelasRel && $kelasRel->isNotEmpty()) {
                    $first = $kelasRel->first();
                    $kelasName = $first->nama_kelas . ($first->tingkat ? ' Kelas ' . $first->tingkat : '');
                } elseif (is_string($s->kelas) && !empty($s->kelas)) {
                    $kelasName = $s->kelas;
                } else {
                    $kelasName = '-';
                }
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $s->nis ?? '-' }}</td>
                <td><strong>{{ $s->name }}</strong></td>
                <td>{{ $kelasName }}</td>
                <td>{{ $s->jurusan?->nama_jurusan ?? '-' }}</td>
                <td class="text-center">{{ $s->tanggal_lahir ? $s->tanggal_lahir->format('d-m-Y') : '-' }}</td>
                <td class="text-center">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 24px; color: #64748b;">Belum ada data siswa yang tercatat.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen resmi di-generate oleh Sistem {{ $siteTitle ?? 'GuruKuu' }} &bull; {{ $schoolName ?? 'SMK Negeri 1 Bangsri' }}
    </div>
</body>
</html>
