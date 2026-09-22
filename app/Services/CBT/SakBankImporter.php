<?php

namespace App\Services\CBT;

use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Program;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;

class SakBankImporter
{
    /** @return array<string, mixed> */
    public function read(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException("Cannot read SAK bank file: {$path}");
        }

        try {
            $manifest = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Invalid SAK bank JSON: '.$exception->getMessage(), previous: $exception);
        }

        if (! is_array($manifest)) {
            throw new RuntimeException('SAK bank root must be an object.');
        }

        $this->validate($manifest);

        return $manifest;
    }

    /** @param array<string, mixed> $manifest */
    public function validate(array $manifest): void
    {
        if (($manifest['program_code'] ?? null) !== 'SAK' || ! is_array($manifest['questions'] ?? null)) {
            throw new RuntimeException('Expected SAK program and questions array.');
        }

        $questions = $manifest['questions'];
        $counts = [];
        $codes = [];
        $positions = [];

        foreach ($questions as $question) {
            if (! is_array($question)) {
                throw new RuntimeException('Every question must be an object.');
            }

            $code = $question['code'] ?? null;
            $exam = $question['exam_code'] ?? null;
            $section = $question['section_code'] ?? null;
            $type = $question['type'] ?? null;
            $position = null;

            if (preg_match('/^SAK-B(0[1-9]|1[01])-Q(0(0[1-9]|10))$/', (string) $code, $match)) {
                $expectedExam = 'SAK-BAB'.$match[1];
                $position = (int) $match[2];
                if ($exam !== $expectedExam || $section !== null) {
                    throw new RuntimeException("Invalid exam/section mapping for {$code}.");
                }
            } elseif (preg_match('/^SAK-TRYOUT-(S[12])-Q(\d{3})$/', (string) $code, $match)) {
                $position = (int) $match[2];
                if ($exam !== 'SAK-BAB12' || $section !== $match[1] ||
                    $position < 1 || $position > ($section === 'S1' ? 2 : 30)) {
                    throw new RuntimeException("Invalid tryout exam/section mapping for {$code}.");
                }
            } else {
                throw new RuntimeException("Invalid question code: {$code}.");
            }

            if (isset($codes[$code])) {
                throw new RuntimeException("Duplicate question code: {$code}.");
            }
            $codes[$code] = true;
            $bucket = $section ?? $exam;
            $counts[$bucket] = ($counts[$bucket] ?? 0) + 1;
            if (isset($positions[$bucket][$position])) {
                throw new RuntimeException("Duplicate position {$position} in {$bucket}.");
            }
            $positions[$bucket][$position] = true;

            if (! in_array($type, ['single_choice', 'numeric', 'essay_manual'], true) ||
                ! is_string($question['stem'] ?? null) || trim($question['stem']) === '' ||
                ! is_numeric($question['points'] ?? null) || $question['points'] <= 0 ||
                ! is_string($question['explanation'] ?? null) ||
                ($section !== 'S2' && trim($question['explanation']) === '') ||
                ! is_int($question['source_page'] ?? null) || $question['source_page'] < 1) {
                throw new RuntimeException("Incomplete question or scoring metadata: {$code}.");
            }

            $options = $question['options'] ?? null;
            if (! is_array($options)) {
                throw new RuntimeException("Invalid options: {$code}.");
            }
            if ($type === 'single_choice') {
                if (count($options) !== 4 || array_column($options, 'key') !== ['A', 'B', 'C', 'D'] ||
                    ! in_array($question['answer_key'] ?? null, ['A', 'B', 'C', 'D'], true)) {
                    throw new RuntimeException("Invalid four options or answer key: {$code}.");
                }
                foreach ($options as $option) {
                    if (! is_string($option['text'] ?? null) || trim($option['text']) === '') {
                        throw new RuntimeException("Empty option: {$code}.");
                    }
                }
            } elseif ($options !== [] || ($type === 'essay_manual' && ($question['answer_key'] ?? null) !== null) ||
                ($type === 'numeric' && (! is_array($question['answer_key'] ?? null) ||
                    ! is_numeric($question['answer_key']['expected'] ?? null) ||
                    ! is_numeric($question['answer_key']['tolerance_absolute'] ?? null)))) {
                throw new RuntimeException("Invalid non-choice answer: {$code}.");
            }
        }

        $expected = array_fill_keys(array_map(fn ($n) => sprintf('SAK-BAB%02d', $n), range(1, 11)), 10);
        $expected['S1'] = 2;
        $expected['S2'] = 30;
        if (count($questions) !== 142 || $counts !== $expected) {
            // Comparison independent of question order, but strict about every expected bucket.
            ksort($counts);
            ksort($expected);
            if (count($questions) !== 142 || $counts !== $expected) {
                throw new RuntimeException('SAK manifest requires 11 x 10 chapter items, 2 S1 items and 30 S2 items (142 total).');
            }
        }
    }

    /** @param array<string, mixed> $manifest */
    public function import(array $manifest): void
    {
        $this->validate($manifest);

        DB::transaction(function () use ($manifest) {
            $program = Program::where('code', 'SAK')->firstOrFail();
            $exams = Exam::where('program_id', $program->id)->get()->keyBy('code');
            if ($exams->count() !== 12) {
                throw new RuntimeException('Seed all 12 SAK exams before importing the bank.');
            }

            $bank = QuestionBank::firstOrCreate(
                ['program_id' => $program->id, 'code' => 'SAK-V1'],
                ['name' => 'SAK canonical book bank v1', 'version' => 1, 'is_active' => true]
            );

            foreach ($manifest['questions'] as $item) {
                $exam = $exams->get($item['exam_code']);
                if (! $exam) {
                    throw new RuntimeException('Unknown exam: '.$item['exam_code']);
                }
                $attributes = [
                    'question_bank_id' => $bank->id,
                    'version' => 1,
                    'type' => $item['type'],
                    'title' => $item['title'],
                    'stem' => $item['stem'],
                    'points' => $item['points'],
                    'explanation' => $item['explanation'],
                    'legal_reference' => $item['legal_reference'],
                    'answer_key_json' => $item['type'] === 'single_choice'
                        ? ['correct_options' => [$item['answer_key']]] : $item['answer_key'],
                    'scoring_rules_json' => $item['type'] === 'essay_manual' ? ['mode' => 'manual_review'] : null,
                    'metadata_json' => ['source_title' => $manifest['source_title'] ?? null, 'source_page' => $item['source_page']],
                    'is_active' => true,
                ];
                $question = Question::firstOrCreate(['code' => $item['code']], $attributes);
                if ($question->question_bank_id !== $bank->id || $question->stem !== $item['stem'] ||
                    $question->type !== $item['type'] || (int) $question->version !== 1 ||
                    $question->title !== $item['title'] ||
                    (float) $question->points !== (float) $item['points'] ||
                    $question->explanation !== $item['explanation'] ||
                    $question->legal_reference !== $item['legal_reference'] ||
                    $question->answer_key_json !== $attributes['answer_key_json'] ||
                    $question->scoring_rules_json !== $attributes['scoring_rules_json']) {
                    throw new RuntimeException('Existing question differs; use an explicit versioned editorial update: '.$item['code']);
                }

                if ($question->options()->count() !== 0 && $question->options()->count() !== count($item['options'])) {
                    throw new RuntimeException('Existing option count differs: '.$item['code']);
                }

                foreach ($item['options'] as $order => $option) {
                    $saved = QuestionOption::firstOrCreate(
                        ['question_id' => $question->id, 'option_key' => $option['key']],
                        ['option_text' => $option['text'], 'is_correct' => $option['key'] === $item['answer_key'], 'sort_order' => $order + 1]
                    );
                    if ($saved->option_text !== $option['text'] || $saved->is_correct !== ($option['key'] === $item['answer_key']) ||
                        $saved->sort_order !== $order + 1) {
                        throw new RuntimeException('Existing option differs: '.$item['code'].'/'.$option['key']);
                    }
                }

                $number = (int) substr($item['code'], -3);
                $link = ExamQuestion::firstOrCreate(
                    ['exam_id' => $exam->id, 'question_id' => $question->id],
                    ['section_code' => $item['section_code'], 'sort_order' => $item['section_code'] === 'S2' ? $number + 2 : $number]
                );
                if ($link->section_code !== $item['section_code'] ||
                    $link->sort_order !== ($item['section_code'] === 'S2' ? $number + 2 : $number)) {
                    throw new RuntimeException('Existing exam section differs: '.$item['code']);
                }
            }
        });
    }
}
