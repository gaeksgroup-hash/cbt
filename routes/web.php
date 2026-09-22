<?php

use App\Http\Controllers\HealthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SakLandingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - GAEKS CBT Platform Foundation
|--------------------------------------------------------------------------
*/

Route::get('/', LandingController::class)->name('landing');
Route::get('/sak', SakLandingController::class)->name('sak.landing');
Route::get('/health', HealthController::class)->name('health');
