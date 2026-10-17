<?php

namespace App\Services\CBT;

use App\Models\Attempt;
use App\Models\AttemptReset;
use App\Models\CandidateUser;
use App\Models\Exam;

class AttemptQuota
{
    /** @return array{limit:int,used:int,remaining:int,reset_after_attempt_no:int} */
    public function usage(CandidateUser $candidate, Exam $exam): array
    {
        $limit = max(1, (int) ($exam->attempt_limit ?? 3));
        $cutoff = (int) (AttemptReset::query()
            ->where('candidate_user_id', $candidate->id)
            ->where('exam_id', $exam->id)
            ->latest('id')
            ->value('reset_after_attempt_no') ?? 0);

        $used = (int) Attempt::query()
            ->where('candidate_user_id', $candidate->id)
            ->where('exam_id', $exam->id)
            ->where('attempt_no', '>', $cutoff)
            ->distinct()
            ->count('attempt_no');

        return [
            'limit' => $limit,
            'used' => $used,
            'remaining' => max(0, $limit - $used),
            'reset_after_attempt_no' => $cutoff,
        ];
    }
}
