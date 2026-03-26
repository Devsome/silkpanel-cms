<?php

namespace App\Http\Controllers;

use App\Enums\DonationStatusEnum;
use App\Helpers\SilkHelper;
use App\Http\Services\Payment\PaymentServiceFactory;
use App\Http\Services\Payment\PayPalPaymentService;
use App\Models\Donation;
use App\Models\DonationPackage;
use App\Models\PaymentProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
    public function index()
    {
        $providers = PaymentProvider::active()
            ->orderBy('sort_order')
            ->get();

        return view('donation.index', compact('providers'));
    }

    public function packages(PaymentProvider $provider)
    {
        abort_unless($provider->is_active, 404);

        // ePin providers have no packages — redirect directly to ePin form
        if (in_array($provider->slug->value, ['hipocard', 'maxicard'])) {
            return redirect()->route('donate.redeem-epin.show', $provider);
        }

        $packages = $provider->donationPackages()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('donation.packages', compact('provider', 'packages'));
    }

    public function checkout(Request $request, DonationPackage $package)
    {
        $validated = $request->validate([
            'provider' => ['required', 'string', 'exists:payment_providers,slug'],
        ]);

        $provider = PaymentProvider::where('slug', $validated['provider'])
            ->active()
            ->firstOrFail();

        if (!$package->is_active) {
            return back()->with('error', 'This package is not available.');
        }

        // Verify the package is assigned to this provider
        if (!$provider->donationPackages()->where('donation_packages.id', $package->id)->exists()) {
            return back()->with('error', 'This package is not available for the selected payment provider.');
        }

        $user = Auth::user();

        $donation = Donation::create([
            'user_id' => $user->id,
            'donation_package_id' => $package->id,
            'payment_provider_slug' => $provider->slug->value,
            'amount' => $package->price,
            'currency' => $package->currency,
            'silk_amount' => $package->silk_amount,
            'silk_type' => $package->silk_type,
            'status' => DonationStatusEnum::PENDING,
            'ip_address' => $request->ip(),
        ]);

        try {
            $service = PaymentServiceFactory::make($provider->slug->value);
            $redirectUrl = $service->createCheckout($package, $donation);

            return redirect()->away($redirectUrl);
        } catch (\Throwable $e) {
            Log::error('Donation: Checkout failed', [
                'donation_id' => $donation->id,
                'provider' => $provider->slug->value,
                'error' => $e->getMessage(),
            ]);

            $donation->update(['status' => DonationStatusEnum::FAILED]);

            return back()->with('error', 'Payment provider error. Please try again later.');
        }
    }

    public function showRedeemEpin(PaymentProvider $provider)
    {
        abort_unless($provider->is_active, 404);
        abort_unless(in_array($provider->slug->value, ['hipocard', 'maxicard']), 404);

        return view('donation.redeem-epin', compact('provider'));
    }

    public function redeemEpin(Request $request, PaymentProvider $provider)
    {
        abort_unless($provider->is_active, 404);
        abort_unless(in_array($provider->slug->value, ['hipocard', 'maxicard']), 404);

        $validated = $request->validate([
            'epin_code' => ['required', 'string', 'max:255'],
            'epin_secret' => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $providerSlug = $provider->slug->value;
        $silkPerUnit = (int) config("donation.providers.{$providerSlug}.silk_per_unit", 100);
        $defaultSilkType = match (config('silkpanel.version')) {
            'isro' => 'silk_own',
            default => 'silk',
        };

        $donation = Donation::create([
            'user_id' => $user->id,
            'donation_package_id' => null,
            'payment_provider_slug' => $providerSlug,
            'amount' => 0,
            'currency' => config('donation.currency', 'USD'),
            'silk_amount' => 0,
            'silk_type' => $defaultSilkType,
            'status' => DonationStatusEnum::PENDING,
            'ip_address' => $request->ip(),
        ]);

        try {
            $service = PaymentServiceFactory::make($providerSlug);
            $result = $service->redeemEpin(
                epinCode: $validated['epin_code'],
                epinSecret: $validated['epin_secret'],
                playerName: $user->name,
                ipAddress: $request->ip(),
            );

            if (isset($result['success']) && $result['success'] === true) {
                $data = $result['data'] ?? [];
                $cardValue = (float) ($data['total_sales'] ?? 0);
                $cardCurrency = $data['currency'] ?? config('donation.currency', 'USD');

                $denominationSilks = $provider->denomination_silks ?? [];
                $silkAmount = isset($denominationSilks[(string) (int) $cardValue])
                    ? (int) $denominationSilks[(string) (int) $cardValue]
                    : (int) ($cardValue * $silkPerUnit);

                $donation->update([
                    'status' => DonationStatusEnum::COMPLETED,
                    'transaction_id' => $validated['epin_code'],
                    'amount' => $cardValue,
                    'currency' => $cardCurrency,
                    'silk_amount' => $silkAmount,
                    'payment_data' => $result,
                    'completed_at' => now(),
                ]);

                if ($user && $user->jid) {
                    $jidPjid = null;
                    match (config('silkpanel.version')) {
                        'isro' => $jidPjid = $user->pjid,
                        default => $jidPjid = $user->jid,
                    };
                    SilkHelper::addSilk(
                        jid: $jidPjid,
                        amount: $silkAmount,
                        type: $donation->silk_type,
                        ip: $donation->ip_address,
                    );
                }

                return redirect()->route('donate.success', [
                    'provider' => $providerSlug,
                    'donation' => $donation->id,
                ]);
            }

            $donation->update([
                'status' => DonationStatusEnum::FAILED,
                'payment_data' => $result,
            ]);

            $errorMessage = $result['message'] ?? 'ePin redemption failed.';

            return back()->with('error', $errorMessage)->withInput();
        } catch (\Throwable $e) {
            Log::error('Donation: ePin redemption failed', [
                'provider' => $providerSlug,
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);

            $donation->update(['status' => DonationStatusEnum::FAILED]);

            return back()->with('error', 'Payment provider error. Please try again later.')->withInput();
        }
    }

    public function success(Request $request)
    {
        $provider = $request->query('provider');
        $donationId = $request->query('donation');

        $donation = Donation::where('id', $donationId)
            ->where('user_id', Auth::id())
            ->first();

        // For PayPal, we need to capture the order on return
        if ($provider === 'paypal' && $donation?->isPending() && $donation->transaction_id) {
            try {
                $paypalService = new PayPalPaymentService();
                $captureResult = $paypalService->captureOrder($donation->transaction_id);

                $captureStatus = $captureResult['status'] ?? '';
                if ($captureStatus === 'COMPLETED') {
                    $donation->update([
                        'status' => DonationStatusEnum::COMPLETED,
                        'payment_data' => $captureResult,
                        'completed_at' => now(),
                    ]);

                    $user = $donation->user;
                    if ($user && $user->jid) {
                        $jidPjid = null;
                        match (config('silkpanel.version')) {
                            'isro' => $jidPjid = $user->pjid,
                            default => $jidPjid = $user->jid,
                        };
                        SilkHelper::addSilk(
                            jid: $jidPjid,
                            amount: $donation->silk_amount,
                            type: $donation->silk_type,
                            ip: $donation->ip_address,
                        );
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Donation: PayPal capture failed on return', [
                    'donation_id' => $donation->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('donation.success', compact('donation'));
    }

    public function cancel()
    {
        return view('donation.cancel');
    }
}
