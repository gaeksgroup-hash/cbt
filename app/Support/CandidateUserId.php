<?php

namespace App\Support;

use InvalidArgumentException;

class CandidateUserId
{
    public const PREFIX = 'GSAK_CBT';
    public const MIN_NUMBER = 1;
    public const MAX_NUMBER = 10000;

    public static function fromNumber(int $number): string
    {
        if ($number < self::MIN_NUMBER || $number > self::MAX_NUMBER) {
            throw new InvalidArgumentException(
                sprintf('Candidate sequence number must be between %d and %d, %d given.', self::MIN_NUMBER, self::MAX_NUMBER, $number)
            );
        }

        return self::PREFIX . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    public static function isValid(string $id): bool
    {
        if (! str_starts_with($id, self::PREFIX)) {
            return false;
        }

        $numPart = substr($id, strlen(self::PREFIX));
        if (! ctype_digit($numPart)) {
            return false;
        }

        $num = (int) $numPart;
        if ($num < self::MIN_NUMBER || $num > self::MAX_NUMBER) {
            return false;
        }

        return self::fromNumber($num) === $id;
    }
}
