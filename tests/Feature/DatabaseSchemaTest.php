<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase {
    use RefreshDatabase;

    public function test_all_thirteen_cbt_tables_exist(): void {
        $tables = [
            'candidate_users', 'programs', 'question_banks', 'exams',
            'exam_tokens', 'questions', 'question_options', 'exam_questions',
            'attempts', 'attempt_questions', 'attempt_answers', 'admin_users', 'audit_logs'
        ];
        foreach ($tables as $t) {$this->assertTrue(Schema::hasTable($t), "Table {$t} does not exist.");
        }
    }

    public function test_critical_columns_exist(): void {
        $this->assertTrue(Schema::hasColumns('candidate_users', ['id', 'public_id', 'status']));
        $this->assertTrue(Schema::hasColumns('programs', ['id', 'code', 'slug', 'name']));$this->assertTrue(Schema::hasColumns('question_banks', ['id', 'program_id', 'code', 'version']));
        $this->assertTrue(Schema::hasColumns('exams', ['id', 'program_id', 'code', 'slug', 'duration_seconds', 'pass_score']));$this->assertTrue(Schema::hasColumns('exam_tokens', ['id', 'exam_id', 'token_hash', 'token_hint']));
        $this->assertTrue(Schema::hasColumns('questions', ['id', 'question_bank_id', 'code', 'type', 'points', 'answer_key_json', 'scoring_rules_json']));$this->assertTrue(Schema::hasColumns('question_options', ['id', 'question_id', 'option_key', 'is_correct']));
        $this->assertTrue(Schema::hasColumns('exam_questions', ['id', 'exam_id', 'question_id', 'section_code', 'sort_order', 'weight']));$this->assertTrue(Schema::hasColumns('attempts', ['id', 'public_uuid', 'candidate_user_id', 'exam_id', 'attempt_no', 'status']));
        $this->assertTrue(Schema::hasColumns('attempt_questions', ['id', 'attempt_id', 'question_snapshot_json', 'display_order']));$this->assertTrue(Schema::hasColumns('attempt_answers', ['id', 'attempt_id', 'attempt_question_id', 'answer_json', 'is_marked_review']));
        $this->assertTrue(Schema::hasColumns('admin_users', ['id', 'email', 'password_hash', 'role']));$this->assertTrue(Schema::hasColumns('audit_logs', ['id', 'actor_type', 'action', 'entity_type', 'created_at']));
    }
}
