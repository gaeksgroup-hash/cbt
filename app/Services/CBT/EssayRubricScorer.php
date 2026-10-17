<?php

namespace App\Services\CBT;

use Illuminate\Support\Str;

class EssayRubricScorer
{
    /** @param array<string, mixed>|null $rules */
    public function score(?string $answer, ?array $rules, float $maxPoints): float
    {
        if (($rules['mode'] ?? null) !== 'keyword_rubric' || trim((string) $answer) === '') {
            return 0.0;
        }

        $normalized = $this->normalize((string) $answer);
        $earned = 0.0;

        foreach ((array) ($rules['criteria'] ?? []) as $criterion) {
            if (! is_array($criterion)) {
                continue;
            }

            $groups = (array) ($criterion['required_groups'] ?? []);
            $criterionMatches = $groups !== [];

            foreach ($groups as $terms) {
                $groupMatches = false;
                foreach ((array) $terms as $term) {
                    $needle = $this->normalize((string) $term);
                    if ($needle !== '' && str_contains($normalized, $needle)) {
                        $groupMatches = true;
                        break;
                    }
                }
                if (! $groupMatches) {
                    $criterionMatches = false;
                    break;
                }
            }

            if ($criterionMatches) {
                $earned += max(0.0, (float) ($criterion['points'] ?? 0));
            }
        }

        return round(min($maxPoints, $earned), 2);
    }

    private function normalize(string $value): string
    {
        $value = mb_strtolower(Str::ascii($value), 'UTF-8');
        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value) ?? '';

        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }
}
