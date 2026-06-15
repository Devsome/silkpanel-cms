@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <div class="mb-6">
                <a href="{{ route('donate.index') }}"
                    class="inline-flex items-center gap-2 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-muted hover:ag-text-primary transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('donation.choose_different_method') }}
                </a>
            </div>

            <div class="mb-8 ag-card-glow p-6 md:p-8">
                <p class="ag-section-eyebrow">{{ __('donation.title') }}</p>
                <h1 class="ag-section-title mt-2">{{ $provider->name }}</h1>
            </div>

            @if (session('error'))
                <div class="mb-6 ag-alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if ($packages->isEmpty())
                <div class="ag-card p-8 text-center">
                    <p class="ag-text-muted">{{ __('donation.no_packages') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($packages as $package)
                        <div class="ag-card flex flex-col overflow-hidden transition-all hover:-translate-y-0.5 hover:shadow-2xl">
                            @if ($package->image)
                                <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}"
                                    class="h-48 w-full object-cover">
                            @endif

                            <div class="p-6 flex flex-1 flex-col">
                                <h2 class="text-lg ag-font-display font-bold uppercase tracking-wider ag-text-surface">
                                    {{ $package->name }}
                                </h2>
                                @if ($package->description)
                                    <p class="mt-2 text-sm ag-text-muted">{{ $package->description }}</p>
                                @endif

                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-2xl ag-font-display font-black ag-text-primary">
                                        {{ number_format($package->silk_amount) }}
                                        <span class="text-sm ag-text-muted font-normal">{{ __('donation.silk') }}</span>
                                    </span>
                                    <span class="text-lg font-bold ag-stat-amber ag-font-mono">
                                        {{ $package->currency }} {{ number_format($package->price, 2) }}
                                    </span>
                                </div>

                                <div class="mt-6">
                                    <form action="{{ route('donate.checkout', $package) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="provider" value="{{ $provider->slug->value }}">
                                        <button type="submit"
                                            class="ag-btn-primary w-full px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-widest">
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
