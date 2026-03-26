<?php

namespace App\Http\Services\Payment;

use App\Models\Donation;
use App\Models\DonationPackage;

interface PaymentServiceInterface
{
    /**
     * Create a checkout session / order and return a redirect URL.
     */
    public function createCheckout(DonationPackage $package, Donation $donation): string;

    /**
     * Handle the incoming webhook payload.
     * Returns the transaction ID on success, null on failure.
     */
    public function handleWebhook(array $payload, array $headers): ?string;

    /**
     * Verify that the webhook signature is valid.
     */
    public function verifyWebhook(string $rawBody, array $headers): bool;

    /**
     * Redeem an ePin code and return a normalized result array.
     * ['success' => bool, 'data' => ['total_sales' => float, 'currency' => string, ...], 'message' => string]
     */
    public function redeemEpin(string $epinCode, string $epinSecret, string $playerName, string $ipAddress): array;

    /**
     * Get the provider slug.
     */
    public function getSlug(): string;
}
