<?php
namespace Tests\Feature;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\AttemptQuestion;
use App\Models\CandidateUser;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamToken;
use App\Models\Program;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase {
    use RefreshDatabase;

    public function test_relationships_operate_correctly(): void {
        $program = Program::create(['code' => 'SAK', 'slug' => 'sak', 'name' => 'SAK']);$bank = $program->questionBanks()->create(['code' => 'B01', 'name' => 'Bank 01']);$exam = $program->exams()->create([             'code' => 'SAK-01', 'slug' => 'sak-01', 'title' => 'SAK Bab 01',             'duration_seconds' => 1200, 'pass_score' => 65.00         ]);$token = $exam->tokens()->create(['token_hash' => hash('sha256', 'TOK'), 'token_hint' => 'SAK-****']);$question = $bank->questions()->create(['code' => 'Q01', 'stem' => 'Stem soal', 'points' => 10.00]);$opt = $question->options()->create(['option_key' => 'A', 'option_text' => 'Pilihan A', 'is_correct' => true]);$pivot = ExamQuestion::create(['exam_id' => $exam->id, 'question_id' =>$question->id, 'sort_order' => 1]);

        $candidate = CandidateUser::create(['public_id' => 'GSAK_CBT001']);$attempt = Attempt::create(['candidate_user_id' => $candidate->id, 'exam_id' =>$exam->id, 'attempt_no' => 1]);
        $aq =$attempt->attemptQuestions()->create([
            'question_id' => $question->id, 'question_version' => 1,             'question_snapshot_json' => ['stem' => 'Stem soal'], 'display_order' => 1         ]);$aa = $attempt->attemptAnswers()->create(['attempt_question_id' =>$aq->id, 'answer_json' => ['selected' => 'A']]);

        $this->assertCount(1, $program->questionBanks);$this->assertCount(1, $program->exams);$this->assertCount(1, $bank->questions);$this->assertCount(1, $question->options);$this->assertCount(1, $exam->tokens);$this->assertCount(1, $exam->questions);$this->assertCount(1, $candidate->attempts);$this->assertCount(1, $attempt->attemptQuestions);$this->assertCount(1, $attempt->attemptAnswers);$this->assertEquals($aa->id,$aq->answer->id);
    }
}
