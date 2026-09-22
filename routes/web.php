<?php

use App\Http\Controllers\HealthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SakLandingController;
use App\Http\Controllers\Candidate\AccessController;
use App\Http\Controllers\Candidate\GuideController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - GAEKS CBT Platform Foundation
|--------------------------------------------------------------------------
*/

Route::get('/', LandingController::class)->name('landing');
Route::get('/sak', SakLandingController::class)->name('sak.landing');
Route::post('/sak/access', [AccessController::class, 'store'])->middleware('throttle:5,1')->name('sak.access');
Route::post('/sak/logout', [AccessController::class, 'destroy'])->name('sak.logout');
Route::get('/sak/exam/{exam:slug}/guide', [GuideController::class, 'show'])->name('sak.exam.guide');
Route::get('/sak/exam/{exam:slug}/demo', [GuideController::class, 'demo'])->name('sak.exam.demo');
Route::get('/health', HealthController::class)->name('health');
