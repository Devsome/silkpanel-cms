@extends('template::layouts.app')

@section('content')
    <div class="flex items-center justify-center min-h-[70vh] px-4 py-16 sm:px-6 lg:px-8">
        <div class="w-full max-w-lg text-center">
            <p class="text-8xl font-extrabold tracking-tight ag-stat-number" style="font-family:'Space Mono',monospace;">
                402
            </p>

            <div class="mt-4 h-0.5 w-24 mx-auto" style="background:linear-gradient(to right,transparent,var(--ag-primary),transparent);"></div>

            <h1 class="mt-6 text-3xl font-bold tracking-tight ag-text-surface sm:text-4xl ag-font-display uppercase">
                {{ __('errors.402.title') }}
            </h1>

            <p class="mt-4 text-base leading-relaxed ag-text-muted">
                {{ __('errors.402.message') }}
            </p>

            <div class="mt-10">
                <a href="{{ url('/') }}"
                    class="ag-btn-primary inline-flex items-center px-6 py-2 ag-font-display font-bold uppercase tracking-wider text-sm transition-all">
                    {{ __('errors.back_to_home') }}
                </a>
            </div>
        </div>
    </div>
@endsection
