<?php

namespace App\Services\CBT;

use RuntimeException;

class TokenHasher
{
    public function normalize(string $token): string
    {
        return strtoupper(trim($token));
    }

    public function hash(string $token, ?string $pepper = null): string
    {
        $pepper = $pepper ?? config('cbt.token_pepper');

        if ($pepper === null || $pepper === '') {
            throw new RuntimeException('CBT token pepper is not configured.');
        }

        return hash_hmac('sha256', $this->normalize($token), $pepper);
    }

    public function verify(string $token, string $storedHash, ?string $pepper = null): bool
    {
        return hash_equals($storedHash, $this->hash($token, $pepper));
    }
}
