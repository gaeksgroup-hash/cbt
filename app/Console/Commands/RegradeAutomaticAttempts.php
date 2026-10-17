<?php

namespace App\Console\Commands;

use App\Models\Attempt;
use App\Services\CBT\ScoringService;
use Illuminate\Console\Command;

class RegradeAutomaticAttempts extends Command
{
    protected $signature = 'cbt:regrade-automatic';

    protected $description = 'Hitung ulang attempt lama memakai rubrik otomatis terbaru.';

    public function handle(ScoringService $scoring): int
    {
        $count = 0;
        Attempt::query()
            ->whereIn('status', ['submitted', 'timed_out'])
            ->where(function ($query) {
                $query->where('grading_status', '!=', 'final')->orWhereNull('score_final');
            })
            ->orderBy('id')
            ->eachById(function (Attempt $attempt) use ($scoring, &$count): void {
                $scoring->regrade($attempt);
                $count++;
            });

        $this->info("Regraded {$count} attempt(s).");

        return self::SUCCESS;
    }
}
