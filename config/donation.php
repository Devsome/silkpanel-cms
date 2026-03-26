<?php

return [
    'currency' => env('DONATION_CURRENCY', 'USD'),

    'providers' => [
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
            'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
        ],

        'stripe' => [
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],

        'hipopay' => [
            'api_key' => env('HIPOPAY_API_KEY'),
            'api_secret' => env('HIPOPAY_API_SECRET'),
        ],

        'hipocard' => [
            'api_key' => env('HIPOCARD_API_KEY'),
            'api_secret' => env('HIPOCARD_API_SECRET'),
            'silk_per_unit' => env('HIPOCARD_SILK_PER_UNIT', 100),
        ],

        'maxicard' => [
            'username' => env('MAXICARD_USERNAME'),
            'password' => env('MAXICARD_PASSWORD'),
            'silk_per_unit' => env('MAXICARD_SILK_PER_UNIT', 100),
        ],

        'paymentwall' => [
            'project_key' => env('PAYMENTWALL_PROJECT_KEY'),
            'secret_key' => env('PAYMENTWALL_SECRET_KEY'),
        ],

        'coinpayments' => [
            'merchant_id' => env('COINPAYMENTS_MERCHANT_ID'),
            'ipn_secret' => env('COINPAYMENTS_IPN_SECRET'),
            'public_key' => env('COINPAYMENTS_PUBLIC_KEY'),
            'private_key' => env('COINPAYMENTS_PRIVATE_KEY'),
        ],
    ],
];
