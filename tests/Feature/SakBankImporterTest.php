<?php

namespace Tests\Feature;

use App\Models\ExamQuestion;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Services\CBT\SakBankImporter;
use Database\Seeders\SakExamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class SakBankImporterTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $questions = [];
        for ($chapter = 1; $chapter <= 11; $chapter++) {
            for ($number = 1; $number <= 10; $number++) {
                $questions[] = $this->item(sprintf('SAK-B%02d-Q%03d', $chapter, $number),
                    sprintf('SAK-BAB%02d', $chapter), null);
            }
        }
        for ($number = 1; $number <= 2; $number++) {
            $item = $this->item(sprintf('SAK-TRYOUT-S1-Q%03d', $number), 'SAK-BAB12', 'S1');
            $item['type'] = 'essay_manual';
            $item['options'] = [];
            $item['answer_key'] = null;
            $item['points'] = 50;
            $questions[] = $item;
        }
        for ($number = 1; $number <= 30; $number++) {
            $questions[] = $this->item(sprintf('SAK-TRYOUT-S2-Q%03d', $number), 'SAK-BAB12', 'S2');
        }

        return ['program_code' => 'SAK', 'source_title' => 'Synthetic fixture', 'questions' => $questions];
    }

    private function item(string $code, string $exam, ?string $section): array
    {
        return [
            'code' => $code, 'exam_code' => $exam, 'section_code' => $section,
            'type' => 'single_choice', 'title' => 'Fixture', 'stem' => 'Question '.$code,
            'options' => [
                ['key' => 'A', 'text' => 'First'], ['key' => 'B', 'text' => 'Second'],
                ['key' => 'C', 'text' => 'Third'], ['key' => 'D', 'text' => 'Fourth'],
            ],
            'answer_key' => 'B', 'explanation' => 'Fixture explanation',
            'legal_reference' => '', 'points' => 10, 'source_page' => 10,
        ];
    }

    public function test_manifest_requires_all_items_and_valid_keys(): void
    {
        $manifest = $this->fixture();
        $manifest['questions'][0]['answer_key'] = 'E';

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid four options or answer key');
        app(SakBankImporter::class)->validate($manifest);
    }

    public function test_import_is_idempotent_and_preserves_existing_content(): void
    {
        $this->seed(SakExamSeeder::class);
        $manifest = $this->fixture();
        $importer = app(SakBankImporter::class);
        $importer->import($manifest);
        $importer->import($manifest);

        $this->assertSame(142, Question::count());
        $this->assertSame(142, ExamQuestion::count());
        $this->assertSame(140 * 4, QuestionOption::count());
        $this->assertSame('B', Question::where('code', 'SAK-B01-Q001')->first()->answer_key_json['correct_options'][0]);

        $manifest['questions'][0]['stem'] = 'Changed without version bump';
        try {
            $importer->import($manifest);
            $this->fail('Changed version-one content must be rejected.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Existing question differs', $exception->getMessage());
        }
        $this->assertSame('Question SAK-B01-Q001', Question::where('code', 'SAK-B01-Q001')->first()->stem);
    }
}
