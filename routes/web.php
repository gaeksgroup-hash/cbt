<?php

use App\Http\Controllers\CandidateAccessController;
use App\Http\Controllers\CandidateExamController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SakLandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)
    ->name('landing');

Route::get('/health', HealthController::class)
    ->name('health');

Route::get('/sak', SakLandingController::class)
    ->name('sak.landing');

Route::post(
    '/sak/access',
    [CandidateAccessController::class, 'store']
)
    ->middleware('throttle:sak-access')
    ->name('sak.access');

Route::post(
    '/sak/logout',
    [CandidateAccessController::class, 'logout']
)
    ->name('sak.logout');

Route::middleware('cbt.exam')->group(function () {
    Route::get(
        '/sak/exam/{exam:slug}/guide',
        [CandidateExamController::class, 'guide']
    )->name('sak.exam.guide');

    Route::get(
        '/sak/exam/{exam:slug}/demo',
        [CandidateExamController::class, 'demo']
    )->name('sak.exam.demo');
});
