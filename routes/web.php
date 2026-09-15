<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Admin\JurusanController as AdminJurusanController;
use App\Http\Controllers\Admin\KontakController as AdminKontakController;
use App\Http\Controllers\Admin\KritikSaranController;
use App\Http\Controllers\Admin\LeaderboardController as AdminLeaderboardController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\GuruController as SiswaGuruController;
use App\Http\Controllers\Siswa\LeaderboardController as SiswaLeaderboardController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Siswa\PenilaianController;
use App\Http\Controllers\Siswa\ProfilController as SiswaProfilController;
use Illuminate\Support\Facades\Route;

// ==================== 1. GURU ROUTES (WAJIB DI ATAS /guru/{guru}) ====================
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
    Route::get('/leaderboard', [GuruDashboardController::class, 'leaderboard'])->name('leaderboard');
    Route::post('/penilaian/{penilaian}/reply', [GuruDashboardController::class, 'replyPenilaian'])->name('penilaian.reply');
    Route::get('/pengaturan', [\App\Http\Controllers\Guru\PengaturanController::class, 'index'])->name('pengaturan');
    Route::post('/pengaturan/profil', [\App\Http\Controllers\Guru\PengaturanController::class, 'updateProfil'])->name('pengaturan.profil');
    Route::post('/pengaturan/ganti-password', [\App\Http\Controllers\Guru\PengaturanController::class, 'gantiPassword'])->name('pengaturan.password');
    Route::post('/pengaturan/chat', [\App\Http\Controllers\Guru\PengaturanController::class, 'kirimPesanAdmin'])->name('pengaturan.chat');
    Route::put('/pengaturan/chat/{kontak}', [\App\Http\Controllers\Guru\PengaturanController::class, 'editPesanAdmin'])->name('pengaturan.chat.edit');
    Route::delete('/pengaturan/chat/{kontak}', [\App\Http\Controllers\Guru\PengaturanController::class, 'hapusPesanAdmin'])->name('pengaturan.chat.destroy');
});

// ==================== 2. PUBLIC ROUTES ====================
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/leaderboard', [LandingController::class, 'leaderboard'])->name('landing.leaderboard');
Route::get('/search', [LandingController::class, 'search'])->name('landing.search');

// ⚠️ Route wildcard ini HARUS di bawah route spesifik /guru/dashboard
Route::get('/guru/{guru}', [LandingController::class, 'guruDetail'])->name('landing.guru.detail');

Route::get('/kebijakan-privasi', function () { return view('legal.privacy'); })->name('legal.privacy');
Route::get('/syarat-ketentuan', function () { return view('legal.terms'); })->name('legal.terms');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// KONTAK GUEST
Route::get('/hubungi-admin', [KontakController::class, 'guestPage'])->name('kontak.guest.page');
Route::post('/hubungi-admin', [KontakController::class, 'storeGuest'])->name('kontak.guest.store');
Route::put('/hubungi-admin/{kontak}', [KontakController::class, 'editGuest'])->name('kontak.guest.edit');
Route::delete('/hubungi-admin/{kontak}/message', [KontakController::class, 'destroyGuestMessage'])->name('kontak.guest.destroy-message');

