<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\CandidateUser;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Program;
use App\Models\Question;
use App\Models\QuestionBank;
use Database\Seeders\SakExamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AttemptLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private CandidateUser $candidate;
    private Exam $exam;
    private const ORDER_ID = 'GDP-20261017123045-A1B2C3D4';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SakExamSeeder::class);
        config()->set('cbt.gdp_entitlements.url', 'https://gdp.test/api/cbt-entitlements.php');
        config()->set('cbt.gdp_entitlements.secret', 'test-secret');
        Http::fake(Http::response([
            'ok' => true,
            'data' => ['orderId' => self::ORDER_ID, 'status' => 'paid', 'entitlementKeys' => ['SAK']],
        ]));
        $this->candidate = CandidateUser::create(['public_id' => self::ORDER_ID, 'status' => 'active']);
        $this->exam = Exam::where('code', 'SAK-BAB03')->firstOrFail();
        $bank = QuestionBank::create([
            'program_id' => Program::where('code', 'SAK')->value('id'),
            'code' => 'TEST-BANK',
            'name' => 'Test bank',
            'version' => 1,
            'is_active' => true,
        ]);
        $question = Question::create([
            'question_bank_id' => $bank->id,
            'code' => 'SAK-B03-Q001',
            'version' => 1,
            'type' => 'single_choice',
            'title' => 'Daerah Pabean',
            'stem' => 'Pilih jawaban yang benar.',
            'points' => 10,
            'explanation' => 'Pembahasan pengujian.',
            'answer_key_json' => ['correct_options' => ['B']],
            'is_active' => true,
        ]);
        foreach (['A' => 'Salah satu', 'B' => 'Jawaban benar', 'C' => 'Salah dua', 'D' => 'Salah tiga'] as $key => $text) {
            $question->options()->create([
                'option_key' => $key,
                'option_text' => $text,
                'is_correct' => $key === 'B',
                'sort_order' => ord($key) - 64,
            ]);
        }
        ExamQuestion::create([
            'exam_id' => $this->exam->id,
            'question_id' => $question->id,
            'sort_order' => 1,
            'weight' => 1,
            'required' => true,
        ]);

        $this->post('/sak/access', [
            'order_id' => $this->candidate->public_id,
            'token' => 'SAK-BAB03-UUBC',
        ])->assertStatus(303);
    }

    public function test_start_save_submit_result_and_retry_preserve_history(): void
    {
        $start = $this->post(route('sak.exam.start', $this->exam), ['understood' => '1']);
        $attempt = Attempt::firstOrFail();
        $start->assertStatus(303)->assertRedirect(route('sak.attempts.show', $attempt));

        $page = $this->get(route('sak.attempts.show', $attempt));
        $page->assertOk()->assertSee('Pilih jawaban yang benar.')
            ->assertDontSee('correct_options');

        $attemptQuestion = $attempt->attemptQuestions()->firstOrFail();
        $this->putJson(route('sak.attempts.save', [$attempt, $attemptQuestion]), [
            'value' => 'B',
            'marked' => false,
        ])->assertOk()->assertJson(['answered' => true]);

        $this->post(route('sak.attempts.submit', $attempt))->assertStatus(303);
        $attempt->refresh();
        $this->assertSame('final', $attempt->grading_status);
        $this->assertSame('100.00', $attempt->score_final);
        $this->assertTrue($attempt->passed);
        $this->get(route('sak.attempts.result', $attempt))->assertOk()->assertSee('100')->assertSee('Target tercapai');

        $this->post(route('sak.exam.start', $this->exam), ['understood' => '1'])->assertStatus(303);
        $this->assertDatabaseCount('attempts', 2);
        $this->assertDatabaseHas('attempts', ['attempt_no' => 1, 'score_final' => 100]);
        $this->assertDatabaseHas('attempts', ['attempt_no' => 2, 'status' => 'in_progress']);
        $this->get(route('sak.results'))->assertOk()->assertSee('Total attempt')->assertSee(self::ORDER_ID);
    }

    public function test_expired_attempt_is_finalized_and_rejects_late_answers(): void
    {
        $this->post(route('sak.exam.start', $this->exam), ['understood' => '1']);
        $attempt = Attempt::firstOrFail();
        $attempt->update(['expires_at' => now()->subSecond()]);
        $attemptQuestion = $attempt->attemptQuestions()->firstOrFail();

        $this->putJson(route('sak.attempts.save', [$attempt, $attemptQuestion]), [
            'value' => 'B',
            'marked' => false,
        ])->assertStatus(409)->assertJson(['expired' => true]);

        $this->assertDatabaseHas('attempts', ['id' => $attempt->id, 'status' => 'timed_out']);
    }
}
