@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-8">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-1">
                    {{ __('donation.section_label') }}</p>
                <h1 class="text-3xl font-black uppercase tracking-widest text-white">{{ __('donation.title') }}</h1>
                <div class="mt-3 h-px bg-linear-to-r from-violet-500/40 to-transparent"></div>
            </div>

            @if (session('error'))
                <div class="mb-6 p-4 border border-red-500/40 bg-red-500/10 text-red-300 text-sm font-mono">
                    {{ session('error') }}
                </div>
            @endif

            @if ($providers->isEmpty())
                <div class="bg-zinc-900 border border-zinc-800 p-10 text-center">
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">
                        {{ __('donation.no_payment_methods') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($providers as $provider)
                        <a href="{{ route('donate.packages', $provider) }}"
                            class="group bg-zinc-900 border border-zinc-800 hover:border-violet-500/40 p-6 flex flex-col items-center text-center transition hover:shadow-[0_0_20px_rgba(139,92,246,0.1)]">

                            <div class="mb-5 h-14 flex items-center justify-center">
                                @if ($provider->slug->value === 'paypal')
                                    <img src="{{ asset('images/payment/paypal-wordmark.svg') }}" alt="PayPal"
                                        class="h-10 invert brightness-75 group-hover:brightness-100 transition">
                                @elseif ($provider->slug->value === 'stripe')
                                    <img src="{{ asset('images/payment/stripe_wordmark.svg') }}" alt="Stripe"
                                        class="h-10 invert brightness-75 group-hover:brightness-100 transition">
                                @elseif (in_array($provider->slug->value, ['hipopay', 'hipocard']))
                                    <img src="{{ asset('images/payment/hipopotamya-logo-white.png') }}"
                                        alt="{{ $provider->name }}"
                                        class="h-10 brightness-75 group-hover:brightness-100 transition">
                                @elseif ($provider->slug->value === 'maxicard')
                                    <img src="{{ asset('images/payment/maxigame-logo.png') }}" alt="{{ $provider->name }}"
                                        class="h-10 brightness-75 group-hover:brightness-100 transition">
                                @elseif ($provider->slug->value === 'fawaterk')
                                    <img src="{{ asset('images/payment/fawaterak-logo.png') }}"
                                        alt="{{ $provider->name }}"
                                        class="h-10 brightness-75 group-hover:brightness-100 transition">
                                @else
                                    <span
                                        class="flex h-12 w-12 items-center justify-center border border-violet-500/30 text-violet-500 group-hover:border-violet-500/70 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </span>
                                @endif
                            </div>

                            <h2
                                class="text-base font-bold uppercase tracking-widest text-white group-hover:text-violet-300 transition">
                                {{ $provider->name }}
                            </h2>
                            @if ($provider->description)
                                <p class="mt-2 text-xs text-zinc-500 leading-relaxed">{{ $provider->description }}</p>
                            @endif

                            <div
                                class="mt-4 flex items-center gap-1.5 text-xs font-mono uppercase tracking-wider text-violet-400/60 group-hover:text-violet-400 transition">
                                {{ __('donation.select') }} →
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
