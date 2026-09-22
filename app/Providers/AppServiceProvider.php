<?php

namespace App\Providers;

use App\Models\Exam;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for(
            'sak-access',
            fn (Request $request) => Limit::perMinute(10)
                ->by($request->ip() ?: 'unknown')
        );

        Route::bind('exam', function (string $slug): Exam {
            return Exam::query()
                ->where('slug', $slug)
                ->whereHas(
                    'program',
                    fn ($query) => $query->where('slug', 'sak')
                )
                ->firstOrFail();
        });
    }
}
