<?php

namespace App\Console\Commands;

use App\Models\Exam;
use App\Models\Program;
use Illuminate\Console\Command;

class ValidateSakBank extends Command
{
    protected $signature = 'cbt:validate-bank {program}';

    protected $description = 'Check persisted SAK exam/question manifest and answer keys';

    public function handle(): int
    {
        if (strtoupper($this->argument('program')) !== 'SAK') {
            $this->error('Only SAK has a canonical manifest.');
            return self::FAILURE;
        }

        $program = Program::where('code', 'SAK')->first();
        $exams = $program ? Exam::where('program_id', $program->id)
            ->with('examQuestions.question.options')->get()->keyBy('code') : collect();
        $errors = [];
        $seen = [];

        for ($chapter = 1; $chapter <= 12; $chapter++) {
            $code = sprintf('SAK-BAB%02d', $chapter);
            $links = $exams->get($code)?->examQuestions ?? collect();
            $expected = $chapter === 12 ? 32 : 10;
            $actual = $links->count();
            $this->line("{$code}: expected {$expected}, actual {$actual}");
            if ($actual !== $expected) {
                $errors[] = "{$code}: missing ".max(0, $expected - $actual).', extra '.max(0, $actual - $expected);
            }
            if ($chapter === 12) {
                foreach (['S1' => 2, 'S2' => 30] as $section => $count) {
                    $sectionActual = $links->where('section_code', $section)->count();
                    if ($sectionActual !== $count) {
                        $errors[] = "{$section}: expected {$count}, actual {$sectionActual}";
                    }
                }
            }
            foreach ($links as $link) {
                $question = $link->question;
                if (! $question || isset($seen[$question->code])) {
                    $errors[] = 'Missing/duplicate question in '.$code;
                    continue;
                }
                $seen[$question->code] = true;
                if ($question->type === 'single_choice') {
                    $options = $question->options;
                    $key = $question->answer_key_json['correct_options'][0] ?? null;
                    if ($options->count() !== 4 || $options->where('option_key', $key)->count() !== 1 ||
                        $options->where('is_correct', true)->count() !== 1 ||
                        $options->firstWhere('option_key', $key)?->is_correct !== true) {
                        $errors[] = 'Invalid answer key/options: '.$question->code;
                    }
                } elseif ($question->type === 'numeric') {
                    if (! is_numeric($question->answer_key_json['expected'] ?? null)) {
                        $errors[] = 'Invalid numeric key: '.$question->code;
                    }
                } elseif ($question->type !== 'essay_manual') {
                    $errors[] = 'Unknown question type: '.$question->code;
                }
            }
        }

        if ($exams->count() !== 12 || count($seen) !== 142) {
            $errors[] = 'Expected 12 exams and 142 unique linked items; actual '.$exams->count().' exams and '.count($seen).' items.';
        }
        foreach ($errors as $error) {
            $this->error($error);
        }
        $this->line($errors ? 'FAIL' : 'PASS: SAK persisted manifest and keys');

        return $errors ? self::FAILURE : self::SUCCESS;
    }
}
