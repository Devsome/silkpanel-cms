<?php

namespace App\Http\Services\Payment;

use App\Models\Donation;
use App\Models\DonationPackage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalPaymentService implements PaymentServiceInterface
{
    private string $clientId;
    private string $clientSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = config('donation.providers.paypal.client_id');
        $this->clientSecret = config('donation.providers.paypal.client_secret');
        $this->baseUrl = config('donation.providers.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public function getSlug(): string
    {
        return 'paypal';
    }

    public function createCheckout(DonationPackage $package, Donation $donation): string
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => (string) $donation->id,
                        'description' => $package->name,
                        'amount' => [
                            'currency_code' => $donation->currency,
                            'value' => number_format((float) $donation->amount, 2, '.', ''),
                        ],
                    ],
                ],
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            'return_url' => route('donate.success', ['provider' => 'paypal', 'donation' => $donation->id]),
                            'cancel_url' => route('donate.cancel'),
                            'brand_name' => config('app.name'),
                            'user_action' => 'PAY_NOW',
                        ],
                    ],
                ],
            ]);

        if (!$response->successful()) {
            Log::error('PayPal: Failed to create order', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw new \RuntimeException('Failed to create PayPal order.');
        }

        $orderData = $response->json();
        $donation->update([
            'transaction_id' => $orderData['id'],
            'payment_data' => $orderData,
        ]);

        $approveLink = collect($orderData['links'] ?? [])
            ->firstWhere('rel', 'payer-action');

        return $approveLink['href'] ?? throw new \RuntimeException('PayPal approve link not found.');
    }

    public function captureOrder(string $orderId): array
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->withHeader('Content-Type', 'application/json')
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

        if (!$response->successful()) {
            Log::error('PayPal: Failed to capture order', [
                'order_id' => $orderId,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw new \RuntimeException('Failed to capture PayPal order.');
        }

        return $response->json();
    }

    public function handleWebhook(array $payload, array $headers): ?string
    {
        $eventType = $payload['event_type'] ?? '';

        if ($eventType !== 'CHECKOUT.ORDER.APPROVED' && $eventType !== 'PAYMENT.CAPTURE.COMPLETED') {
            return null;
        }

        $resource = $payload['resource'] ?? [];

        if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
            return $resource['id'] ?? null;
        }

        // CHECKOUT.ORDER.APPROVED
        return $resource['id'] ?? null;
    }

    public function verifyWebhook(string $rawBody, array $headers): bool
    {
        // PayPal webhook verification via API
        $accessToken = $this->getAccessToken();

        // For production, you should verify the webhook signature using PayPal's API
        // https://developer.paypal.com/docs/api/webhooks/v1/#verify-webhook-signature_post
        if (config('donation.providers.paypal.mode') === 'sandbox') {
            return true;
        }

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v1/notifications/verify-webhook-signature", [
                'auth_algo' => $headers['paypal-auth-algo'][0] ?? '',
                'cert_url' => $headers['paypal-cert-url'][0] ?? '',
                'transmission_id' => $headers['paypal-transmission-id'][0] ?? '',
                'transmission_sig' => $headers['paypal-transmission-sig'][0] ?? '',
                'transmission_time' => $headers['paypal-transmission-time'][0] ?? '',
                'webhook_id' => config('donation.providers.paypal.webhook_id', ''),
                'webhook_event' => json_decode($rawBody, true),
            ]);

        return $response->successful()
            && ($response->json('verification_status') === 'SUCCESS');
    }

    private function getAccessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            Log::error('PayPal: Failed to get access token', [
                'status' => $response->status(),
            ]);
            throw new \RuntimeException('Failed to authenticate with PayPal.');
        }

        return $response->json('access_token');
    }
}
