@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition mb-6">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8 mb-8">
                <h1 class="text-3xl font-bold text-white uppercase tracking-widest">{{ __('donation.title') }}</h1>
                <p class="mt-2 text-sm text-gray-400">{{ __('donation.select_payment_method') }}</p>
            </div>

            @if (session('error'))
                <div class="mb-6 rounded-xl border border-red-800/40 bg-red-900/20 p-4">
                    <p class="text-sm text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            @if ($providers->isEmpty())
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-8 text-center">
                    <p class="text-gray-500">{{ __('donation.no_payment_methods') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($providers as $provider)
                        <a href="{{ route('donate.packages', $provider) }}"
                            class="group block rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 text-center transition-all hover:-translate-y-0.5 hover:border-emerald-500/30 hover:shadow-lg hover:shadow-emerald-900/20">
                            <div class="mb-4 h-12 flex items-center justify-center">
                                @if ($provider->slug->value === 'paypal')
                                    <img src="{{ asset('images/payment/paypal-wordmark.svg') }}" alt="PayPal"
                                        class="h-10">
                                @elseif($provider->slug->value === 'stripe')
                                    <img src="{{ asset('images/payment/stripe_wordmark.svg') }}" alt="Stripe"
                                        class="h-10">
                                @elseif($provider->slug->value === 'hipopay' || $provider->slug->value === 'hipocard')
                                    <img src="{{ asset('images/payment/hipopotamya-logo-white.png') }}"
                                        alt="{{ $provider->name }}" class="h-10">
                                @elseif($provider->slug->value === 'maxicard')
                                    <img src="{{ asset('images/payment/maxigame-logo.png') }}" alt="{{ $provider->name }}"
                                        class="h-10">
                                @elseif($provider->slug->value === 'fawaterk')
                                    <img src="{{ asset('images/payment/fawaterak-logo.png') }}"
                                        alt="{{ $provider->name }}" class="h-10">
                                @endif
                            </div>

                            <h2
                                class="text-base font-bold text-white uppercase tracking-wider group-hover:text-emerald-300 transition">
                                {{ $provider->name }}
                            </h2>
                            @if ($provider->description)
                                <p class="mt-2 text-sm text-gray-400">{{ $provider->description }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>

                @if (Route::has('donate.redeem-epin'))
                    <div class="mt-8 text-center">
                        <a href="{{ route('donate.redeem-epin') }}"
                            class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            {{ __('donation.redeem_epin') }}
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
