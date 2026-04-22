<?php

namespace Database\Seeders;

use App\Models\PaymentProvider;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'slug' => 'paypal',
                'name' => 'PayPal',
                'description' => 'Pay securely with your PayPal account.',
                'is_active' => false,
                'sort_order' => 1,
            ],
            [
                'slug' => 'stripe',
                'name' => 'Stripe',
                'description' => 'Pay with credit or debit card.',
                'is_active' => false,
                'sort_order' => 2,
            ],
            [
                'slug' => 'hipopay',
                'name' => 'HipoPay',
                'description' => 'Pay with HipoPay.',
                'is_active' => false,
                'sort_order' => 3,
            ],
            [
                'slug' => 'hipocard',
                'name' => 'HipoCard',
                'description' => 'Pay with HipoCard ePin.',
                'is_active' => false,
                'sort_order' => 4,
            ],
            [
                'slug' => 'maxicard',
                'name' => 'MaxiCard',
                'description' => 'Pay with MaxiCard ePin.',
                'is_active' => false,
                'sort_order' => 5,
            ],
            [
                'slug' => 'fawaterk',
                'name' => 'Fawaterk',
                'description' => 'Pay with Fawaterk.',
                'is_active' => false,
                'sort_order' => 6,
            ]
        ];

        if (PaymentProvider::count() === 0) {
            foreach ($providers as $provider) {
                PaymentProvider::updateOrCreate(
                    ['slug' => $provider['slug']],
                    $provider,
                );
            }
        } else {
            $this->command->info('Payment providers already seeded, skipping.');
        }
    }
}
