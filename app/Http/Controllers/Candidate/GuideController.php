<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\CandidateUser;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuideController extends Controller
{
    public function show(Request $request, Exam $exam): View
    {
        $candidate = $this->authorizeAccess($request, $exam);
        $previous = $candidate->attempts()->where('exam_id', $exam->id)
            ->whereIn('status', ['submitted', 'timed_out'])->get();

        return view('sak.guide', [
            'exam' => $exam,
            'attemptCount' => $previous->count(),
            'bestScore' => $previous->where('grading_status', 'final')->max('score_final'),
            'questionCount' => $exam->examQuestions()->count(),
        ]);
    }

    public function demo(Request $request, Exam $exam): View
    {
        $this->authorizeAccess($request, $exam);

        return view('sak.demo', ['exam' => $exam]);
    }

    private function authorizeAccess(Request $request, Exam $exam): CandidateUser
    {
        abort_unless($request->session()->get('cbt.exam_id') === $exam->id &&
            $exam->is_active && $exam->program?->slug === 'sak', 404);
        $candidate = CandidateUser::find($request->session()->get('cbt.candidate_user_id'));
        abort_unless($candidate && ($candidate->status === 'active' ||
            ($candidate->status === 'pre_generated' && config('cbt.allow_pre_generated_candidates'))), 404);

        return $candidate;
    }
}
