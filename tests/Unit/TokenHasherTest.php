<?php

namespace Tests\Unit;

use App\Services\CBT\TokenHasher;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TokenHasherTest extends TestCase
{
    protected TokenHasher $hasher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hasher = new TokenHasher;
    }

    public function test_normalize_trims_and_uppercases(): void
    {
        $this->assertEquals('SAK-BAB01-BTKI',$this->hasher->normalize('  sak-bab01-btki  '));
        $this->assertEquals('SAK-BAB12-TRYOUT',$this->hasher->normalize("\tsak-bab12-tryout\n"));
    }

    public function test_hash_produces_deterministic_64_character_hex(): void
    {
        $pepper = 'my-test-pepper';
        $hash1 =$this->hasher->hash('sak-bab01-btki', $pepper);$hash2 = $this->hasher->hash('SAK-BAB01-BTKI',$pepper);

        $this->assertEquals($hash1, $hash2);$this->assertEquals(64, strlen($hash1));$this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/',$hash1);
        $this->assertNotEquals('SAK-BAB01-BTKI',$hash1);
    }

    public function test_verify_returns_true_for_matching_token(): void
    {
        $pepper = 'my-test-pepper';$hash = $this->hasher->hash('SAK-BAB01-BTKI',$pepper);

        $this->assertTrue($this->hasher->verify('SAK-BAB01-BTKI', $hash,$pepper));
        $this->assertTrue($this->hasher->verify('  sak-bab01-btki ', $hash,$pepper));
        $this->assertFalse($this->hasher->verify('WRONG-TOKEN', $hash,$pepper));
    }

    public function test_throws_exception_when_pepper_is_empty(): void
    {
        $this->expectException(RuntimeException::class);$this->expectExceptionMessage('CBT token pepper is not configured.');

        $this->hasher->hash('SAK-BAB01-BTKI', '');
    }
}
