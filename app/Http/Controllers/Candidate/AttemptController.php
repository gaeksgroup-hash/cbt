<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\AttemptQuestion;
use App\Models\Exam;
use App\Services\CBT\AttemptService;
use App\Services\CBT\ScoringService;
use App\Support\CandidateSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class AttemptController extends Controller
{
    public function start(
        Request $request,
        Exam $exam,
        AttemptService $attempts,
        CandidateSession $session,
        ?string $sessionCode = null
    ): RedirectResponse {
        $candidate = $session->exam($request, $exam);
        $request->validate(['understood' => ['accepted']]);

        try {
            $attempt = $attempts->start($candidate, $exam, $sessionCode);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['attempt' => $exception->getMessage()]);
        }

        return redirect()->route('sak.attempts.show', $attempt, 303);
    }

    public function show(
        Request $request,
        Attempt $attempt,
        CandidateSession $session,
        ScoringService $scoring
    ): View|RedirectResponse {
        $session->attempt($request, $attempt);
        if ($attempt->status === 'in_progress' && $attempt->expires_at?->isPast()) {
            $scoring->finalize($attempt, true);

            return redirect()->route('sak.attempts.result', $attempt, 303);
        }
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('sak.attempts.result', $attempt, 303);
        }

        $attempt->load(['exam', 'attemptQuestions.answer']);
        $questions = $attempt->attemptQuestions;
        $requested = max(1, min((int) $request->integer('q', 1), $questions->count()));
        $current = $questions->get($requested - 1);
        $answered = $questions->filter(fn ($question) => filled($question->answer?->answer_json))->count();

        return view('sak.attempt', compact('attempt', 'questions', 'current', 'requested', 'answered'));
    }

    public function save(
        Request $request,
        Attempt $attempt,
        AttemptQuestion $attemptQuestion,
        CandidateSession $session,
        ScoringService $scoring
    ): JsonResponse {
        $session->attempt($request, $attempt);
        abort_unless($attemptQuestion->attempt_id === $attempt->id, 404);

        if ($attempt->status !== 'in_progress' || $attempt->expires_at?->isPast()) {
            if ($attempt->status === 'in_progress') {
                $scoring->finalize($attempt, true);
            }

            return response()->json(['message' => 'Waktu ujian telah berakhir.', 'expired' => true], 409);
        }

        $snapshot = $attemptQuestion->question_snapshot_json;
        $type = $snapshot['type'];
        $rules = ['marked' => ['required', 'boolean']];
        if ($type === 'single_choice') {
            $rules['value'] = ['nullable', 'string', Rule::in(array_column($snapshot['options'] ?? [], 'key'))];
        } elseif ($type === 'numeric') {
            $rules['value'] = ['nullable', 'numeric'];
        } else {
            $rules['value'] = ['nullable', 'string', 'max:20000'];
        }
        $validated = $request->validate($rules);
        $answerJson = $validated['value'] === null || $validated['value'] === ''
            ? null
            : ($type === 'single_choice' ? ['selected' => $validated['value']] : ['value' => $validated['value']]);

        $answer = $attempt->attemptAnswers()->updateOrCreate(
            ['attempt_question_id' => $attemptQuestion->id],
            [
                'answer_json' => $answerJson,
                'is_marked_review' => $validated['marked'],
                'saved_at' => now(),
            ]
        );

        return response()->json([
            'saved_at' => $answer->saved_at->toIso8601String(),
            'answered' => $answerJson !== null,
        ]);
    }

    public function submit(
        Request $request,
        Attempt $attempt,
        CandidateSession $session,
        ScoringService $scoring
    ): RedirectResponse {
        $session->attempt($request, $attempt);
        $scoring->finalize($attempt, $attempt->expires_at?->isPast() ?? false);

        return redirect()->route('sak.attempts.result', $attempt, 303);
    }
}
