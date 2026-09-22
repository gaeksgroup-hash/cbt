<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamToken;
use App\Models\Program;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Services\CBT\TokenHasher;
use App\Support\SakExamCatalog;
use Database\Seeders\SakExamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class SakExamSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_program_twelve_exams_and_twelve_tokens(): void
    {
        $this->seed(SakExamSeeder::class);

        $this->assertEquals(1, Program::count());$this->assertTrue(Program::where('code', 'SAK')->exists());

        $this->assertEquals(12, Exam::count());$this->assertEquals(12, ExamToken::count());

        $this->assertEquals(0, QuestionBank::count());$this->assertEquals(0, Question::count());
    }

    public function test_all_twelve_raw_tokens_resolve_to_correct_internal_exams(): void
    {
        $this->seed(SakExamSeeder::class);

        $hasher = app(TokenHasher::class);

        foreach (SakExamCatalog::all() as $item) {$expectedHash = $hasher->hash($item['token']);

            $tokenRow = ExamToken::where('token_hash',$expectedHash)->first();
            $this->assertNotNull($tokenRow, "Token hash not found for token [{$item['token_hint']}].");

            $this->assertEquals(
                $item['code'],$tokenRow->exam->code,
                "Token [{$item['token_hint']}] mapped to unexpected exam code [{$tokenRow->exam->code}]."
            );
        }
    }

    public function test_no_raw_token_is_stored_in_plaintext(): void
    {
        $this->seed(SakExamSeeder::class);

        foreach (SakExamCatalog::all() as $item) {
            $rawToken =$item['token'];

            $this->assertFalse(
                ExamToken::where('token_hash', $rawToken)->exists(),
                "Plaintext token leak: raw token found in token_hash column!"
            );

            $this->assertFalse(
                ExamToken::where('token_hint', $rawToken)->exists(),
                "Plaintext token leak: raw token found in token_hint column!"
            );

            $this->assertFalse(
                Exam::where('code', $rawToken)->exists(),
                "Plaintext token leak: raw token used as exam code!"
            );
        }
    }

    public function test_seeder_is_strictly_idempotent(): void
    {
        $this->seed(SakExamSeeder::class);$this->assertEquals(1, Program::count());
        $this->assertEquals(12, Exam::count());$this->assertEquals(12, ExamToken::count());

        $this->seed(SakExamSeeder::class);$this->assertEquals(1, Program::count());
        $this->assertEquals(12, Exam::count());$this->assertEquals(12, ExamToken::count());
    }

    public function test_seeder_is_non_destructive_to_admin_alterations(): void
    {
        $this->seed(SakExamSeeder::class);

        $exam1 = Exam::where('code', 'SAK-BAB01')->first();
        $this->assertNotNull($exam1);

        $customDesc = 'Deskripsi kustom yang diedit oleh administrator.';
        $exam1->update(['description' =>$customDesc]);

        $this->seed(SakExamSeeder::class);

        $reloaded = Exam::where('code', 'SAK-BAB01')->first();$this->assertEquals($customDesc,$reloaded->description);
    }

    public function test_throws_exception_if_token_hash_points_to_conflicting_exam(): void
    {
        $this->seed(SakExamSeeder::class);

        $exam2 = Exam::where('code', 'SAK-BAB02')->first();$hasher = app(TokenHasher::class);
        $token1Hash =$hasher->hash('SAK-BAB01-BTKI');

        ExamToken::where('token_hash', $token1Hash)->update(['exam_id' =>$exam2->id]);

        $this->expectException(RuntimeException::class);$this->expectExceptionMessage('Data integrity conflict');

        $this->seed(SakExamSeeder::class);
    }
}
