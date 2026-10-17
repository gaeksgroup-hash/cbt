<?php

namespace App\Services\CBT;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

class GdpOrderEntitlementVerifier
{
    public function normalizeOrderId(string $orderId): string
    {
        return strtoupper(trim($orderId));
    }

    public function isValidOrderId(string $orderId): bool
    {
        return preg_match('/^GDP-\d{14}-[A-F0-9]{8}$/', $this->normalizeOrderId($orderId)) === 1;
    }

    public function allows(string $orderId, string $entitlementKey): bool
    {
        $orderId = $this->normalizeOrderId($orderId);
        $entitlementKey = strtoupper(trim($entitlementKey));
        $url = trim((string) config('cbt.gdp_entitlements.url'));
        $secret = trim((string) config('cbt.gdp_entitlements.secret'));

        if (! $this->isValidOrderId($orderId) || $entitlementKey === '') {
            return false;
        }

        if ($url === '' || $secret === '') {
            throw new EntitlementVerificationUnavailable('Konfigurasi verifikasi GDP belum lengkap.');
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withToken($secret)
                ->timeout(max(2, (int) config('cbt.gdp_entitlements.timeout_seconds', 8)))
                ->retry(2, 150, throw: false)
                ->post($url, ['orderId' => $orderId]);
        } catch (ConnectionException $exception) {
            throw new EntitlementVerificationUnavailable('GDP tidak dapat dihubungi.', 0, $exception);
        } catch (Throwable $exception) {
            throw new EntitlementVerificationUnavailable('Verifikasi GDP gagal.', 0, $exception);
        }

        if ($response->serverError() || in_array($response->status(), [401, 403, 404, 429], true)) {
            throw new EntitlementVerificationUnavailable('GDP mengembalikan gangguan server.');
        }

        if (! $response->successful() || ! $response->json('ok')) {
            return false;
        }

        $verifiedOrderId = strtoupper((string) $response->json('data.orderId', ''));
        $status = (string) $response->json('data.status', '');
        $keys = array_map(
            static fn (mixed $key): string => strtoupper(trim((string) $key)),
            (array) $response->json('data.entitlementKeys', [])
        );

        return hash_equals($orderId, $verifiedOrderId)
            && $status === 'paid'
            && in_array($entitlementKey, $keys, true);
    }
}
