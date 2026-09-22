<?php

namespace Tests\Unit;

use App\Support\SakExamCatalog;
use PHPUnit\Framework\TestCase;

class SakExamCatalogTest extends TestCase
{
    public function test_catalog_has_exactly_twelve_exams(): void
    {
        $this->assertCount(12, SakExamCatalog::all());$this->assertEquals(12, SakExamCatalog::count());
    }

    public function test_internal_exam_codes_are_unique(): void
    {
        $codes = array_column(SakExamCatalog::all(), 'code');
        $this->assertCount(12, array_unique($codes));

        for ($i = 1; $i <= 12; $i++) {$expectedCode = sprintf('SAK-BAB%02d', $i);$this->assertContains($expectedCode,$codes);
        }
    }

    public function test_slugs_are_unique(): void
    {
        $slugs = array_column(SakExamCatalog::all(), 'slug');
        $this->assertCount(12, array_unique($slugs));
    }

    public function test_tokens_are_unique(): void
    {
        $tokens = array_column(SakExamCatalog::all(), 'token');
        $this->assertCount(12, array_unique($tokens));
    }

    public function test_no_internal_exam_code_equals_raw_token(): void
    {
        foreach (SakExamCatalog::all() as $item) {$this->assertNotEquals(
                $item['token'],$item['code'],
                "Security violation: Exam code [{$item['code']}] must not be identical to raw token [{$item['token']}]."
            );
        }
    }

    public function test_chapter_exams_have_standard_twenty_minute_config(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            $exam = SakExamCatalog::find($i);
            $this->assertNotNull($exam);
            $this->assertEquals('chapter',$exam['exam_type']);
            $this->assertEquals(1200,$exam['duration_seconds']);
            $this->assertEquals(65.00,$exam['pass_score']);
            $this->assertEquals('full_explanation',$exam['result_review_mode']);
            $this->assertTrue($exam['is_active']);
            $this->assertNull($exam['attempt_limit']);
        }
    }

    public function test_tryout_exam_has_tryout_config(): void
    {
        $tryout = SakExamCatalog::find(12);
        $this->assertNotNull($tryout);
        $this->assertEquals('tryout',$tryout['exam_type']);
        $this->assertEquals(9000,$tryout['duration_seconds']);
        $this->assertEquals(60.00,$tryout['pass_score']);
        $this->assertEquals('score_only',$tryout['result_review_mode']);
        $this->assertTrue($tryout['is_active']);
    }
}
