<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Support\CandidateSession;
use App\Support\TryoutScore;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __invoke(Request $request, CandidateSession $session): View
    {
        $candidate = $session->candidate($request);
        $attempts = $candidate->attempts()->with('exam.program')
            ->whereIn('status', ['submitted', 'timed_out'])
            ->latest('submitted_at')->get();
        $finalAttempts = $attempts->where('grading_status', 'final');
        $tryoutPairs = TryoutScore::pairs($attempts);
        $tryoutExamIds = $attempts->filter(fn ($attempt) => $attempt->exam->exam_type === 'tryout')->pluck('exam_id')->unique();
        $bestByExam = $finalAttempts->whereNotIn('exam_id', $tryoutExamIds)->groupBy('exam_id')->map->max('score_final');
        $bestTryout = $tryoutPairs->where('is_final', true)->max('score');

        if ($bestTryout !== null) {
            $bestByExam->put('tryout', $bestTryout);
        }

        return view('sak.history', [
            'candidate' => $candidate,
            'attempts' => $attempts,
            'totalAttempts' => $attempts->count(),
            'completedExams' => $attempts->pluck('exam_id')->unique()->count(),
            'totalBestScore' => round((float) $bestByExam->sum(), 2),
            'averageBestScore' => $bestByExam->isEmpty() ? null : round((float) $bestByExam->avg(), 2),
            'tryoutPairs' => $tryoutPairs,
        ]);
    }
}
