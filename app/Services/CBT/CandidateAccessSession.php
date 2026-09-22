<?php

namespace App\Services\CBT;

use App\Models\CandidateUser;
use App\Models\Exam;
use Illuminate\Http\Request;

class CandidateAccessSession
{
    public function grant(
        Request $request,
        CandidateUser $candidate,
        Exam $exam
    ): void {
        $request->session()->regenerate();

        $request->session()->put('cbt.access', [
            'candidate_id' => $candidate->id,
            'exam_id' => $exam->id,
            'program_id' => $exam->program_id,
            'authorized_at' => now()->toIso8601String(),
        ]);
    }

    public function current(Request $request): ?array
    {
        $access = $request->session()->get('cbt.access');

        return is_array($access) ? $access : null;
    }

    public function clear(Request $request): void
    {
        $request->session()->forget('cbt.access');
    }
}
