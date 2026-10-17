<?php

namespace Tests\Unit;

use App\Services\CBT\EssayRubricScorer;
use App\Support\SakEssayRubrics;
use PHPUnit\Framework\TestCase;

class EssayRubricScorerTest extends TestCase
{
    public function test_it_scores_a_complete_answer_immediately(): void
    {
        $answer = 'Smartwatch adalah perangkat multifungsi dengan komunikasi nirkabel. '
            .'Catatan 1 n Bab 91 mengecualikan perangkat ini. Berdasarkan KUMHS 1 dan KUMHS 3 b, '
            .'karakter hakikinya berada pada pos 85.17, yaitu 8517 62 59, bukan pos 91.02.';

        $score = (new EssayRubricScorer())->score(
            $answer,
            SakEssayRubrics::for('SAK-B01-Q010'),
            10
        );

        $this->assertSame(10.0, $score);
    }

    public function test_blank_or_unmatched_answers_receive_zero(): void
    {
        $scorer = new EssayRubricScorer();
        $rules = SakEssayRubrics::for('SAK-B01-Q010');

        $this->assertSame(0.0, $scorer->score('', $rules, 10));
        $this->assertSame(0.0, $scorer->score('Saya tidak tahu jawabannya.', $rules, 10));
    }
}
