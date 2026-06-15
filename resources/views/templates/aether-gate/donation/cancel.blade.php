@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-3xl px-4 md:px-8">
            <div class="ag-card p-8 text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full"
                    style="background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.4);">
                    <svg class="h-8 w-8 ag-stat-amber" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>

                <h1 class="mt-4 text-2xl ag-font-display font-black uppercase tracking-widest ag-text-primary">
                    {{ __('donation.cancel_title') }}
                </h1>
                <p class="mt-3 ag-text-muted">{{ __('donation.cancel_message') }}</p>

                <div class="mt-6">
                    <a href="{{ route('donate.index') }}"
                        class="ag-btn-primary inline-flex items-center px-6 py-2 text-xs ag-font-display font-bold uppercase tracking-widest">
                        {{ __('donation.back_to_donations') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
