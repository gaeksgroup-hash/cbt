<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamToken;
use App\Models\Program;
use App\Services\CBT\ExamTokenResolver;
use App\Services\CBT\TokenHasher;
use Database\Seeders\SakExamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamTokenResolverTest extends TestCase
{
    use RefreshDatabase;

    protected ExamTokenResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SakExamSeeder::class);$this->resolver = app(ExamTokenResolver::class);
    }

    public function test_resolves_correct_exam_from_valid_token(): void
    {
        $exam =$this->resolver->resolve('SAK-BAB01-BTKI');
        $this->assertNotNull($exam);
        $this->assertEquals('SAK-BAB01',$exam->code);
        $this->assertEquals('bab-01-btki-npkb',$exam->slug);

        $tryout =$this->resolver->resolve('SAK-BAB12-TRYOUT');
        $this->assertNotNull($tryout);
        $this->assertEquals('SAK-BAB12',$tryout->code);
        $this->assertEquals('tryout',$tryout->exam_type);
    }

    public function test_resolves_normalized_token_with_whitespace_and_lowercase(): void
    {
        $exam =$this->resolver->resolve('   sak-bab02-hitung   ');
        $this->assertNotNull($exam);
        $this->assertEquals('SAK-BAB02',$exam->code);
    }

    public function test_returns_null_for_wrong_or_empty_token(): void
    {
        $this->assertNull($this->resolver->resolve('INVALID-TOKEN-123'));
        $this->assertNull($this->resolver->resolve(''));
        $this->assertNull($this->resolver->resolve('   '));
    }

    public function test_returns_null_when_token_is_deactivated(): void
    {
        $hasher = app(TokenHasher::class);
        $hash =$hasher->hash('SAK-BAB01-BTKI');

        ExamToken::where('token_hash', $hash)->update(['is_active' => false]);

        $this->assertNull($this->resolver->resolve('SAK-BAB01-BTKI'));
    }

    public function test_returns_null_when_exam_is_deactivated(): void
    {
        Exam::where('code', 'SAK-BAB01')->update(['is_active' => false]);

        $this->assertNull($this->resolver->resolve('SAK-BAB01-BTKI'));
    }

    public function test_returns_null_when_program_is_deactivated(): void
    {
        Program::where('code', 'SAK')->update(['is_active' => false]);

        $this->assertNull($this->resolver->resolve('SAK-BAB01-BTKI'));
    }

    public function test_returns_null_when_token_valid_from_is_in_the_future(): void
    {
        $hasher = app(TokenHasher::class);
        $hash =$hasher->hash('SAK-BAB01-BTKI');

        ExamToken::where('token_hash', $hash)->update([
            'valid_from' => now()->addDay(),
        ]);

        $this->assertNull($this->resolver->resolve('SAK-BAB01-BTKI'));
    }

    public function test_returns_null_when_token_valid_until_is_in_the_past(): void
    {
        $hasher = app(TokenHasher::class);
        $hash =$hasher->hash('SAK-BAB01-BTKI');

        ExamToken::where('token_hash', $hash)->update([
            'valid_until' => now()->subDay(),
        ]);

        $this->assertNull($this->resolver->resolve('SAK-BAB01-BTKI'));
    }

    public function test_resolves_when_token_is_within_validity_window(): void
    {
        $hasher = app(TokenHasher::class);
        $hash =$hasher->hash('SAK-BAB01-BTKI');

        ExamToken::where('token_hash', $hash)->update([
            'valid_from' => now()->subHour(),
            'valid_until' => now()->addHour(),
        ]);

        $exam =$this->resolver->resolve('SAK-BAB01-BTKI');
        $this->assertNotNull($exam);
        $this->assertEquals('SAK-BAB01',$exam->code);
    }
}
