<?php

namespace App\Http\Services\Payment;

use App\Models\Donation;
use App\Models\DonationPackage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MaxicardPaymentService implements PaymentServiceInterface
{
    private string $username;
    private string $password;
    private string $baseUrl = 'https://www.maxigame.org/epin/yukle.php';

    public function __construct()
    {
        $this->username = config('donation.providers.maxicard.username');
        $this->password = config('donation.providers.maxicard.password');
    }

    public function getSlug(): string
    {
        return 'maxicard';
    }

    /**
     * Maxicard does not use redirect-based checkout.
     * Use redeemEpin() instead.
     */
    public function createCheckout(DonationPackage $package, Donation $donation): string
    {
        throw new \RuntimeException('Maxicard does not support redirect checkout. Use ePin redemption instead.');
    }

    /**
     * Redeem an ePin code via the Maxicard API.
     *
     * Returns a normalized response array:
     *   ['success' => bool, 'data' => ['total_sales' => float, 'currency' => string, 'order_id' => int], 'message' => string]
     */
    public function redeemEpin(string $epinCode, string $epinSecret, string $playerName, string $ipAddress): array
    {
        $xml = "<APIRequest>
                <params>
                    <username>{$this->username}</username>
                    <password>{$this->password}</password>
                    <cmd>epinadd</cmd>
                    <epinusername>{$playerName}</epinusername>
                    <epincode>{$epinCode}</epincode>
                    <epinpass>{$epinSecret}</epinpass>
                </params>
            </APIRequest>";

        $response = Http::send('post', $this->baseUrl, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cache-Control' => 'no-cache',
            ],
            'form_params' => [
                'data' => urlencode($xml),
            ],
            'timeout' => 20,
        ]);

        if (!$response->successful()) {
            Log::error('Maxicard: HTTP request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Maxicard API request failed.');
        }

        $body = $response->body();

        libxml_use_internal_errors(true);
        $parsed = simplexml_load_string($body);

        if ($parsed === false) {
            Log::error('Maxicard: Failed to parse XML response', ['body' => $body]);
            throw new \RuntimeException('Maxicard API returned an invalid response.');
        }

        $durum = trim((string) ($parsed->params->durum ?? ''));
        $aciklama = trim((string) ($parsed->params->aciklama ?? ''));
        $siparis_no = (int) trim((string) ($parsed->params->siparis_no ?? '0'));
        $tutar = (float) trim((string) ($parsed->params->tutar ?? '0'));

        if ($durum === 'ok' && $siparis_no > 0) {
            return [
                'success' => true,
                'data' => [
                    'total_sales' => $tutar,
                    'currency' => config('donation.currency', 'USD'),
                    'order_id' => $siparis_no,
                ],
                'message' => $aciklama,
            ];
        }

        $englishMessage = match ($durum) {
            'hata' => 'Common error.',
            'bayi_hata' => 'API username or password is incorrect.',
            'bayi_aktif_hata' => 'API user is not active.',
            'hesap_hata' => 'API user not found.',
            'ip_hata' => 'Unauthorized IP address.',
            'kod_hata' => 'Epin code or password is incorrect.',
            'kod_tekrar_hata' => 'Card code has already been used.',
            'fiyat_hata' => 'Amount is uncertain or not registered for the merchant.',
            'komut_hata' => 'API command is missing.',
            'eksik_alan' => 'Required field is missing.',
            'dbhata' => 'System error. Please try again later.',
            default => null,
        };

        return [
            'success' => false,
            'error_code' => $durum,
            'message' => $englishMessage ?? ($aciklama ?: 'ePin redemption failed.'),
        ];
    }

    public function handleWebhook(array $payload, array $headers): ?string
    {
        // Maxicard uses synchronous ePin validation, no webhooks
        return null;
    }

    public function verifyWebhook(string $rawBody, array $headers): bool
    {
        // Maxicard uses synchronous ePin validation, no webhooks
        return false;
    }
}
