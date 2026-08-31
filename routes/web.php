<?php

use App\Http\Controllers\Admin\BadgeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Admin\JurusanController as AdminJurusanController;
use App\Http\Controllers\Admin\KontakController as AdminKontakController;
use App\Http\Controllers\Admin\KritikSaranController;
use App\Http\Controllers\Admin\LeaderboardController as AdminLeaderboardController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\Admin\StatistikController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\GuruController as SiswaGuruController;
use App\Http\Controllers\Siswa\LeaderboardController as SiswaLeaderboardController;
use App\Http\Controllers\Siswa\PenilaianController;
use App\Http\Controllers\Siswa\ProfilController as SiswaProfilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== LANDING PAGE ====================
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/leaderboard', [LandingController::class, 'leaderboard'])->name('landing.leaderboard');
Route::get('/search', [LandingController::class, 'search'])->name('landing.search');
Route::get('/guru/{guru}', [LandingController::class, 'guruDetail'])->name('landing.guru.detail');

// ==================== AUTH ====================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ==================== HALAMAN LEGAL ====================
Route::get('/kebijakan-privasi', function () { return view('legal.privacy'); })->name('legal.privacy');
Route::get('/syarat-ketentuan', function () { return view('legal.terms'); })->name('legal.terms');

// ==================== KONTAK TAMU ====================
Route::get('/hubungi-admin', [KontakController::class, 'guestPage'])->name('kontak.guest.page');
Route::post('/hubungi-admin', [KontakController::class, 'storeGuest'])->name('kontak.guest.store');
Route::put('/hubungi-admin/{kontak}', [KontakController::class, 'editGuest'])->name('kontak.guest.edit');
Route::delete('/hubungi-admin/{kontak}/message', [KontakController::class, 'destroyGuestMessage'])->name('kontak.guest.destroy-message');

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Profil Admin
    Route::get('/profil', [AdminProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [AdminProfilController::class, 'update'])->name('profil.update');
    
    // Master Data
    Route::resource('guru', AdminGuruController::class);
    Route::resource('siswa', \App\Http\Controllers\Admin\SiswaController::class);
    
    // Jurusan (Eksplisit untuk menghindari error parameter)
    Route::get('/jurusan', [AdminJurusanController::class, 'index'])->name('jurusan.index');
    Route::post('/jurusan', [AdminJurusanController::class, 'store'])->name('jurusan.store');
    Route::put('/jurusan/{jurusan}', [AdminJurusanController::class, 'update'])->name('jurusan.update');
    Route::delete('/jurusan/{jurusan}', [AdminJurusanController::class, 'destroy'])->name('jurusan.destroy');
    
    Route::resource('periode', PeriodeController::class)->except(['create', 'show', 'edit']);
    Route::patch('periode/{periode}/toggle', [PeriodeController::class, 'toggleStatus'])->name('periode.toggle');
    
    Route::resource('badge', BadgeController::class)->except(['create', 'show', 'edit']);
    Route::post('badge/award', [BadgeController::class, 'award'])->name('badge.award');
    
    // Penilaian & Laporan
    Route::get('kritik-saran', [KritikSaranController::class, 'index'])->name('kritik-saran.index');
    Route::delete('kritik-saran/{kritikSaran}/feedback', [KritikSaranController::class, 'destroyFeedback'])->name('kritik-saran.destroy-feedback');
    Route::delete('kritik-saran/{kritikSaran}', [KritikSaranController::class, 'destroy'])->name('kritik-saran.destroy');
    
    Route::get('statistik', [StatistikController::class, 'index'])->name('statistik.index');
    Route::get('leaderboard', [AdminLeaderboardController::class, 'index'])->name('leaderboard.index');
    
    Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('pengaturan/reset-penilaian', [PengaturanController::class, 'resetPenilaian'])->name('pengaturan.reset');
    
    // Kontak Admin
    Route::get('kontak', [AdminKontakController::class, 'index'])->name('kontak.index');
    Route::post('kontak/{kontak}/reply', [AdminKontakController::class, 'reply'])->name('kontak.reply');
    Route::put('kontak/{kontak}/reply', [AdminKontakController::class, 'editReply'])->name('kontak.edit-reply');
    Route::delete('kontak/{kontak}', [AdminKontakController::class, 'destroy'])->name('kontak.destroy');
    Route::delete('kontak/{kontak}/reply', [AdminKontakController::class, 'destroyReply'])->name('kontak.destroy-reply');
});

// ==================== SISWA ROUTES ====================
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
    Route::get('/profil', [SiswaProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [SiswaProfilController::class, 'update'])->name('profil.update');
    
    // Kontak Siswa
    Route::get('/kontak', [KontakController::class, 'siswaIndex'])->name('kontak.index');
    Route::post('/kontak', [KontakController::class, 'storeSiswa'])->name('kontak.store');
    Route::delete('/kontak/{kontak}', [KontakController::class, 'siswaDestroy'])->name('kontak.destroy');
    Route::put('/kontak/{kontak}', [KontakController::class, 'editSiswa'])->name('kontak.edit');
    Route::delete('/kontak/{kontak}/message', [KontakController::class, 'destroySiswaMessage'])->name('kontak.destroy-message');
});