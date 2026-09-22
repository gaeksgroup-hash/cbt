<?php

namespace App\Services\CBT;

use App\Models\CandidateUser;
use App\Support\CandidateUserId;

class CandidateAccessService
{
    public function __construct(
        private readonly ExamTokenResolver $resolver
    ) {}

    public function resolve(
        string $userId,
        string $token
    ): ?array {
        $userId = strtoupper(trim($userId));

        $candidate = CandidateUserId::isValid($userId)
            ? CandidateUser::where('public_id', $userId)->first()
            : null;

        $exam = $this->resolver->resolve($token);

        if (
            ! $candidate ||
            ! in_array(
                $candidate->status,
                config('cbt.candidate_allowed_statuses', []),
                true
            ) ||
            ! $exam ||
            $exam->program?->slug !== 'sak'
        ) {
            return null;
        }

        return [
            'candidate' => $candidate,
            'exam' => $exam,
        ];
    }
}
