<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\User;
use App\Services\SiPintuService;
use Illuminate\Http\Request;

class SiPintuController extends Controller
{
    protected SiPintuService $siPintu;

    public function __construct(SiPintuService $siPintu)
    {
        $this->siPintu = $siPintu;
    }

    /**
     * Dashboard & Status Gateway SiPintu
     */
    public function index()
    {
        $pingResult = $this->siPintu->ping();
        $validationResult = $pingResult['success'] ? $this->siPintu->validateClient() : null;

        $stats = [
            'local_guru_count'  => Guru::count(),
            'local_siswa_count' => User::where('role', 'siswa')->count(),
            'gateway_url'       => $this->siPintu->getBaseUrl(),
            'client_id'         => $this->siPintu->getClientId(),
        ];

        return view('admin.sipintu.index', compact('pingResult', 'validationResult', 'stats'));
    }

    /**
     * Data Guru dari SiPintu Gateway
     */
    public function teachers(Request $request)
    {
        $params = [];
        if ($request->filled('nip')) {
            $params['nip'] = trim($request->nip);
        }
        if ($request->filled('search')) {
            $params['search'] = trim($request->search);
        }
        if ($request->has('refresh')) {
            $params['refresh'] = true;
        }

        $result = $this->siPintu->getTeachers($params);
        $teachers = $result['data'] ?? [];

        // Fetch all local NIPs for quick lookup
        $localNips = Guru::pluck('nip')->filter()->toArray();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();

        return view('admin.sipintu.teachers', compact('result', 'teachers', 'localNips', 'jurusans'));
    }

    /**
     * Data Siswa dari SiPintu Gateway
     */
    public function students(Request $request)
    {
        $params = [];
        if ($request->filled('nis')) {
            $params['nis'] = trim($request->nis);
        }
        if ($request->filled('search')) {
            $params['search'] = trim($request->search);
        }
        if ($request->has('refresh')) {
            $params['refresh'] = true;
        }
        
        // Status filter: default only active students
        $onlyActive = true;
        if ($request->has('only_active')) {
            $onlyActive = filter_var($request->only_active, FILTER_VALIDATE_BOOLEAN);
        }
        $params['only_active'] = $onlyActive;

        $result = $this->siPintu->getStudents($params);
        $students = $result['data'] ?? [];

        // Fetch all local NISs for quick lookup
        $localNisList = User::where('role', 'siswa')->pluck('nis')->filter()->toArray();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('admin.sipintu.students', compact('result', 'students', 'localNisList', 'kelasList', 'onlyActive'));
    }

    /**
     * AJAX/JSON Teacher Detail from SiPintu
     */
    public function teacherDetail(Request $request, $nip)
    {
        $teacher = $this->siPintu->getTeacherByNip($nip);

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru tidak ditemukan di SiPintu.',
            ], 404);
        }

        $local = Guru::where('nip', $nip)->first();

        return response()->json([
            'success'  => true,
            'data'     => $teacher,
            'is_local' => (bool) $local,
            'local_id' => $local?->id,
        ]);
    }

    /**
     * AJAX/JSON Student Detail from SiPintu
     */
    public function studentDetail(Request $request, $nis)
    {
        $student = $this->siPintu->getStudentByNis($nis);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan di SiPintu.',
            ], 404);
        }

        $local = User::where('role', 'siswa')->where('nis', $nis)->first();

        return response()->json([
            'success'  => true,
            'data'     => $student,
            'is_local' => (bool) $local,
            'local_id' => $local?->id,
        ]);
    }

    /**
     * Import single teacher into local Guru table
     */
    public function importTeacher(Request $request)
    {
        $request->validate([
            'nip'        => 'required|string',
            'nama'       => 'nullable|string',
            'email'      => 'nullable|string',
            'phone'      => 'nullable|string',
            'jurusan_id' => 'nullable|exists:jurusan,id',
        ]);

        $nip = $request->nip;
        $teacherData = [];

        if ($request->filled('nama')) {
            $teacherData = $request->all();
        } else {
            $fetched = $this->siPintu->getTeacherByNip($nip);
            if (!$fetched) {
                return back()->with('error', "Gagal menemukan guru dengan NIP {$nip} di SiPintu.");
            }
            $teacherData = $fetched;
        }

        $jurusanId = $request->jurusan_id ?: null;
        $res = $this->siPintu->syncTeacherToLocal($teacherData, $jurusanId);

        if ($res['success']) {
            if ($request->wantsJson()) {
                return response()->json($res);
            }
            return back()->with('success', $res['message']);
        }

        if ($request->wantsJson()) {
            return response()->json($res, 400);
        }
        return back()->with('error', $res['message']);
    }

    /**
     * Import single student into local User table
     */
    public function importStudent(Request $request)
    {
        $request->validate([
            'nis'      => 'required|string',
            'name'     => 'nullable|string',
            'email'    => 'nullable|string',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $nis = $request->nis;
        $studentData = [];

        if ($request->filled('name') || $request->filled('nama')) {
            $studentData = $request->all();
        } else {
            $fetched = $this->siPintu->getStudentByNis($nis);
            if (!$fetched) {
                return back()->with('error', "Gagal menemukan siswa dengan NIS {$nis} di SiPintu.");
            }
            $studentData = $fetched;
        }

        $kelasId = $request->kelas_id ?: null;
        $res = $this->siPintu->syncStudentToLocal($studentData, $kelasId);

        if ($res['success']) {
            if ($request->wantsJson()) {
                return response()->json($res);
            }
            return back()->with('success', $res['message']);
        }

        if ($request->wantsJson()) {
            return response()->json($res, 400);
        }
        return back()->with('error', $res['message']);
    }

    /**
     * Batch sync all teachers from SiPintu
     */
    public function syncAllTeachers(Request $request)
    {
        $result = $this->siPintu->getTeachers();
        if (!$result['success'] || empty($result['data'])) {
            return back()->with('error', 'Tidak ada data guru yang dapat ditarik dari SiPintu.');
        }

        $count = 0;
        foreach ($result['data'] as $t) {
            $res = $this->siPintu->syncTeacherToLocal($t);
            if ($res['success']) {
                $count++;
            }
        }

        return back()->with('success', "Berhasil menyinkronkan {$count} data guru dari SiPintu ke database GuruKuu.");
    }

    /**
     * Batch sync all active students from SiPintu
     */
    public function syncAllStudents(Request $request)
    {
        $result = $this->siPintu->getStudents(['only_active' => true]);
        if (!$result['success'] || empty($result['data'])) {
            return back()->with('error', 'Tidak ada data siswa aktif yang dapat ditarik dari SiPintu.');
        }

        $count = 0;
        foreach ($result['data'] as $s) {
            $res = $this->siPintu->syncStudentToLocal($s);
            if ($res['success']) {
                $count++;
            }
        }

        return back()->with('success', "Berhasil menyinkronkan {$count} data siswa aktif dari SiPintu ke database GuruKuu.");
    }

    /**
     * Live AJAX Connection Check
     */
    public function checkConnection()
    {
        $ping = $this->siPintu->ping();
        $validation = $ping['success'] ? $this->siPintu->validateClient() : null;

        return response()->json([
            'ping'       => $ping,
            'validation' => $validation,
        ]);
    }
}
