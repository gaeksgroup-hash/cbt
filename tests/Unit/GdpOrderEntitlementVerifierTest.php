<?php

namespace Tests\Unit;

use App\Services\CBT\EntitlementVerificationUnavailable;
use App\Services\CBT\GdpOrderEntitlementVerifier;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GdpOrderEntitlementVerifierTest extends TestCase
{
    private const ORDER_ID = 'GDP-20261017123045-A1B2C3D4';

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('cbt.gdp_entitlements.url', 'https://gdp.test/api/cbt-entitlements.php');
        config()->set('cbt.gdp_entitlements.secret', 'test-secret');
    }

    public function test_it_accepts_only_a_paid_order_with_the_matching_product_key(): void
    {
        Http::fake(Http::response([
            'ok' => true,
            'data' => [
                'orderId' => self::ORDER_ID,
                'status' => 'paid',
                'entitlementKeys' => ['SAK', 'OTHER'],
            ],
        ]));

        $this->assertTrue(app(GdpOrderEntitlementVerifier::class)->allows(self::ORDER_ID, 'sak'));
    }

    public function test_it_rejects_refunded_mismatched_and_malformed_orders(): void
    {
        Http::fake(Http::response([
            'ok' => true,
            'data' => [
                'orderId' => self::ORDER_ID,
                'status' => 'refunded',
                'entitlementKeys' => ['SAK'],
            ],
        ]));

        $verifier = app(GdpOrderEntitlementVerifier::class);
        $this->assertFalse($verifier->allows(self::ORDER_ID, 'SAK'));
        $this->assertFalse($verifier->allows('NOT-AN-ORDER', 'SAK'));
    }

    public function test_it_fails_closed_when_the_shared_secret_is_missing(): void
    {
        config()->set('cbt.gdp_entitlements.secret', '');

        $this->expectException(EntitlementVerificationUnavailable::class);
        app(GdpOrderEntitlementVerifier::class)->allows(self::ORDER_ID, 'SAK');
    }

    public function test_it_reports_bridge_authentication_failure_as_unavailable(): void
    {
        Http::fake(Http::response(['ok' => false], 401));

        $this->expectException(EntitlementVerificationUnavailable::class);
        app(GdpOrderEntitlementVerifier::class)->allows(self::ORDER_ID, 'SAK');
    }
}
