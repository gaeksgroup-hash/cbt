<?php

namespace App\Services\CBT;

use App\Models\Attempt;
use Illuminate\Support\Facades\DB;

class ScoringService
{
    public function __construct(protected EssayRubricScorer $essayScorer)
    {
    }

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
                    $autoPossible += $maxPoints;
                    $value = $answer?->answer_json['value'] ?? null;
                    $awarded = $this->essayScorer->score(
                        is_string($value) ? $value : null,
                        is_array($snapshot['scoring_rules'] ?? null) ? $snapshot['scoring_rules'] : null,
                        $maxPoints
                    );
                    $correct = $awarded >= $maxPoints;
                    $autoEarned += $awarded;
                }

                if ($answer) {
                    $answer->update(['is_correct' => $correct, 'awarded_points' => $awarded]);
                }
            }

            $autoScore = $autoPossible > 0 ? round($autoEarned / $autoPossible * 100, 2) : null;
            $finalScore = $totalPossible <= 0 ? null : round($autoEarned / $totalPossible * 100, 2);

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
                'grading_status' => 'final',
            ]);

            return $attempt->fresh();
        });
    }

    public function regrade(Attempt $attempt): Attempt
    {
        return DB::transaction(function () use ($attempt) {
            $attempt = Attempt::query()->lockForUpdate()->findOrFail($attempt->id);
            if (! in_array($attempt->status, ['submitted', 'timed_out'], true)) {
                return $attempt;
            }

            $timedOut = $attempt->status === 'timed_out';
            $submittedAt = $attempt->submitted_at;
            $durationUsed = $attempt->duration_used_seconds;
            $attempt->forceFill(['status' => 'in_progress'])->save();
            $rescored = $this->finalize($attempt, $timedOut);

            $rescored->forceFill([
                'submitted_at' => $submittedAt,
                'duration_used_seconds' => $durationUsed,
            ])->save();

            return $rescored->fresh();
        });
    }
}
