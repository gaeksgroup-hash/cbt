<?php

namespace Database\Seeders;

use App\Models\CandidateUser;
use App\Support\CandidateUserId;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CandidateUserSeeder extends Seeder
{
    public function run(): void
    {
        $chunkSize = 500;
        $now = now();$batch = [];

        DB::transaction(function () use ($chunkSize, $now, &$batch) {
            for ($i = CandidateUserId::MIN_NUMBER; $i <= CandidateUserId::MAX_NUMBER; $i++) {$batch[] = [
                    'public_id' => CandidateUserId::fromNumber($i),
                    'name' => null,
                    'email' => null,
                    'status' => 'pre_generated',
                    'activated_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($batch) >=$chunkSize) {
                    CandidateUser::insertOrIgnore($batch);$batch = [];
                }
            }

            if (! empty($batch)) {
                CandidateUser::insertOrIgnore($batch);$batch = [];
            }
        });
    }
}
