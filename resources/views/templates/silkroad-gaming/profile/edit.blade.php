@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8 space-y-6">

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8">
                <h1 class="text-3xl font-bold text-white uppercase tracking-widest">{{ __('Profile') }}</h1>
                <p class="mt-2 text-sm text-gray-400">{{ __('dashboard.profile_desc') }}</p>
            </div>

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8">
                @include('template::profile.partials.update-profile-information-form')
            </div>

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8">
                @include('template::profile.partials.update-password-form')
            </div>

        </div>
    </section>
@endsection
