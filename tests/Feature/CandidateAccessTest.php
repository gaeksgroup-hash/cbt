<?php

namespace Tests\Feature;

use App\Models\CandidateUser;
use App\Models\Exam;
use Database\Seeders\SakExamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CandidateAccessTest extends TestCase
{
    use RefreshDatabase;

    private const ORDER_ID = 'GDP-20261017123045-A1B2C3D4';

    private array $gdpResponse = [];

    private int $gdpStatus = 200;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SakExamSeeder::class);
        config()->set('cbt.gdp_entitlements.url', 'https://gdp.test/api/cbt-entitlements.php');
        config()->set('cbt.gdp_entitlements.secret', 'test-secret');
        $this->gdpResponse = [
            'ok' => true,
            'data' => [
                'orderId' => self::ORDER_ID,
                'status' => 'paid',
                'entitlementKeys' => ['SAK'],
            ],
        ];
        Http::fake(function (Request $request) {
            $response = $this->gdpResponse;
            if (($response['data']['orderId'] ?? null) === self::ORDER_ID) {
                $response['data']['orderId'] = $request['orderId'];
            }

            return Http::response($response, $this->gdpStatus);
        });
    }

    public function test_valid_order_and_book_token_route_to_mapped_guide_without_credentials_in_url(): void
    {
        $response = $this->post('/sak/access', [
            'order_id' => ' gdp-20261017123045-a1b2c3d4 ',
            'token' => ' sak-bab01-btki ',
        ]);

        $response->assertStatus(303)->assertRedirect(route('sak.exam.guide',
            Exam::where('code', 'SAK-BAB01')->first()));
        $this->assertStringNotContainsString(self::ORDER_ID, $response->headers->get('Location'));
        $this->assertStringNotContainsString('SAK-BAB01-BTKI', $response->headers->get('Location'));
        $this->assertDatabaseHas('candidate_users', ['public_id' => self::ORDER_ID, 'status' => 'active']);
        $this->get($response->headers->get('Location'))->assertOk()->assertSee('Lihat contoh');
    }

    public function test_invalid_order_and_invalid_token_have_same_generic_error(): void
    {
        foreach ([
            ['order_id' => 'GDP-20261017123045-FFFFFFFF', 'token' => 'WRONG-TOKEN'],
            ['order_id' => 'NOT-AN-ORDER', 'token' => 'SAK-BAB01-BTKI'],
        ] as $input) {
            $this->from('/sak')->post('/sak/access', $input)
                ->assertRedirect('/sak')
                ->assertSessionHasErrors(['access' => 'User ID / Order ID atau token buku tidak cocok dengan akses ujian ini.']);
        }
    }

    public function test_order_for_another_book_is_rejected(): void
    {
        $this->gdpResponse = [
            'ok' => true,
            'data' => [
                'orderId' => self::ORDER_ID,
                'status' => 'paid',
                'entitlementKeys' => ['FREIGHT'],
            ],
        ];

        $this->from('/sak')->post('/sak/access', [
            'order_id' => self::ORDER_ID,
            'token' => 'SAK-BAB01-BTKI',
        ])->assertSessionHasErrors('access');

        $this->assertDatabaseMissing('candidate_users', ['public_id' => self::ORDER_ID]);
    }

    public function test_suspended_order_identity_is_rejected_even_when_purchase_is_valid(): void
    {
        CandidateUser::create(['public_id' => self::ORDER_ID, 'status' => 'suspended']);

        $this->from('/sak')->post('/sak/access', [
            'order_id' => self::ORDER_ID,
            'token' => 'SAK-BAB01-BTKI',
        ])->assertSessionHasErrors('access');
    }

    public function test_token_cannot_open_different_exam_and_demo_does_not_start_attempt(): void
    {
        $first = Exam::where('code', 'SAK-BAB01')->first();
        $second = Exam::where('code', 'SAK-BAB02')->first();
        $this->get(route('sak.exam.guide', $first))->assertNotFound();

        $this->post('/sak/access', ['order_id' => self::ORDER_ID, 'token' => 'SAK-BAB01-BTKI'])
            ->assertStatus(303);
        $this->get(route('sak.exam.guide', $second))->assertNotFound();
        $this->get(route('sak.exam.demo', $first))->assertOk()->assertSee('Timer belum berjalan');
        $this->assertDatabaseCount('attempts', 0);
        $this->post('/sak/logout')->assertRedirect('/sak');
        $this->get(route('sak.exam.guide', $first))->assertNotFound();
    }

    public function test_verification_service_failure_is_reported_without_creating_an_identity(): void
    {
        $this->gdpResponse = ['ok' => false];
        $this->gdpStatus = 503;

        $this->from('/sak')->post('/sak/access', [
            'order_id' => self::ORDER_ID,
            'token' => 'SAK-BAB01-BTKI',
        ])->assertSessionHasErrors([
            'access' => 'Verifikasi pembelian GDP sedang tidak tersedia. Coba kembali beberapa menit lagi.',
        ]);

        $this->assertDatabaseMissing('candidate_users', ['public_id' => self::ORDER_ID]);
    }
}