// ==================== 3. AUTHENTICATED ROUTES ====================
Route::middleware('auth')->group(function () {
    Route::get('/ganti-password', [LoginController::class, 'showGantiPassword'])->name('auth.ganti-password');
    Route::post('/ganti-password', [LoginController::class, 'gantiPassword'])->name('auth.ganti-password.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// ==================== 4. ADMIN ROUTES ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [AdminProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [AdminProfilController::class, 'update'])->name('profil.update');
    Route::resource('guru', AdminGuruController::class);
    Route::resource('siswa', AdminSiswaController::class);
    Route::patch('/siswa/{siswa}/toggle', [AdminSiswaController::class, 'toggleStatus'])->name('siswa.toggle');
    Route::get('/jurusan', [AdminJurusanController::class, 'index'])->name('jurusan.index');
    Route::post('/jurusan', [AdminJurusanController::class, 'store'])->name('jurusan.store');
    Route::get('/jurusan/{jurusan}/edit', [AdminJurusanController::class, 'edit'])->name('jurusan.edit');
    Route::put('/jurusan/{jurusan}', [AdminJurusanController::class, 'update'])->name('jurusan.update');
    Route::delete('/jurusan/{jurusan}', [AdminJurusanController::class, 'destroy'])->name('jurusan.destroy');
    Route::resource('periode', PeriodeController::class)->except(['create', 'show', 'edit']);
    Route::patch('periode/{periode}/toggle', [PeriodeController::class, 'toggleStatus'])->name('periode.toggle');
    
    Route::get('/kritik-saran', [KritikSaranController::class, 'index'])->name('kritik-saran.index');
    Route::delete('/kritik-saran/{kritikSaran}', [KritikSaranController::class, 'destroy'])->name('kritik-saran.destroy');
    Route::post('/kritik-saran/{kritikSaran}/warn', [KritikSaranController::class, 'warn'])->name('kritik-saran.warn');
    
    Route::get('/leaderboard', [AdminLeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan/landing', [PengaturanController::class, 'updateLanding'])->name('pengaturan.landing');
    Route::post('/pengaturan/landing/reset', [PengaturanController::class, 'resetLandingHero'])->name('pengaturan.landing.reset');
    Route::post('/pengaturan/reset', [PengaturanController::class, 'reset'])->name('pengaturan.reset');
    Route::post('/pengaturan/ganti-password', [PengaturanController::class, 'gantiPassword'])->name('pengaturan.password');
    Route::post('/pengaturan/periode', [PengaturanController::class, 'simpanPeriode'])->name('pengaturan.periode');
    Route::post('/pengaturan/periode/{periode}/aktifkan', [PengaturanController::class, 'aktifkanPeriode'])->name('pengaturan.periode.aktifkan');
    
    Route::get('/kontak', [AdminKontakController::class, 'index'])->name('kontak.index');
    Route::post('/kontak/{kontak}/reply', [AdminKontakController::class, 'reply'])->name('kontak.reply');
    Route::put('/kontak/{kontak}/reply', [AdminKontakController::class, 'editReply'])->name('kontak.edit-reply');
    Route::delete('/kontak/{kontak}', [AdminKontakController::class, 'destroy'])->name('kontak.destroy');
    Route::delete('/kontak/{kontak}/reply', [AdminKontakController::class, 'destroyReply'])->name('kontak.destroy-reply');

    // EXPORTS
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/leaderboard/excel', [ExportController::class, 'leaderboardExcel'])->name('leaderboard.excel');
        Route::get('/leaderboard/pdf', [ExportController::class, 'leaderboardPdf'])->name('leaderboard.pdf');
        Route::get('/guru/excel', [ExportController::class, 'guruExcel'])->name('guru.excel');
        Route::get('/guru/pdf', [ExportController::class, 'guruPdf'])->name('guru.pdf');
        Route::get('/siswa/excel', [ExportController::class, 'siswaExcel'])->name('siswa.excel');
        Route::get('/siswa/pdf', [ExportController::class, 'siswaPdf'])->name('siswa.pdf');
        Route::get('/all/zip', [ExportController::class, 'allZip'])->name('all.zip');
    });

    Route::prefix('sipintu')->name('sipintu.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SiPintuController::class, 'index'])->name('index');
        Route::get('/guru', [\App\Http\Controllers\Admin\SiPintuController::class, 'teachers'])->name('guru');
        Route::get('/siswa', [\App\Http\Controllers\Admin\SiPintuController::class, 'students'])->name('siswa');
        Route::get('/guru/{nip}/detail', [\App\Http\Controllers\Admin\SiPintuController::class, 'teacherDetail'])->name('guru.detail');
        Route::get('/siswa/{nis}/detail', [\App\Http\Controllers\Admin\SiPintuController::class, 'studentDetail'])->name('siswa.detail');
        Route::post('/guru/import', [\App\Http\Controllers\Admin\SiPintuController::class, 'importTeacher'])->name('guru.import');
        Route::post('/siswa/import', [\App\Http\Controllers\Admin\SiPintuController::class, 'importStudent'])->name('siswa.import');
        Route::post('/guru/sync-all', [\App\Http\Controllers\Admin\SiPintuController::class, 'syncAllTeachers'])->name('guru.sync-all');
        Route::post('/siswa/sync-all', [\App\Http\Controllers\Admin\SiPintuController::class, 'syncAllStudents'])->name('siswa.sync-all');
        Route::post('/sync-all-full', [\App\Http\Controllers\Admin\SiPintuController::class, 'syncAllDirect'])->name('sync-all-full');
        Route::post('/check-connection', [\App\Http\Controllers\Admin\SiPintuController::class, 'checkConnection'])->name('check-connection');
    });
});

// ==================== 5. SISWA ROUTES ====================
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/guru', [SiswaGuruController::class, 'index'])->name('guru.index');
    Route::get('/guru/{guru}', [SiswaGuruController::class, 'show'])->name('guru.show');
    Route::middleware('periode.active')->group(function () {
        Route::get('/penilaian/{guru}/create', [PenilaianController::class, 'create'])->name('penilaian.create');
        Route::post('/penilaian/{guru}', [PenilaianController::class, 'store'])->name('penilaian.store');
    });
    Route::get('/riwayat', [PenilaianController::class, 'riwayat'])->name('riwayat');
    Route::delete('/riwayat/{penilaian}', [PenilaianController::class, 'destroy'])->name('riwayat.destroy');
    Route::get('/leaderboard', [SiswaLeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/pengaturan', [\App\Http\Controllers\Siswa\PengaturanController::class, 'index'])->name('pengaturan');
    Route::post('/pengaturan/ganti-password', [\App\Http\Controllers\Siswa\PengaturanController::class, 'gantiPassword'])->name('pengaturan.password');
    Route::post('/pengaturan/chat', [\App\Http\Controllers\Siswa\PengaturanController::class, 'kirimPesanAdmin'])->name('pengaturan.chat');
    Route::get('/profil', function() { return redirect()->route('siswa.pengaturan'); })->name('profil.index');
    
    Route::get('/kontak', function() { return redirect()->to(route('siswa.pengaturan') . '#tabChat'); })->name('kontak.index');
    Route::post('/kontak', [KontakController::class, 'storeSiswa'])->name('kontak.store');
    Route::put('/kontak/{kontak}', [KontakController::class, 'editSiswa'])->name('kontak.edit');
    Route::delete('/kontak/{kontak}', [KontakController::class, 'siswaDestroy'])->name('kontak.destroy');
    Route::delete('/kontak/{kontak}/message', [KontakController::class, 'destroySiswaMessage'])->name('kontak.destroy-message');
});