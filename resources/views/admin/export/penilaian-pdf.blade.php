{{-- resources/views/admin/export/penilaian-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penilaian - GuruKuu</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #4f46e5; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #4f46e5; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .header-info { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>LAPORAN PENILAIAN GURU</h1>
    <div class="header-info">
        <p><strong>GuruKuu</strong> - Sistem Informasi Penilaian Guru</p>
        @if($periodeAktif)
            <p>Periode: {{ $periodeAktif->nama_periode }}</p>
        @endif
        <p>Tanggal Cetak: {{ date('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Siswa</th>
                <th>Guru</th>
                <th>Kategori</th>
                <th>KDS</th>
                <th>MGR</th>
                <th>KOM</th>
                <th>TJW</th>
                <th>KRT</th>
                <th>KRM</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penilaian as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->siswa->name }}</td>
                <td>{{ $p->guru->nama }}</td>
                <td>{{ $p->guru->kategori_label }}</td>
                <td>{{ $p->kedisiplinan }}</td>
                <td>{{ $p->cara_mengajar }}</td>
                <td>{{ $p->komunikasi }}</td>
                <td>{{ $p->tanggung_jawab }}</td>
                <td>{{ $p->kreativitas }}</td>
                <td>{{ $p->keramahan }}</td>
                <td><strong>{{ number_format($p->total_nilai, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>