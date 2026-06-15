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
                <p class="ag-section-eyebrow">{{ __('webmall.ui.subtitle') }}</p>
                <h1 class="ag-section-title mt-2">Webmall</h1>
            </div>

            @livewire('webmall')

        </div>
    </section>
@endsection
