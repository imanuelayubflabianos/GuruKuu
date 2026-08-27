<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Siswa;
use Illuminate\Support\Facades\Route;

/* ===================== LANDING PAGE ===================== */
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/leaderboard', [LandingController::class, 'leaderboard'])->name('landing.leaderboard');
Route::get('/search', [LandingController::class, 'search'])->name('landing.search');
Route::get('/guru/{guru}', [LandingController::class, 'guruDetail'])->name('landing.guru.detail');

/* ===================== AUTH ===================== */
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/* ===================== HALAMAN LEGAL ===================== */
Route::get('/kebijakan-privasi', function () { return view('legal.privacy'); })->name('legal.privacy');
Route::get('/syarat-ketentuan', function () { return view('legal.terms'); })->name('legal.terms');

/* ===================== KONTAK TAMU ===================== */
Route::get('/hubungi-admin', [KontakController::class, 'guestPage'])->name('kontak.guest.page');
Route::post('/hubungi-admin', [KontakController::class, 'storeGuest'])->name('kontak.guest.store');
Route::put('/hubungi-admin/{kontak}', [KontakController::class, 'editGuest'])->name('kontak.guest.edit');
Route::delete('/hubungi-admin/{kontak}/message', [KontakController::class, 'destroyGuestMessage'])->name('kontak.guest.destroy-message');

/* ===================== ADMIN ROUTES ===================== */
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // ✅ PROFIL ADMIN (BARU)
    Route::get('/profil', [Admin\ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [Admin\ProfilController::class, 'update'])->name('profil.update');
    
    Route::resource('guru', Admin\GuruController::class);
    Route::resource('siswa', Admin\SiswaController::class);
    Route::resource('jurusan', Admin\JurusanController::class)->except(['create', 'show', 'edit']);
    Route::resource('periode', Admin\PeriodeController::class)->except(['create', 'show', 'edit']);
    Route::patch('periode/{periode}/toggle', [Admin\PeriodeController::class, 'toggleStatus'])->name('periode.toggle');
    Route::resource('badge', Admin\BadgeController::class)->except(['create', 'show', 'edit']);
    Route::post('badge/award', [Admin\BadgeController::class, 'award'])->name('badge.award');
    
    // ✅ KRITIK SARAN (dengan hapus feedback & hapus semua)
    Route::get('kritik-saran', [Admin\KritikSaranController::class, 'index'])->name('kritik-saran.index');
    Route::delete('kritik-saran/{kritikSaran}/feedback', [Admin\KritikSaranController::class, 'destroyFeedback'])->name('kritik-saran.destroy-feedback');
    Route::delete('kritik-saran/{kritikSaran}', [Admin\KritikSaranController::class, 'destroy'])->name('kritik-saran.destroy');
    
    Route::get('statistik', [Admin\StatistikController::class, 'index'])->name('statistik.index');
    Route::get('leaderboard', [Admin\LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('export/pdf', [Admin\ExportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('export/excel', [Admin\ExportController::class, 'exportExcel'])->name('export.excel');
    Route::get('pengaturan', [Admin\PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('pengaturan/reset-penilaian', [Admin\PengaturanController::class, 'resetPenilaian'])->name('pengaturan.reset');
    
    // Pesan Masuk
    Route::get('kontak', [KontakController::class, 'index'])->name('kontak.index');
    Route::post('kontak/{kontak}/reply', [KontakController::class, 'reply'])->name('kontak.reply');
    Route::put('kontak/{kontak}/reply', [KontakController::class, 'editReply'])->name('kontak.edit-reply');
    Route::delete('kontak/{kontak}', [KontakController::class, 'destroy'])->name('kontak.destroy');
    Route::delete('kontak/{kontak}/reply', [KontakController::class, 'destroyReply'])->name('kontak.destroy-reply');
});

/* ===================== SISWA ROUTES ===================== */
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [Siswa\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/guru', [Siswa\GuruController::class, 'index'])->name('guru.index');
    Route::get('/guru/{guru}', [Siswa\GuruController::class, 'show'])->name('guru.show');
    
    Route::middleware('periode.active')->group(function () {
        Route::get('/penilaian/{guru}/create', [Siswa\PenilaianController::class, 'create'])->name('penilaian.create');
        Route::post('/penilaian/{guru}', [Siswa\PenilaianController::class, 'store'])->name('penilaian.store');
    });
    
    Route::get('/riwayat', [Siswa\PenilaianController::class, 'riwayat'])->name('riwayat');
    Route::delete('/riwayat/{penilaian}', [Siswa\PenilaianController::class, 'destroy'])->name('riwayat.destroy');
    Route::get('/leaderboard', [Siswa\LeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/profil', [Siswa\ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [Siswa\ProfilController::class, 'update'])->name('profil.update');
    
    Route::get('/kontak', [KontakController::class, 'siswaIndex'])->name('kontak.index');
    Route::delete('/kontak/{kontak}', [KontakController::class, 'siswaDestroy'])->name('kontak.destroy');
    Route::put('/kontak/{kontak}', [KontakController::class, 'editSiswa'])->name('kontak.edit');
    Route::delete('/kontak/{kontak}/message', [KontakController::class, 'destroySiswaMessage'])->name('kontak.destroy-message');
});

Route::post('/siswa/kontak', [KontakController::class, 'storeSiswa'])->middleware(['auth', 'role:siswa'])->name('kontak.siswa');