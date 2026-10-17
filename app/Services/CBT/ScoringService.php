<?php

namespace App\Services\CBT;

use App\Models\Attempt;
use Illuminate\Support\Facades\DB;

class ScoringService
{
    public function finalize(Attempt $attempt, bool $timedOut = false): Attempt
    {
        return DB::transaction(function () use ($attempt, $timedOut) {
            $attempt = Attempt::query()->lockForUpdate()->findOrFail($attempt->id);
            if ($attempt->status !== 'in_progress') {
                return $attempt;
            }

            $attempt->load('attemptQuestions.answer');
            $autoEarned = 0.0;
            $autoPossible = 0.0;
            $totalPossible = 0.0;
            $manualPending = false;

            foreach ($attempt->attemptQuestions as $attemptQuestion) {
                $snapshot = $attemptQuestion->question_snapshot_json;
                $answer = $attemptQuestion->answer;
                $maxPoints = (float) $attemptQuestion->max_points;
                $totalPossible += $maxPoints;
                $awarded = null;
                $correct = null;

                if ($snapshot['type'] === 'single_choice') {
                    $autoPossible += $maxPoints;
                    $selected = $answer?->answer_json['selected'] ?? null;
                    $correctKey = $snapshot['answer_key']['correct_options'][0] ?? null;
                    $correct = is_string($selected) && hash_equals((string) $correctKey, $selected);
                    $awarded = $correct ? $maxPoints : 0.0;
                    $autoEarned += $awarded;
                } elseif ($snapshot['type'] === 'numeric') {
                    $autoPossible += $maxPoints;
                    $value = $answer?->answer_json['value'] ?? null;
                    $expected = $snapshot['answer_key']['expected'] ?? null;
                    $tolerance = (float) ($snapshot['answer_key']['tolerance_absolute'] ?? 0);
                    $correct = is_numeric($value) && is_numeric($expected) && abs((float) $value - (float) $expected) <= $tolerance;
                    $awarded = $correct ? $maxPoints : 0.0;
                    $autoEarned += $awarded;
                } else {
                    $manualPending = true;
                }

                if ($answer) {
                    $answer->update(['is_correct' => $correct, 'awarded_points' => $awarded]);
                }
            }

            $autoScore = $autoPossible > 0 ? round($autoEarned / $autoPossible * 100, 2) : null;
            $finalScore = $manualPending || $totalPossible <= 0 ? null : round($autoEarned / $totalPossible * 100, 2);

            $attempt->update([
                'status' => $timedOut ? 'timed_out' : 'submitted',
                'submitted_at' => now(),
                'score_auto' => $autoScore,
                'score_final' => $finalScore,
                'passed' => $finalScore === null ? null : $finalScore >= (float) $attempt->exam->pass_score,
                'duration_used_seconds' => min(
                    $attempt->started_at?->diffInSeconds(now()) ?? 0,
                    $attempt->exam->exam_type === 'tryout' ? 4500 : $attempt->exam->duration_seconds
                ),
                'grading_status' => $manualPending ? 'awaiting_review' : 'final',
            ]);

            return $attempt->fresh();
        });
    }
}
