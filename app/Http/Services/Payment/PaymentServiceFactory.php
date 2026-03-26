<?php

namespace App\Http\Services\Payment;

use App\Enums\PaymentProviderEnum;

class PaymentServiceFactory
{
    public static function make(string|PaymentProviderEnum $provider): PaymentServiceInterface
    {
        $slug = $provider instanceof PaymentProviderEnum ? $provider->value : $provider;

        return match ($slug) {
            'paypal' => new PayPalPaymentService(),
            'stripe' => new StripePaymentService(),
            'hipopay' => new HipoPayPaymentService(),
            'hipocard' => new HipoCardPaymentService(),
            'maxicard' => new MaxicardPaymentService(),
            default => throw new \InvalidArgumentException("Unsupported payment provider: {$slug}"),
        };
    }
}
