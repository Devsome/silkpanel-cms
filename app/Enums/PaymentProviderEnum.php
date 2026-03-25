<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentProviderEnum: string implements HasLabel
{
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';
    case HIPOPAY = 'hipopay';
    case HIPOCARD = 'hipocard';
    case PAYMENTWALL = 'paymentwall';
    case COINPAYMENTS = 'coinpayments';

    public function getLabel(): string
    {
        return match ($this) {
            self::PAYPAL => 'PayPal',
            self::STRIPE => 'Stripe',
            self::HIPOPAY => 'HipoPay',
            self::HIPOCARD => 'HipoCard',
            self::PAYMENTWALL => 'Paymentwall',
            self::COINPAYMENTS => 'CoinPayments',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
