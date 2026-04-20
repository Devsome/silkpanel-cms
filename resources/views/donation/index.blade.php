<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('donation.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-6 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <p class="mb-6 text-gray-600 dark:text-gray-400">
                {{ __('donation.select_payment_method') }}
            </p>

            @if ($providers->isEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('donation.no_payment_methods') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($providers as $provider)
                        <a href="{{ route('donate.packages', $provider) }}"
                            class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:ring-2 hover:ring-purple-500 transition">
                            <div class="p-6 text-center">
                                <div class="mb-4">
                                    @if ($provider->slug->value === 'paypal')
                                        <img src="{{ asset('images/payment/paypal-wordmark.svg') }}" alt="PayPal"
                                            class="h-10 mx-auto dark:invert">
                                    @elseif($provider->slug->value === 'stripe')
                                        <img src="{{ asset('images/payment/stripe_wordmark.svg') }}" alt="Stripe"
                                            class="h-10 mx-auto dark:invert">
                                    @elseif($provider->slug->value === 'hipopay' || $provider->slug->value === 'hipocard')
                                        <img src="{{ asset('images/payment/hipopotamya-logo-pure.png') }}"
                                            alt="{{ $provider->name }}" class="h-10 mx-auto dark:hidden">
                                        <img src="{{ asset('images/payment/hipopotamya-logo-white.png') }}"
                                            alt="{{ $provider->name }}" class="h-10 mx-auto hidden dark:block">
                                    @elseif($provider->slug->value === 'maxicard')
                                        <img src="{{ asset('images/payment/maxigame-logo.png') }}"
                                            alt="{{ $provider->name }}" class="h-10 mx-auto">
                                    @elseif($provider->slug->value === 'fawaterk')
                                        <img src="{{ asset('images/payment/fawaterak-logo.png') }}"
                                            alt="{{ $provider->name }}" class="h-10 mx-auto">
                                    @else
                                        <span class="inline-block text-4xl text-gray-500"></span>
                                    @endif
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $provider->name }}
                                </h3>

                                @if ($provider->description)
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $provider->description }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
