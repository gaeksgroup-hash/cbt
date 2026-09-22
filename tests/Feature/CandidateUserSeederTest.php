<?php

namespace Tests\Feature;

use App\Models\CandidateUser;
use Database\Seeders\CandidateUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_exactly_10000_candidates(): void
    {
        $this->assertEquals(0, CandidateUser::count());

        $this->seed(CandidateUserSeeder::class);

        $this->assertEquals(10000, CandidateUser::count());
    }

    public function test_public_ids_are_strictly_unique(): void
    {
        $this->seed(CandidateUserSeeder::class);

        $totalCount = CandidateUser::count();$distinctCount = CandidateUser::distinct('public_id')->count('public_id');

        $this->assertEquals(10000,$totalCount);
        $this->assertEquals(10000,$distinctCount);
    }

    public function test_boundary_records_exist_with_correct_formatting(): void
    {
        $this->seed(CandidateUserSeeder::class);

        $this->assertTrue(CandidateUser::where('public_id', 'GSAK_CBT001')->exists());$this->assertTrue(CandidateUser::where('public_id', 'GSAK_CBT999')->exists());
        $this->assertTrue(CandidateUser::where('public_id', 'GSAK_CBT1000')->exists());$this->assertTrue(CandidateUser::where('public_id', 'GSAK_CBT10000')->exists());

        $this->assertFalse(CandidateUser::where('public_id', 'GSAK_CBT000')->exists());
        $this->assertFalse(CandidateUser::where('public_id', 'GSAK_CBT01000')->exists());$this->assertFalse(CandidateUser::where('public_id', 'GSAK_CBT10001')->exists());
    }

    public function test_new_candidates_have_default_pre_generated_status(): void
    {
        $this->seed(CandidateUserSeeder::class);

        $first = CandidateUser::where('public_id', 'GSAK_CBT001')->first();
        $this->assertNotNull($first);
        $this->assertEquals('pre_generated',$first->status);
        $this->assertNull($first->name);
        $this->assertNull($first->email);
        $this->assertNull($first->activated_at);

        $last = CandidateUser::where('public_id', 'GSAK_CBT10000')->first();
        $this->assertNotNull($last);
        $this->assertEquals('pre_generated',$last->status);
        $this->assertNull($last->name);
        $this->assertNull($last->email);
        $this->assertNull($last->activated_at);
    }

    public function test_seeder_is_idempotent_on_rerun(): void
    {
        $this->seed(CandidateUserSeeder::class);$this->assertEquals(10000, CandidateUser::count());

        $this->seed(CandidateUserSeeder::class);$this->assertEquals(10000, CandidateUser::count());
    }

    public function test_rerun_is_non_destructive_to_modified_candidates(): void
    {
        $this->seed(CandidateUserSeeder::class);

        $candidate = CandidateUser::where('public_id', 'GSAK_CBT001')->first();
        $this->assertNotNull($candidate);

        $activatedTime = now()->subDays(2);$candidate->update([
            'status' => 'active',
            'name' => 'Denyt Triawan',
            'email' => 'candidate1@gaeks.com',
            'activated_at' => $activatedTime,
        ]);

        $this->seed(CandidateUserSeeder::class);

        $reloaded = CandidateUser::where('public_id', 'GSAK_CBT001')->first();
        $this->assertNotNull($reloaded);
        $this->assertEquals('active',$reloaded->status);
        $this->assertEquals('Denyt Triawan',$reloaded->name);
        $this->assertEquals('candidate1@gaeks.com', $reloaded->email);
        $this->assertEquals($activatedTime->toDateTimeString(), $reloaded->activated_at->toDateTimeString());$this->assertEquals(10000, CandidateUser::count());
    }
}
