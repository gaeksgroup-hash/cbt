<?php

use App\Http\Controllers\HealthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SakLandingController;
use App\Http\Controllers\Candidate\AccessController;
use App\Http\Controllers\Candidate\GuideController;
use App\Http\Controllers\Candidate\AttemptController;
use App\Http\Controllers\Candidate\HistoryController;
use App\Http\Controllers\Candidate\ResultController;
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
Route::post('/sak/exam/{exam:slug}/start/{sessionCode?}', [AttemptController::class, 'start'])
    ->whereIn('sessionCode', ['S1', 'S2'])->name('sak.exam.start');
Route::get('/sak/attempts/{attempt}', [AttemptController::class, 'show'])->name('sak.attempts.show');
Route::put('/sak/attempts/{attempt}/questions/{attemptQuestion}', [AttemptController::class, 'save'])
    ->name('sak.attempts.save');
Route::post('/sak/attempts/{attempt}/submit', [AttemptController::class, 'submit'])
    ->name('sak.attempts.submit');
Route::get('/sak/attempts/{attempt}/result', [ResultController::class, 'show'])
    ->name('sak.attempts.result');
Route::get('/sak/results', HistoryController::class)->name('sak.results');
Route::get('/health', HealthController::class)->name('health');
