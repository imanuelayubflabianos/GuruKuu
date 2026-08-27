<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new User([
            'nis' => (string)$row['nis'],
            'name' => $row['nama'],
            'kelas' => $row['kelas'],
            'jurusan_id' => \App\Models\Jurusan::where('nama_jurusan', $row['jurusan'])->first()?->id,
            'tanggal_lahir' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
    }
}