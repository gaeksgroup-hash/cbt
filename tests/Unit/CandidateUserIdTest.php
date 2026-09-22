<?php

namespace Tests\Unit;

use App\Support\CandidateUserId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CandidateUserIdTest extends TestCase
{
    public function test_generates_correct_padding_for_single_and_double_digits(): void
    {
        $this->assertEquals('GSAK_CBT001', CandidateUserId::fromNumber(1));
        $this->assertEquals('GSAK_CBT002', CandidateUserId::fromNumber(2));$this->assertEquals('GSAK_CBT009', CandidateUserId::fromNumber(9));
        $this->assertEquals('GSAK_CBT010', CandidateUserId::fromNumber(10));$this->assertEquals('GSAK_CBT099', CandidateUserId::fromNumber(99));
    }

    public function test_generates_correct_padding_for_three_digits(): void
    {
        $this->assertEquals('GSAK_CBT100', CandidateUserId::fromNumber(100));
        $this->assertEquals('GSAK_CBT500', CandidateUserId::fromNumber(500));$this->assertEquals('GSAK_CBT999', CandidateUserId::fromNumber(999));
    }

    public function test_preserves_four_and_five_digits_without_extra_padding(): void
    {
        $this->assertEquals('GSAK_CBT1000', CandidateUserId::fromNumber(1000));$this->assertNotEquals('GSAK_CBT01000', CandidateUserId::fromNumber(1000));
        $this->assertEquals('GSAK_CBT9999', CandidateUserId::fromNumber(9999));$this->assertEquals('GSAK_CBT10000', CandidateUserId::fromNumber(10000));
    }

    public function test_throws_exception_on_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CandidateUserId::fromNumber(0);
    }

    public function test_throws_exception_on_negative_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CandidateUserId::fromNumber(-1);
    }

    public function test_throws_exception_above_maximum_range(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CandidateUserId::fromNumber(10001);
    }

    public function test_is_valid_helper(): void
    {
        $this->assertTrue(CandidateUserId::isValid('GSAK_CBT001'));$this->assertTrue(CandidateUserId::isValid('GSAK_CBT999'));
        $this->assertTrue(CandidateUserId::isValid('GSAK_CBT1000'));$this->assertTrue(CandidateUserId::isValid('GSAK_CBT10000'));

        $this->assertFalse(CandidateUserId::isValid('GSAK_CBT000'));$this->assertFalse(CandidateUserId::isValid('GSAK_CBT01000'));
        $this->assertFalse(CandidateUserId::isValid('GSAK_CBT10001'));$this->assertFalse(CandidateUserId::isValid('INVALID_PREFIX'));
    }
}
