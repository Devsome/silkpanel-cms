@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-3xl px-4 md:px-8">
            <div class="gp-card gp-ornate-border p-8 text-center">
                <svg class="mx-auto h-16 w-16 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>

                <h1 class="mt-4 text-2xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('donation.success_title') }}</h1>

                @if ($donation && $donation->isCompleted())
                    <p class="mt-3 gp-text-on-surface-variant">
                        {{ __('donation.success_silk_added', ['amount' => number_format($donation->silk_amount)]) }}</p>
                @else
                    <p class="mt-3 gp-text-on-surface-variant">{{ __('donation.success_processing') }}</p>
                @endif

                <div class="mt-6">
                    <a href="{{ route('donate.index') }}"
                        class="inline-flex items-center px-4 py-2 text-xs font-headline font-bold uppercase tracking-widest gp-gold-btn">
                        {{ __('donation.back_to_donations') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
