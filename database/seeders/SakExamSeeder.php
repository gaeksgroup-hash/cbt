<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamToken;
use App\Models\Program;
use App\Services\CBT\TokenHasher;
use App\Support\SakExamCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SakExamSeeder extends Seeder
{
    public function run(?TokenHasher $hasher = null): void
    {
        $hasher = $hasher ?? app(TokenHasher::class);

        DB::transaction(function () use ($hasher) {
            $programData = SakExamCatalog::PROGRAM;
            $program = Program::firstOrCreate(
                ['code' => $programData['code']],
                [
                    'slug' => $programData['slug'],
                    'name' => $programData['name'],
                    'description' => $programData['description'],
                    'is_active' => $programData['is_active'],
                ]
            );

            foreach (SakExamCatalog::all() as $item) {
                $exam = Exam::firstOrCreate(
                    [
                        'program_id' => $program->id,
                        'code' => $item['code'],
                    ],
                    [
                        'slug' => $item['slug'],
                        'title' => $item['title'],
                        'exam_type' => $item['exam_type'],
                        'duration_seconds' => $item['duration_seconds'],
                        'pass_score' => $item['pass_score'],
                        'attempt_limit' => $item['attempt_limit'],
                        'randomize_questions' => $item['randomize_questions'],
                        'randomize_options' => $item['randomize_options'],
                        'result_review_mode' => $item['result_review_mode'],
                        'is_active' => $item['is_active'],
                    ]
                );

                $tokenHash = $hasher->hash($item['token']);

                $existingToken = ExamToken::where('token_hash', $tokenHash)->first();

                if ($existingToken) {
                    if ($existingToken->exam_id !== $exam->id) {
                        throw new RuntimeException(
                            sprintf(
                                'Data integrity conflict: Token [%s] is already assigned to exam ID [%d], cannot reassign to exam ID [%d] (%s).',
                                $item['token_hint'],
                                $existingToken->exam_id,
                                $exam->id,
                                $exam->code
                            )
                        );
                    }
                    continue;
                }

                ExamToken::create([
                    'exam_id' => $exam->id,
                    'token_hash' => $tokenHash,
                    'token_hint' => $item['token_hint'],
                    'is_active' => true,
                    'valid_from' => null,
                    'valid_until' => null,
                ]);
            }
        });
    }
}
