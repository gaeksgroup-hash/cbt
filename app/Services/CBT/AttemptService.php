<?php

namespace App\Services\CBT;

use App\Models\Attempt;
use App\Models\CandidateUser;
use App\Models\Exam;
use App\Models\ExamQuestion;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AttemptService
{
    public function __construct(
        protected AttemptQuota $quota,
        protected ScoringService $scoring
    ) {
    }

    public function start(CandidateUser $candidate, Exam $exam, ?string $sessionCode = null): Attempt
    {
        $sessionCode = $this->normalizeSession($exam, $sessionCode);

        return DB::transaction(function () use ($candidate, $exam, $sessionCode) {
            CandidateUser::query()->whereKey($candidate->id)->lockForUpdate()->firstOrFail();
            $active = Attempt::query()
                ->where('candidate_user_id', $candidate->id)
                ->where('exam_id', $exam->id)
                ->where('session_code', $sessionCode)
                ->where('status', 'in_progress')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if ($active && $active->expires_at?->isFuture()) {
                return $active;
            }

            if ($active) {
                $this->scoring->finalize($active, true);
            }

            $usage = $this->quota->usage($candidate, $exam);
            if ($usage['remaining'] <= 0) {
                throw new AttemptLimitReached($usage['limit']);
            }

            $links = ExamQuestion::query()
                ->where('exam_id', $exam->id)
                ->when($sessionCode, fn ($query) => $query->where('section_code', $sessionCode))
                ->with(['question.options'])
                ->orderBy('sort_order')
                ->get();

            if ($links->isEmpty()) {
                throw new RuntimeException('Paket soal belum tersedia.');
            }

            $attemptNo = (int) Attempt::query()
                ->where('candidate_user_id', $candidate->id)
                ->where('exam_id', $exam->id)
                ->where('session_code', $sessionCode)
                ->max('attempt_no') + 1;

            $duration = $exam->exam_type === 'tryout' ? 4500 : $exam->duration_seconds;
            $attempt = Attempt::create([
                'candidate_user_id' => $candidate->id,
                'exam_id' => $exam->id,
                'session_code' => $sessionCode,
                'attempt_no' => $attemptNo,
                'status' => 'in_progress',
                'started_at' => now(),
                'expires_at' => now()->addSeconds($duration),
                'grading_status' => 'pending',
            ]);

            foreach ($links as $index => $link) {
                $question = $link->question;
                $attempt->attemptQuestions()->create([
                    'question_id' => $question->id,
                    'question_version' => $question->version,
                    'question_snapshot_json' => [
                        'code' => $question->code,
                        'type' => $question->type,
                        'title' => $question->title,
                        'stem' => $question->stem,
                        'explanation' => $question->explanation,
                        'legal_reference' => $question->legal_reference,
                        'answer_key' => $question->answer_key_json,
                        'scoring_rules' => $question->scoring_rules_json,
                        'options' => $question->options->map(fn ($option) => [
                            'key' => $option->option_key,
                            'text' => $option->option_text,
                        ])->values()->all(),
                    ],
                    'display_order' => $index + 1,
                    'option_order_json' => $question->options->pluck('option_key')->values()->all(),
                    'section_code' => $link->section_code,
                    'max_points' => $question->points,
                ]);
            }

            return $attempt;
        });
    }

    private function normalizeSession(Exam $exam, ?string $sessionCode): ?string
    {
        if ($exam->exam_type !== 'tryout') {
            return null;
        }

        $sessionCode = strtoupper((string) $sessionCode);
        abort_unless(in_array($sessionCode, ['S1', 'S2'], true), 404);

        return $sessionCode;
    }
}
