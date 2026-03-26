<?php

namespace App\Http\Services\Payment;

use App\Models\Donation;
use App\Models\DonationPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HipoPayPaymentService implements PaymentServiceInterface
{
    private string $apiKey;
    private string $apiSecret;
    private string $baseUrl = 'https://www.hipopotamya.com/api/v1/merchants';

    public function __construct()
    {
        $this->apiKey = config('donation.providers.hipopay.api_key');
        $this->apiSecret = config('donation.providers.hipopay.api_secret');
    }

    public function getSlug(): string
    {
        return 'hipopay';
    }

    public function createCheckout(DonationPackage $package, Donation $donation): string
    {
        $user = Auth::user();
        $userId = $user->id;
        $userEmail = $user->email;
        $username = $user->name;

        $hash = base64_encode(
            hash_hmac('sha256', $userId . $userEmail . $username . $this->apiKey, $this->apiSecret, true)
        );

        $response = Http::asForm()
            ->withHeaders([
                'User-Agent' => 'Silkpanel/1.0 (https://devso.me)',
            ])
            ->accept('application/json')
            ->timeout(20)
            ->post("{$this->baseUrl}/payment/token", [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'user_id' => $userId,
                'email' => $userEmail,
                'username' => $username,
                'ip_address' => $donation->ip_address,
                'hash' => $hash,
                'pro' => true,
                'product[name]' => mb_substr(trim($package->name), 0, 255) ?: 'Donation',
                'product[price]' => max(100, (int) round((float) $donation->amount * 100)),
                'product[reference_id]' => mb_substr((string) $donation->id, 0, 100),
                'product[commission_type]' => 1,
            ]);

        if (!$response->successful()) {
            Log::error('HipoPay: Failed to create payment session', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            throw new \RuntimeException('Failed to create HipoPay payment session.');
        }

        $data = $response->json();

        $donation->update([
            'transaction_id' => $data['token'] ?? $data['transaction_id'] ?? null,
            'payment_data' => $data,
        ]);

        $redirectUrl = $data['url'] ?? $data['redirect_url'] ?? null;

        if (!$redirectUrl) {
            throw new \RuntimeException('HipoPay redirect URL not found in response.');
        }

        return $redirectUrl;
    }

    public function redeemEpin(string $epinCode, string $epinSecret, string $playerName, string $ipAddress): array
    {
        throw new \RuntimeException('HipoPay does not support ePin redemption.');
    }

    public function handleWebhook(array $payload, array $headers): ?string
    {
        $status = $payload['status'] ?? '';

        if ($status !== 'completed' && $status !== 'COMPLETED') {
            return null;
        }

        return $payload['transaction_id'] ?? null;
    }

    public function verifyWebhook(string $rawBody, array $headers): bool
    {
        $data = json_decode($rawBody, true);

        if (!is_array($data) || !isset($data['hash'], $data['transaction_id'], $data['user_id'], $data['email'], $data['name'], $data['status'])) {
            return false;
        }

        $expectedHash = base64_encode(
            hash_hmac(
                'sha256',
                $data['transaction_id'] . $data['user_id'] . $data['email'] . $data['name'] . $data['status'] . $this->apiKey,
                $this->apiSecret,
                true
            )
        );

        return hash_equals($expectedHash, $data['hash']);
    }
}
