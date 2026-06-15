@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-3xl px-4 md:px-8">
            <div class="ag-card-glow p-8 text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full"
                    style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.4);">
                    <svg class="h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>

                <h1 class="mt-4 text-2xl ag-font-display font-black uppercase tracking-widest ag-text-primary">
                    {{ __('donation.success_title') }}
                </h1>

                @if ($donation && $donation->isCompleted())
                    <p class="mt-3 ag-text-muted">
                        {{ __('donation.success_silk_added', ['amount' => number_format($donation->silk_amount)]) }}
                    </p>
                @else
                    <p class="mt-3 ag-text-muted">{{ __('donation.success_processing') }}</p>
                @endif

                <div class="mt-6">
                    <a href="{{ route('dashboard') }}"
                        class="ag-btn-primary inline-flex items-center px-6 py-2 text-xs ag-font-display font-bold uppercase tracking-widest">
                        {{ __('dashboard.back_to_dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
