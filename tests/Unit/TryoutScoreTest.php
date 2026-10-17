<?php

namespace Tests\Unit;

use App\Support\TryoutScore;
use PHPUnit\Framework\TestCase;

class TryoutScoreTest extends TestCase
{
    public function test_combines_sessions_and_applies_both_pass_thresholds(): void
    {
        $exam = (object) ['exam_type' => 'tryout'];
        $attempts = collect([
            (object) ['attempt_no' => 1, 'session_code' => 'S1', 'grading_status' => 'final', 'score_final' => 40, 'exam' => $exam],
            (object) ['attempt_no' => 1, 'session_code' => 'S2', 'grading_status' => 'final', 'score_final' => 80, 'exam' => $exam],
            (object) ['attempt_no' => 2, 'session_code' => 'S1', 'grading_status' => 'final', 'score_final' => 39, 'exam' => $exam],
            (object) ['attempt_no' => 2, 'session_code' => 'S2', 'grading_status' => 'final', 'score_final' => 81, 'exam' => $exam],
        ]);

        $pairs = TryoutScore::pairs($attempts)->keyBy('attempt_no');

        $this->assertSame(60.0, $pairs[1]['score']);
        $this->assertTrue($pairs[1]['passed']);
        $this->assertSame(60.0, $pairs[2]['score']);
        $this->assertFalse($pairs[2]['passed']);
    }

    public function test_keeps_combined_score_pending_until_both_sessions_are_final(): void
    {
        $exam = (object) ['exam_type' => 'tryout'];
        $pairs = TryoutScore::pairs(collect([
            (object) ['attempt_no' => 1, 'session_code' => 'S1', 'grading_status' => 'awaiting_review', 'score_final' => null, 'exam' => $exam],
            (object) ['attempt_no' => 1, 'session_code' => 'S2', 'grading_status' => 'final', 'score_final' => 90, 'exam' => $exam],
        ]));

        $this->assertFalse($pairs->first()['is_final']);
        $this->assertNull($pairs->first()['score']);
        $this->assertNull($pairs->first()['passed']);
    }
}
