<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\CandidateUser;
use App\Models\Exam;
use App\Services\CBT\ScoringService;
use App\Support\SakEssayRubrics;
use Database\Seeders\SakExamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomaticEssayScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_essay_is_finalized_automatically_and_regrade_preserves_timing(): void
    {
        $this->seed(SakExamSeeder::class);
        $candidate = CandidateUser::create(['public_id' => 'CBT-ESSAYTEST', 'status' => 'active']);
        $exam = Exam::where('code', 'SAK-BAB01')->firstOrFail();
        $attempt = Attempt::create([
            'candidate_user_id' => $candidate->id,
            'exam_id' => $exam->id,
            'attempt_no' => 1,
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(5),
            'expires_at' => now()->addMinutes(15),
            'grading_status' => 'pending',
        ]);
        $question = $attempt->attemptQuestions()->create([
            'question_version' => 1,
            'question_snapshot_json' => [
                'code' => 'SAK-B01-Q010',
                'type' => 'essay_manual',
                'title' => 'Klasifikasi smartwatch',
                'stem' => 'Jelaskan klasifikasinya.',
                'explanation' => 'Pembahasan tersedia setelah ujian.',
                'answer_key' => null,
                'scoring_rules' => SakEssayRubrics::for('SAK-B01-Q010'),
                'options' => [],
            ],
            'display_order' => 1,
            'max_points' => 10,
        ]);
        $question->answer()->create([
            'attempt_id' => $attempt->id,
            'answer_json' => ['value' => 'Smartwatch adalah perangkat multifungsi. Catatan 1 n Bab 91 mengecualikannya. KUMHS 1 dan KUMHS 3 b menunjukkan karakter hakiki pada pos 85.17, yaitu 8517 62 59, bukan pos 91.02.'],
            'saved_at' => now(),
        ]);

        $scored = app(ScoringService::class)->finalize($attempt);
        $this->assertSame('final', $scored->grading_status);
        $this->assertSame('100.00', $scored->score_final);
        $this->assertTrue($scored->passed);

        $submittedAt = $scored->submitted_at->toISOString();
        $duration = $scored->duration_used_seconds;
        $rescored = app(ScoringService::class)->regrade($scored);
        $this->assertSame($submittedAt, $rescored->submitted_at->toISOString());
        $this->assertSame($duration, $rescored->duration_used_seconds);
    }
}
