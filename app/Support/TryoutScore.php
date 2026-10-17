<?php

namespace App\Support;

use Illuminate\Support\Collection;

class TryoutScore
{
    public static function pairs(Collection $attempts): Collection
    {
        return $attempts
            ->filter(fn ($attempt) => $attempt->exam?->exam_type === 'tryout' && in_array($attempt->session_code, ['S1', 'S2'], true))
            ->groupBy('attempt_no')
            ->map(function (Collection $group, int|string $attemptNo): array {
                $s1 = $group->firstWhere('session_code', 'S1');
                $s2 = $group->firstWhere('session_code', 'S2');
                $isFinal = $s1?->grading_status === 'final' && $s2?->grading_status === 'final';
                $score = $isFinal ? round(((float) $s1->score_final + (float) $s2->score_final) / 2, 2) : null;

                return [
                    'attempt_no' => (int) $attemptNo,
                    's1' => $s1,
                    's2' => $s2,
                    'score' => $score,
                    'is_final' => $isFinal,
                    'passed' => $isFinal
                        ? (float) $s1->score_final >= 40 && (float) $s2->score_final >= 40 && $score >= 60
                        : null,
                ];
            })
            ->sortByDesc('attempt_no')
            ->values();
    }
}
