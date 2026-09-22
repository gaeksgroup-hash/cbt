<?php
namespace Tests\Feature;
use App\Models\AdminUser;
use App\Models\Attempt;
use App\Models\AttemptQuestion;
use App\Models\CandidateUser;
use App\Models\Exam;
use App\Models\ExamToken;
use App\Models\Program;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModelSecurityAndUuidTest extends TestCase {
    use RefreshDatabase;

    public function test_sensitive_attributes_are_hidden(): void {
        $p = Program::create(['code' => 'P1', 'slug' => 'p1', 'name' => 'P1']);
        $b = QuestionBank::create(['program_id' =>$p->id, 'code' => 'B1', 'name' => 'B1']);
        $e = Exam::create(['program_id' =>$p->id, 'code' => 'E1', 'slug' => 'e1', 'title' => 'E1', 'duration_seconds' => 1200]);
        $tok = ExamToken::create(['exam_id' =>$e->id, 'token_hash' => 'SECRET']);
        $this->assertArrayNotHasKey('token_hash',$tok->toArray());

        $q = Question::create(['question_bank_id' =>$b->id, 'code' => 'Q1', 'stem' => 'Soal', 'answer_key_json' => ['key' => 'A'], 'scoring_rules_json' => ['r' => 1]]);
        $this->assertArrayNotHasKey('answer_key_json',$q->toArray());
        $this->assertArrayNotHasKey('scoring_rules_json',$q->toArray());

        $opt = QuestionOption::create(['question_id' =>$q->id, 'option_key' => 'A', 'option_text' => 'T', 'is_correct' => true]);
        $this->assertArrayNotHasKey('is_correct',$opt->toArray());

        $adm = AdminUser::create(['name' => 'Adm', 'email' => 'adm@cbt.com', 'password_hash' => 'hash']);
        $this->assertArrayNotHasKey('password_hash',$adm->toArray());

        $c = CandidateUser::create(['public_id' => 'GSAK_CBT001']);$att = Attempt::create(['candidate_user_id' => $c->id, 'exam_id' =>$e->id, 'attempt_no' => 1]);
        $aq = AttemptQuestion::create(['attempt_id' =>$att->id, 'question_snapshot_json' => ['secret' => 1], 'display_order' => 1]);
        $this->assertArrayNotHasKey('question_snapshot_json',$aq->toArray());
    }

    public function test_attempt_generates_uuid_automatically(): void {
        $c1 = CandidateUser::create(['public_id' => 'GSAK_CBT001']);
        $c2 = CandidateUser::create(['public_id' => 'GSAK_CBT002']);$p = Program::create(['code' => 'P1', 'slug' => 'p1', 'name' => 'P1']);
        $e = Exam::create(['program_id' =>$p->id, 'code' => 'E1', 'slug' => 'e1', 'title' => 'E1', 'duration_seconds' => 1200]);

        $att1 = Attempt::create(['candidate_user_id' =>$c1->id, 'exam_id' => $e->id, 'attempt_no' => 1]);$att2 = Attempt::create(['candidate_user_id' => $c2->id, 'exam_id' =>$e->id, 'attempt_no' => 1]);

        $this->assertTrue(Str::isUuid($att1->public_uuid));$this->assertTrue(Str::isUuid($att2->public_uuid));$this->assertNotEquals($att1->public_uuid,$att2->public_uuid);
    }
}
