<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                $siteLogo = \App\Models\Setting::get('site_logo', '');
                $siteTitle = \App\Models\Setting::get('site_title', 'GuruKuu');
                $view->with(compact('siteLogo', 'siteTitle'));
            } catch (\Throwable $e) {
                $view->with([
                    'siteLogo' => '',
                    'siteTitle' => 'GuruKuu',
                ]);
            }
        });
    }
}
