<?php

namespace App\Http\Middleware;

use App\Models\CandidateUser;
use App\Models\Exam;
use App\Services\CBT\CandidateAccessSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCandidateExamAccess
{
    public function __construct(
        private readonly CandidateAccessSession $accessSession
    ) {}

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $access = $this->accessSession->current($request);
        $requestedExam = $request->route('exam');

        if (
            ! $access ||
            ! $requestedExam instanceof Exam ||
            ! isset(
                $access['candidate_id'],
                $access['exam_id'],
                $access['program_id']
            )
        ) {
            return $this->deny($request);
        }

        $candidate = CandidateUser::find($access['candidate_id']);

        $exam = Exam::with('program')
            ->find($access['exam_id']);

        if (
            ! $candidate ||
            ! in_array(
                $candidate->status,
                config('cbt.candidate_allowed_statuses', []),
                true
            ) ||
            ! $exam ||
            ! $exam->is_active ||
            ! $exam->program ||
            ! $exam->program->is_active ||
            $exam->program->slug !== 'sak' ||
            (int) $access['program_id'] !== (int) $exam->program_id
        ) {
            $this->accessSession->clear($request);

            return $this->deny($request);
        }

        if ((int) $requestedExam->id !== (int) $exam->id) {
            return $this->deny($request);
        }

        $request->attributes->set('cbt.candidate', $candidate);
        $request->attributes->set('cbt.exam', $exam);

        return $next($request);
    }

    private function deny(Request $request): Response
    {
        return redirect()
            ->route('sak.landing')
            ->with(
                'notice',
                'Masukkan User ID dan token untuk mengakses paket ujian.'
            );
    }
}
