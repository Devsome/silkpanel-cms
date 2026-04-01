<?php

namespace App\Http\Services\Payment;

use App\Models\Donation;
use App\Models\DonationPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FawaterkPaymentService implements PaymentServiceInterface
{
    private string $apiKey;
    private string $vendorKey;
    private string $endpoint;
    private string $currency;

    public function __construct()
    {
        $config = config('donation.providers.fawaterk');
        $this->apiKey = $config['api_key'] ?? '';
        $this->vendorKey = $config['vendor_key'] ?? '';
        $this->endpoint = rtrim($config['endpoint'] ?? 'https://app.fawaterk.com', '/');
        $this->currency = config('donation.currency', 'USD');
    }

    public function getSlug(): string
    {
        return 'fawaterk';
    }

    public function createCheckout(DonationPackage $package, Donation $donation): string
    {
        $user = Auth::user();

        $invoiceData = [
            'cartTotal' => (string) $package->price,
            'currency' => $this->currency,
            'customer' => [
                'first_name' => $user->name ?? 'Customer',
                'last_name' => $user->name ?? 'Customer',
                'email' => $user->email,
            ],
            'redirectionUrls' => [
                'successUrl' => route('donate.success'),
                'failUrl' => route('donate.cancel'),
                'pendingUrl' => route('donate.cancel'),
            ],
            'cartItems' => [
                [
                    'name' => mb_substr(trim($package->name), 0, 255) ?: 'Donation',
                    'price' => (string) $package->price,
                    'quantity' => 1,
                ],
            ],
            'payLoad' => [
                'donation_id' => $donation->id,
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->timeout(20)->post("{$this->endpoint}/api/v2/createInvoiceLink", $invoiceData);

        if (!$response->successful()) {
            Log::error('Fawaterk: Failed to create invoice', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw new \RuntimeException('Failed to create Fawaterk payment session.');
        }

        $data = $response->json();

        $paymentUrl = $data['data']['url'] ?? null;
        $invoiceId = $data['data']['invoiceId'] ?? null;

        if (!$paymentUrl) {
            Log::error('Fawaterk: No payment URL in response', ['data' => $data]);
            throw new \RuntimeException('Fawaterk payment URL not found in response.');
        }

        $donation->update([
            'transaction_id' => (string) $invoiceId,
            'payment_data' => $data,
        ]);

        return $paymentUrl;
    }

    public function handleWebhook(array $payload, array $headers): ?string
    {
        $status = $payload['invoice_status'] ?? '';

        if (strtolower($status) !== 'paid') {
            return null;
        }

        $invoiceId = $payload['invoice_id'] ?? null;

        return $invoiceId ? (string) $invoiceId : null;
    }

    public function verifyWebhook(string $rawBody, array $headers): bool
    {
        $data = json_decode($rawBody, true);

        if (!is_array($data)) {
            return false;
        }

        $hashKey = $data['hashKey'] ?? null;
        $invoiceId = $data['invoice_id'] ?? null;
        $invoiceKey = $data['invoice_key'] ?? null;
        $paymentMethod = $data['payment_method'] ?? null;

        if (!$hashKey || !$invoiceId || !$invoiceKey || !$paymentMethod) {
            return false;
        }

        $queryParam = "InvoiceId={$invoiceId}&InvoiceKey={$invoiceKey}&PaymentMethod={$paymentMethod}";
        $expectedHash = hash_hmac('sha256', $queryParam, $this->vendorKey, false);

        return hash_equals($expectedHash, $hashKey);
    }

    public function redeemEpin(string $epinCode, string $epinSecret, string $playerName, string $ipAddress): array
    {
        throw new \RuntimeException('Fawaterk does not support ePin redemption.');
    }
}
