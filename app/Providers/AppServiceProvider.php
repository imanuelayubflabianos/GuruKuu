<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Rate Limiters untuk Perlindungan Server & Anti-Abuse
        RateLimiter::for('web-global', function (Request $request) {
            return Limit::perMinute(200)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->view('errors.429', [], 429);
                });
        });

        RateLimiter::for('submissions', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak permintaan dalam waktu singkat. Mohon tunggu sebentar.'
                    ], 429);
                });
        });

        RateLimiter::for('auth-action', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // 2. View Composer Optimal: Menggunakan in-memory cache Setting
        View::composer(['layouts.*', 'landing.*', 'admin.*', 'guru.*', 'siswa.*', 'auth.*', 'errors.*'], function ($view) {
            try {
                $siteLogo = \App\Models\Setting::get('site_logo', '');
                $siteTitle = \App\Models\Setting::get('site_title', 'GuruKuu');
                $siteTitlePart1 = \App\Models\Setting::get('site_title_part1', 'Guru');
                $siteTitlePart2 = \App\Models\Setting::get('site_title_part2', 'Kuu');
                $siteTitleColor1 = \App\Models\Setting::get('site_title_color1', '#003366');
                $siteTitleColor2 = \App\Models\Setting::get('site_title_color2', '#FFC107');
                $view->with(compact('siteLogo', 'siteTitle', 'siteTitlePart1', 'siteTitlePart2', 'siteTitleColor1', 'siteTitleColor2'));
            } catch (\Throwable $e) {
                $view->with([
                    'siteLogo' => '',
                    'siteTitle' => 'GuruKuu',
                    'siteTitlePart1' => 'Guru',
                    'siteTitlePart2' => 'Kuu',
                    'siteTitleColor1' => '#003366',
                    'siteTitleColor2' => '#FFC107',
                ]);
            }
        });

        View::composer('layouts.admin', function ($view) {
            try {
                $unreadPelanggaranCount = \App\Models\Pelanggaran::where('is_read', false)->count();
                $recentPelanggarans = \App\Models\Pelanggaran::with(['user.jurusan', 'user.kelas', 'guru'])
                    ->where('is_read', false)
                    ->latest()
                    ->take(5)
                    ->get();
                $view->with(compact('unreadPelanggaranCount', 'recentPelanggarans'));
            } catch (\Throwable $e) {
                $view->with([
                    'unreadPelanggaranCount' => 0,
                    'recentPelanggarans' => collect(),
                ]);
            }
        });
    }
}
