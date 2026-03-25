<?php

namespace App\Http\Controllers;

use App\Enums\DonationStatusEnum;
use App\Helpers\SilkHelper;
use App\Http\Services\Payment\PaymentServiceFactory;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handlePayPal(Request $request)
    {
        return $this->processWebhook('paypal', $request);
    }

    public function handleStripe(Request $request)
    {
        return $this->processWebhook('stripe', $request);
    }

    public function handleHipoPay(Request $request)
    {
        return $this->processWebhook('hipopay', $request);
    }

    private function processWebhook(string $provider, Request $request)
    {
        $service = PaymentServiceFactory::make($provider);
        $rawBody = $request->getContent();
        $headers = $request->headers->all();

        if (!$service->verifyWebhook($rawBody, $headers)) {
            Log::warning("Webhook: Invalid signature for {$provider}");
            return response('Invalid signature', 403);
        }

        $payload = json_decode($rawBody, true);
        if (!is_array($payload)) {
            return response('Invalid payload', 400);
        }

        $transactionId = $service->handleWebhook($payload, $headers);
        if (!$transactionId) {
            // Event type not relevant, acknowledge it
            return response('OK', 200);
        }

        $donation = Donation::where('transaction_id', $transactionId)
            ->where('payment_provider_slug', $provider)
            ->first();

        if (!$donation) {
            Log::warning("Webhook: Donation not found for transaction {$transactionId} ({$provider})");
            return response('Donation not found', 404);
        }

        if ($donation->isCompleted()) {
            return response('Already processed', 200);
        }

        $this->completeDonation($donation, $payload);

        return response('OK', 200);
    }

    private function completeDonation(Donation $donation, array $payload): void
    {
        $donation->update([
            'status' => DonationStatusEnum::COMPLETED,
            'payment_data' => $payload,
            'completed_at' => now(),
        ]);

        $user = $donation->user;
        if ($user && $user->jid) {
            SilkHelper::addSilk(
                jid: $user->jid,
                amount: $donation->silk_amount,
                type: $donation->silk_type,
                ip: $donation->ip_address,
            );
        }

        Log::info("Webhook: Donation #{$donation->id} completed", [
            'user_id' => $donation->user_id,
            'silk_amount' => $donation->silk_amount,
            'provider' => $donation->payment_provider_slug,
        ]);
    }
}
