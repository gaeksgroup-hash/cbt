<?php

namespace Tests\Feature;

use App\Models\CandidateUser;
use App\Models\Exam;
use Database\Seeders\SakExamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SakExamSeeder::class);
        CandidateUser::create(['public_id' => 'GSAK_CBT001', 'status' => 'active']);
    }

    public function test_valid_pair_routes_to_mapped_guide_without_credentials_in_url(): void
    {
        $response = $this->post('/sak/access', [
            'user_id' => ' gsak_cbt001 ', 'token' => ' sak-bab01-btki ',
        ]);

        $response->assertStatus(303)->assertRedirect(route('sak.exam.guide',
            Exam::where('code', 'SAK-BAB01')->first()));
        $this->assertStringNotContainsString('GSAK_CBT001', $response->headers->get('Location'));
        $this->assertStringNotContainsString('SAK-BAB01-BTKI', $response->headers->get('Location'));
        $this->get($response->headers->get('Location'))->assertOk()->assertSee('Lihat contoh');
    }

    public function test_invalid_user_and_invalid_token_have_same_generic_error(): void
    {
        foreach ([
            ['user_id' => 'GSAK_CBT999', 'token' => 'SAK-BAB01-BTKI'],
            ['user_id' => 'GSAK_CBT001', 'token' => 'WRONG-TOKEN'],
        ] as $input) {
            $this->from('/sak')->post('/sak/access', $input)
                ->assertRedirect('/sak')
                ->assertSessionHasErrors(['access' => 'User ID atau token tidak valid / tidak aktif. Periksa kembali data akses Anda.']);
        }
    }

    public function test_pre_generated_access_is_configurable_and_suspended_is_rejected(): void
    {
        $candidate = CandidateUser::where('public_id', 'GSAK_CBT001')->first();
        $candidate->update(['status' => 'pre_generated']);
        $this->from('/sak')->post('/sak/access', ['user_id' => 'GSAK_CBT001', 'token' => 'SAK-BAB01-BTKI'])
            ->assertSessionHasErrors('access');

        config()->set('cbt.allow_pre_generated_candidates', true);
        $this->post('/sak/access', ['user_id' => 'GSAK_CBT001', 'token' => 'SAK-BAB01-BTKI'])
            ->assertStatus(303);

        $candidate->update(['status' => 'suspended']);
        $this->get(route('sak.exam.guide', Exam::where('code', 'SAK-BAB01')->first()))->assertNotFound();
    }

    public function test_token_cannot_open_different_exam_and_demo_does_not_start_attempt(): void
    {
        $first = Exam::where('code', 'SAK-BAB01')->first();
        $second = Exam::where('code', 'SAK-BAB02')->first();
        $this->get(route('sak.exam.guide', $first))->assertNotFound();

        $this->post('/sak/access', ['user_id' => 'GSAK_CBT001', 'token' => 'SAK-BAB01-BTKI'])
            ->assertStatus(303);
        $this->get(route('sak.exam.guide', $second))->assertNotFound();
        $this->get(route('sak.exam.demo', $first))->assertOk()->assertSee('Timer belum berjalan');
        $this->assertDatabaseCount('attempts', 0);
        $this->post('/sak/logout')->assertRedirect('/sak');
        $this->get(route('sak.exam.guide', $first))->assertNotFound();
    }
}
