<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeId = $periodeAktif?->id;

        $filterKategori = $request->get('kategori', 'semua');
        $filterJurusanId = $request->get('jurusan_id', null);
        $filterKelasId = $request->get('kelas_id', null);

        $query = Penilaian::with(['guru.jurusan', 'siswa'])
            ->where(function($q) {
                $q->where(function($q2) {
                    $q2->whereNotNull('kritik')->where('kritik', '!=', '');
                })->orWhere(function($q2) {
                    $q2->whereNotNull('saran')->where('saran', '!=', '');
                });
            });

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        if ($filterKategori === 'normada' || $filterKategori === 'produktif') {
            $query->whereHas('guru', function($q) use ($filterKategori) {
                $q->where('kategori', $filterKategori);
            });
        }

        if ($filterJurusanId) {
            $query->whereHas('guru', function($q) use ($filterJurusanId) {
                $q->where('jurusan_id', $filterJurusanId);
            });
        }

        if ($filterKelasId) {
            $query->where('class_id', $filterKelasId);
        }

        $feedbacks = $query->latest()->paginate(15);

        $semuaJurusan = Jurusan::orderBy('nama_jurusan')->get();
        $semuaKelas = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('admin.feedback.index', compact(
            'feedbacks', 'semuaJurusan', 'semuaKelas',
            'filterKategori', 'filterJurusanId', 'filterKelasId'
        ));
    }

    public function destroy(Penilaian $feedback)
    {
        $feedback->update(['kritik' => null, 'saran' => null]);
        return back()->with('success', 'Feedback berhasil dihapus.');
    }

    public function warn(Penilaian $feedback)
    {
        if ($feedback->siswa_id) {
            $siswa = User::find($feedback->siswa_id);
            if ($siswa) {
                $siswa->increment('warning_count');
                return back()->with('success', 'Peringatan berhasil diberikan kepada ' . $siswa->name . '. Total peringatan: ' . $siswa->warning_count);
            }
        }
        return back()->with('error', 'Data siswa tidak ditemukan.');
    }
}