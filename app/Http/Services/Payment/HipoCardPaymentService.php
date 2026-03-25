<?php

namespace App\Http\Services\Payment;

use App\Models\Donation;
use App\Models\DonationPackage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HipoCardPaymentService implements PaymentServiceInterface
{
    private string $apiKey;
    private string $apiSecret;
    private string $baseUrl = 'https://www.hipopotamya.com/api/v1/hipocard/epins';
    private string $baseUrlSandbox = 'https://www.hipopotamya.com/api/sandbox/v1/hipocard/epins';

    public function __construct()
    {
        $this->apiKey = config('donation.providers.hipocard.api_key');
        $this->apiSecret = config('donation.providers.hipocard.api_secret');
    }

    public function getSlug(): string
    {
        return 'hipocard';
    }

    /**
     * HipoCard does not use redirect-based checkout.
     * Use redeemEpin() instead.
     */
    public function createCheckout(DonationPackage $package, Donation $donation): string
    {
        throw new \RuntimeException('HipoCard does not support redirect checkout. Use ePin redemption instead.');
    }

    /**
     * Redeem an ePin code via the HipoCard API.
     */
    public function redeemEpin(string $epinCode, string $epinSecret, string $playerName, string $ipAddress): array
    {
        $response = Http::asForm()
            ->withHeaders([
                'User-Agent' => 'Silkpanel/1.0 (https://devso.me)',
            ])
            ->accept('application/json')
            ->timeout(20)
            ->withHeaders([
                'api-key' => $this->apiKey,
                'api-secret' => $this->apiSecret,
            ])
            ->post($this->baseUrl, [
                'epin_code' => $epinCode,
                'epin_secret' => $epinSecret,
                'player_name' => $playerName,
                'used_ip' => $ipAddress,
            ]);

        if (!$response->successful()) {
            Log::error('HipoCard: API request failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw new \RuntimeException('HipoCard API request failed.');
        }

        return $response->json();
    }

    public function handleWebhook(array $payload, array $headers): ?string
    {
        // HipoCard uses synchronous ePin validation, no webhooks
        return null;
    }

    public function verifyWebhook(string $rawBody, array $headers): bool
    {
        // HipoCard uses synchronous ePin validation, no webhooks
        return false;
    }
}
