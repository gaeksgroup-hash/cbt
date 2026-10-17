<?php

namespace App\Support;

use App\Models\Attempt;
use App\Models\CandidateUser;
use App\Models\Exam;
use Illuminate\Http\Request;

class CandidateSession
{
    public function candidate(Request $request): CandidateUser
    {
        $candidate = CandidateUser::find($request->session()->get('cbt.candidate_user_id'));

        abort_unless($candidate && ($candidate->status === 'active' ||
            ($candidate->status === 'pre_generated' && config('cbt.allow_pre_generated_candidates'))), 404);

        return $candidate;
    }

    public function exam(Request $request, Exam $exam): CandidateUser
    {
        $verifiedAt = (int) $request->session()->get('cbt.entitlement_verified_at', 0);
        $ttlSeconds = max(5, (int) config('cbt.gdp_entitlements.session_ttl_minutes', 120)) * 60;
        abort_unless(
            $request->session()->get('cbt.exam_id') === $exam->id &&
            $request->session()->get('cbt.entitlement_key') === $exam->program?->entitlement_key &&
            $verifiedAt > 0 && now()->timestamp - $verifiedAt <= $ttlSeconds &&
            $exam->is_active &&
            $exam->program?->slug === 'sak',
            404
        );

        return $this->candidate($request);
    }

    public function attempt(Request $request, Attempt $attempt): CandidateUser
    {
        $candidate = $this->candidate($request);
        abort_unless($attempt->candidate_user_id === $candidate->id, 404);

        return $candidate;
    }
}
