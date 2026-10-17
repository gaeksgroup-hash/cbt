<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Support\CandidateSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuideController extends Controller
{
    public function show(Request $request, Exam $exam, CandidateSession $session): View
    {
        $candidate = $session->exam($request, $exam);
        $previous = $candidate->attempts()->where('exam_id', $exam->id)
            ->whereIn('status', ['submitted', 'timed_out'])->get();

        return view('sak.guide', [
            'exam' => $exam,
            'attemptCount' => $previous->count(),
            'bestScore' => $previous->where('grading_status', 'final')->max('score_final'),
            'questionCount' => $exam->examQuestions()->count(),
            'latestAttempts' => $previous->sortByDesc('id')->take(4),
        ]);
    }

    public function demo(Request $request, Exam $exam, CandidateSession $session): View
    {
        $session->exam($request, $exam);

        return view('sak.demo', ['exam' => $exam]);
    }
}
