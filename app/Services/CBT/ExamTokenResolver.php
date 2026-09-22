<?php

namespace App\Services\CBT;

use App\Models\Exam;
use App\Models\ExamToken;
use RuntimeException;

class ExamTokenResolver
{
    public function __construct(
        protected TokenHasher $hasher
    ) {}

    public function resolve(string $rawToken): ?Exam
    {
        if (trim($rawToken) === '') {
            return null;
        }

        $tokenHash = $this->hasher->hash($rawToken);

        $examToken = ExamToken::where('token_hash', $tokenHash)
            ->with(['exam.program'])
            ->first();

        if (! $examToken || ! $examToken->is_active) {
            return null;
        }

        $now = now();

        if ($examToken->valid_from && $now->lt($examToken->valid_from)) {
            return null;
        }

        if ($examToken->valid_until && $now->gt($examToken->valid_until)) {
            return null;
        }

        $exam = $examToken->exam;
        if (! $exam || !$exam->is_active) {
            return null;
        }

        $program =$exam->program;
        if (! $program || !$program->is_active) {
            return null;
        }

        return $exam;
    }
}
