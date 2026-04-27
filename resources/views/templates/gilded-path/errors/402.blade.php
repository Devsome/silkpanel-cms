@extends('template::layouts.app')

@section('content')
    <div class="flex items-center justify-center min-h-[70vh] px-4 py-16 sm:px-6 lg:px-8">
        <div class="w-full max-w-lg text-center">
            <p class="text-8xl font-extrabold tracking-tight gp-text-primary">
                402
            </p>

            <h1 class="mt-4 text-3xl font-bold tracking-tight gp-text-on-surface sm:text-4xl">
                {{ __('errors.402.title') }}
            </h1>

            <p class="mt-4 text-lg leading-relaxed gp-text-on-surface">
                {{ __('errors.402.message') }}
            </p>

            <div class="mt-10">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gp-gold-btn px-6 py-2 font-headline font-bold uppercase tracking-wider text-sm transition-all">
                    {{ __('errors.back_to_home') }}
                </a>
            </div>
        </div>
    </div>
@endsection
