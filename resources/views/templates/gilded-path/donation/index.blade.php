@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <div class="mb-8 gp-card gp-ornate-border p-6 md:p-8">
                <h1 class="text-3xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('donation.title') }}</h1>
                <p class="mt-2 text-sm gp-text-on-surface-variant">{{ __('donation.select_payment_method') }}</p>
            </div>

            @if (session('error'))
                <div class="mb-6 gp-card p-4" style="border:1px solid rgba(255, 100, 100, 0.5);">
                    <p class="text-sm text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            @if ($providers->isEmpty())
                <div class="gp-card gp-ornate-border p-8 text-center">
                    <p class="gp-text-on-surface-variant">{{ __('donation.no_payment_methods') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($providers as $provider)
                        <a href="{{ route('donate.packages', $provider) }}"
                            class="block gp-card gp-ornate-border p-6 text-center transition-all hover:-translate-y-0.5 hover:shadow-2xl">
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

                            <h2 class="text-lg font-headline font-bold uppercase tracking-wider gp-text-on-surface">
                                {{ $provider->name }}</h2>
                            @if ($provider->description)
                                <p class="mt-2 text-sm gp-text-on-surface-variant">{{ $provider->description }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
