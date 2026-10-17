<?php

namespace App\Services\CBT;

use RuntimeException;

class AttemptLimitReached extends RuntimeException
{
    public function __construct(public readonly int $limit)
    {
        parent::__construct("Batas {$limit} attempt untuk paket ini sudah digunakan.");
    }
}
