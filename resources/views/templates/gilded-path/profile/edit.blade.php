@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8 space-y-6">
            <a href="{{ route('dashboard') }}"
                class="mb-2 inline-flex items-center gap-2 text-xs font-headline font-bold uppercase tracking-widest gp-text-on-surface-variant transition-colors hover:gp-text-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>
            <div class="gp-card gp-ornate-border p-6 md:p-8">
                <h1 class="text-3xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('Profile') }}
                </h1>
                <p class="mt-2 text-sm gp-text-on-surface-variant">{{ __('dashboard.profile_desc') }}</p>
            </div>

            <div class="gp-card gp-ornate-border p-6 md:p-8">
                @include('template::profile.partials.update-profile-information-form')
            </div>

            <div class="gp-card gp-ornate-border p-6 md:p-8">
                @include('template::profile.partials.update-password-form')
            </div>
        </div>
    </section>
@endsection
