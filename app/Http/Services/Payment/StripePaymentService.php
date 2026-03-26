<?php

namespace App\Http\Services\Payment;

use App\Models\Donation;
use App\Models\DonationPackage;
use Illuminate\Support\Facades\Log;

class StripePaymentService implements PaymentServiceInterface
{
    private string $secretKey;
    private string $webhookSecret;

    public function __construct()
    {
        $this->secretKey = config('donation.providers.stripe.secret');
        $this->webhookSecret = config('donation.providers.stripe.webhook_secret');
    }

    public function getSlug(): string
    {
        return 'stripe';
    }

    public function createCheckout(DonationPackage $package, Donation $donation): string
    {
        \Stripe\Stripe::setApiKey($this->secretKey);

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower($donation->currency),
                        'product_data' => [
                            'name' => $package->name,
                            'description' => $package->description ?: "{$package->silk_amount} Silk",
                        ],
                        'unit_amount' => (int) round((float) $donation->amount * 100),
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('donate.success', ['provider' => 'stripe', 'donation' => $donation->id]),
            'cancel_url' => route('donate.cancel'),
            'metadata' => [
                'donation_id' => $donation->id,
            ],
            'client_reference_id' => (string) $donation->id,
        ]);

        $donation->update([
            'transaction_id' => $session->id,
            'payment_data' => ['session_id' => $session->id],
        ]);

        return $session->url;
    }

    public function redeemEpin(string $epinCode, string $epinSecret, string $playerName, string $ipAddress): array
    {
        throw new \RuntimeException('Stripe does not support ePin redemption.');
    }

    public function handleWebhook(array $payload, array $headers): ?string
    {
        $type = $payload['type'] ?? '';

        if ($type !== 'checkout.session.completed') {
            return null;
        }

        $session = $payload['data']['object'] ?? [];

        if (($session['payment_status'] ?? '') !== 'paid') {
            return null;
        }

        return $session['id'] ?? null;
    }

    public function verifyWebhook(string $rawBody, array $headers): bool
    {
        $sigHeader = $headers['stripe-signature'][0] ?? ($headers['Stripe-Signature'][0] ?? '');

        if (empty($sigHeader) || empty($this->webhookSecret)) {
            return false;
        }

        try {
            \Stripe\Webhook::constructEvent($rawBody, $sigHeader, $this->webhookSecret);
            return true;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe: Webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
