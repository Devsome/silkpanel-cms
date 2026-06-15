@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <a href="{{ route('dashboard') }}"
                class="mb-6 inline-flex items-center gap-2 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-muted hover:ag-text-primary transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            <div class="mb-8 ag-card-glow p-6 md:p-8">
                <p class="ag-section-eyebrow">{{ __('donation.select_payment_method') }}</p>
                <h1 class="ag-section-title mt-2">{{ __('donation.title') }}</h1>
            </div>

            @if (session('error'))
                <div class="mb-6 ag-alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if ($providers->isEmpty())
                <div class="ag-card p-8 text-center">
                    <p class="ag-text-muted">{{ __('donation.no_payment_methods') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($providers as $provider)
                        <a href="{{ route('donate.packages', $provider) }}"
                            class="block ag-card p-6 text-center transition-all hover:-translate-y-0.5 hover:shadow-2xl group"
                            style="hover:border-color:rgba(34,211,238,0.5);">
                            <div class="mb-4 h-12 flex items-center justify-center">
                                @if ($provider->slug->value === 'paypal')
                                    <img src="{{ asset('images/payment/paypal-wordmark.svg') }}" alt="PayPal" class="h-10">
                                @elseif ($provider->slug->value === 'stripe')
                                    <img src="{{ asset('images/payment/stripe_wordmark.svg') }}" alt="Stripe" class="h-10">
                                @elseif ($provider->slug->value === 'hipopay' || $provider->slug->value === 'hipocard')
                                    <img src="{{ asset('images/payment/hipopotamya-logo-white.png') }}"
                                        alt="{{ $provider->name }}" class="h-10">
                                @elseif ($provider->slug->value === 'maxicard')
                                    <img src="{{ asset('images/payment/maxigame-logo.png') }}" alt="{{ $provider->name }}"
                                        class="h-10">
                                @elseif ($provider->slug->value === 'fawaterk')
                                    <img src="{{ asset('images/payment/fawaterak-logo.png') }}" alt="{{ $provider->name }}"
                                        class="h-10">
                                @else
                                    <span class="text-lg ag-font-display font-bold uppercase tracking-wider ag-text-primary">
                                        {{ $provider->name }}
                                    </span>
                                @endif
                            </div>

                            <h2 class="text-lg ag-font-display font-bold uppercase tracking-wider ag-text-surface group-hover:ag-text-primary transition-colors">
                                {{ $provider->name }}
                            </h2>
                            @if ($provider->description)
                                <p class="mt-2 text-sm ag-text-muted">{{ $provider->description }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
