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
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CandidateController as AdminCandidateController;

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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');

    Route::middleware('cbt.admin')->group(function () {
        Route::get('/', [AdminCandidateController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
        Route::post('/users', [AdminCandidateController::class, 'store'])->name('candidates.store');
        Route::get('/users/{candidate}', [AdminCandidateController::class, 'show'])->name('candidates.show');
        Route::patch('/users/{candidate}/status', [AdminCandidateController::class, 'updateStatus'])->name('candidates.status');
        Route::post('/users/{candidate}/exams/{exam}/reset-attempts', [AdminCandidateController::class, 'resetAttempts'])
            ->name('candidates.attempts.reset');
    });
});
