<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Support\CandidateSession;
use App\Support\TryoutScore;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function show(Request $request, Attempt $attempt, CandidateSession $session): View
    {
        $candidate = $session->attempt($request, $attempt);
        abort_if($attempt->status === 'in_progress', 404);
        $attempt->load(['exam', 'attemptQuestions.answer']);

        $answered = $attempt->attemptQuestions->filter(fn ($question) => filled($question->answer?->answer_json))->count();
        $correct = $attempt->attemptQuestions->filter(fn ($question) => $question->answer?->is_correct === true)->count();
        $incorrect = $attempt->attemptQuestions->filter(fn ($question) => $question->answer?->is_correct === false)->count();
        $history = $candidate->attempts()->where('exam_id', $attempt->exam_id)
            ->where('session_code', $attempt->session_code)
            ->whereIn('status', ['submitted', 'timed_out'])
            ->orderByDesc('attempt_no')->get();
        $tryoutSummary = null;

        if ($attempt->exam->exam_type === 'tryout') {
            $tryoutAttempts = $candidate->attempts()->with('exam')
                ->where('exam_id', $attempt->exam_id)
                ->where('attempt_no', $attempt->attempt_no)
                ->whereIn('status', ['submitted', 'timed_out'])
                ->get();
            $tryoutSummary = TryoutScore::pairs($tryoutAttempts)->first();
        }

        return view('sak.result', compact('attempt', 'answered', 'correct', 'incorrect', 'history', 'tryoutSummary'));
    }
}
