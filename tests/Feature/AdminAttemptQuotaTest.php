<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Attempt;
use App\Models\CandidateUser;
use App\Models\Exam;
use App\Services\CBT\AttemptQuota;
use Database\Seeders\SakExamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminAttemptQuotaTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SakExamSeeder::class);
        $this->admin = AdminUser::create([
            'username' => 'Admincbt',
            'name' => 'Administrator CBT',
            'email' => 'admincbt@example.test',
            'password_hash' => Hash::make('test-password'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_a_local_user_that_opens_sak_without_gdp_request(): void
    {
        Http::preventStrayRequests();
        $this->post(route('admin.login.store'), [
            'username' => 'admincbt',
            'password' => 'test-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->post(route('admin.candidates.store'), [
            'public_id' => 'CBT-CLIENT001',
            'name' => 'Klien Uji',
            'email' => 'klien@example.test',
        ])->assertRedirect();

        $candidate = CandidateUser::where('public_id', 'CBT-CLIENT001')->firstOrFail();
        $this->assertDatabaseHas('candidate_entitlements', [
            'candidate_user_id' => $candidate->id,
            'entitlement_key' => 'SAK',
            'source' => 'admin',
        ]);

        $this->post('/admin/logout');
        $this->post('/sak/access', [
            'order_id' => 'CBT-CLIENT001',
            'token' => 'SAK-BAB01-BTKI',
        ])->assertStatus(303);
    }

    public function test_limit_popup_appears_at_three_attempts_and_admin_reset_opens_a_new_quota(): void
    {
        $candidate = CandidateUser::create(['public_id' => 'CBT-LIMIT001', 'status' => 'active']);
        $exam = Exam::where('code', 'SAK-BAB01')->firstOrFail();

        foreach (range(1, 3) as $attemptNo) {
            Attempt::create([
                'candidate_user_id' => $candidate->id,
                'exam_id' => $exam->id,
                'attempt_no' => $attemptNo,
                'status' => 'submitted',
                'started_at' => now()->subMinutes(5),
                'expires_at' => now()->subMinute(),
                'submitted_at' => now()->subMinute(),
                'score_auto' => 80,
                'score_final' => 80,
                'grading_status' => 'final',
            ]);
        }

        $candidateSession = [
            'cbt.candidate_user_id' => $candidate->id,
            'cbt.exam_id' => $exam->id,
            'cbt.entitlement_key' => 'SAK',
            'cbt.entitlement_verified_at' => now()->timestamp,
        ];
        $this->withSession($candidateSession)
            ->get(route('sak.exam.guide', $exam))
            ->assertOk()
            ->assertSee('3/3')
            ->assertSee('data-auto-open', false)
            ->assertSee('085608561745');

        $this->withSession($candidateSession)
            ->post(route('sak.exam.start', $exam), ['understood' => '1'])
            ->assertSessionHasErrors('attempt');

        $this->withSession(['cbt.admin_user_id' => $this->admin->id])
            ->post(route('admin.candidates.attempts.reset', [$candidate, $exam]))
            ->assertSessionHas('success');

        $usage = app(AttemptQuota::class)->usage($candidate, $exam);
        $this->assertSame(0, $usage['used']);
        $this->assertSame(3, $usage['remaining']);
        $this->assertDatabaseCount('attempts', 3);
        $this->assertDatabaseHas('audit_logs', ['action' => 'candidate.attempt_quota_reset']);
    }
}
