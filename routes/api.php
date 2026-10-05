<?php

use App\Http\Controllers\Auth\OAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Webhook & API endpoints for SiPintu Identity Gateway and other downstream services.
|
*/

Route::post('/sipintu/sync-user', [OAuthController::class, 'syncUser'])->name('api.sipintu.sync-user');
