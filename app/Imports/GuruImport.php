<?php

namespace App\Imports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Guru([
            'nip' => $row['nip'],
            'nama' => $row['nama'],
            'kategori' => strtolower($row['kategori']) === 'produktif' ? 'produktif' : 'normada',
            'jurusan_id' => \App\Models\Jurusan::where('nama_jurusan', $row['jurusan'])->first()?->id,
            'bio' => $row['bio'] ?? 'Guru profesional',
        ]);
    }
}