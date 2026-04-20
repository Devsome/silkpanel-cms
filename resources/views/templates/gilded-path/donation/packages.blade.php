@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <div class="mb-6">
                <a href="{{ route('donate.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-headline font-bold uppercase tracking-widest gp-text-on-surface-variant transition-colors hover:gp-text-primary">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('donation.choose_different_method') }}
                </a>
            </div>

            <div class="mb-8 gp-card gp-ornate-border p-6 md:p-8">
                <h1 class="text-3xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('donation.title') }} - {{ $provider->name }}</h1>
            </div>

            @if (session('error'))
                <div class="mb-6 gp-card p-4" style="border:1px solid rgba(255, 100, 100, 0.5);">
                    <p class="text-sm text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            @if ($packages->isEmpty())
                <div class="gp-card gp-ornate-border p-8 text-center">
                    <p class="gp-text-on-surface-variant">{{ __('donation.no_packages') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($packages as $package)
                        <div class="gp-card gp-ornate-border flex flex-col overflow-hidden">
                            @if ($package->image)
                                <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}"
                                    class="h-48 w-full object-cover">
                            @endif

                            <div class="p-6 flex flex-1 flex-col">
                                <h2 class="text-lg font-headline font-bold uppercase tracking-wider gp-text-on-surface">
                                    {{ $package->name }}</h2>
                                @if ($package->description)
                                    <p class="mt-2 text-sm gp-text-on-surface-variant">{{ $package->description }}</p>
                                @endif

                                <div class="mt-4 flex items-center justify-between">
                                    <span
                                        class="text-2xl font-headline font-black gp-text-primary">{{ number_format($package->silk_amount) }}
                                        {{ __('donation.silk') }}</span>
                                    <span class="text-lg font-bold gp-text-on-surface">{{ $package->currency }}
                                        {{ number_format($package->price, 2) }}</span>
                                </div>

                                <div class="mt-6">
                                    <form action="{{ route('donate.checkout', $package) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="provider" value="{{ $provider->slug->value }}">
                                        <button type="submit"
                                            class="w-full px-4 py-2 text-xs font-headline font-bold uppercase tracking-widest gp-gold-btn">
                                            {{ __('donation.buy_now') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
