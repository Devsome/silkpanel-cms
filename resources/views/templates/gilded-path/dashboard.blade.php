@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8 space-y-8">
            <div class="gp-card gp-ornate-border p-6 md:p-8">
                <p class="text-xs font-headline font-bold uppercase tracking-widest gp-text-outline">
                    {{ __('dashboard.title') }}</p>
                <h1 class="mt-2 text-3xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('dashboard.welcome', ['name' => Auth::user()->name]) }}
                </h1>
                <p class="mt-2 text-sm gp-text-on-surface-variant">{{ Auth::user()->email }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('profile.edit') }}"
                    class="group gp-card gp-ornate-border p-6 transition-all hover:-translate-y-0.5 hover:shadow-2xl">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center gp-card-lowest"
                            style="border:1px solid rgba(242,202,80,0.3);">
                            <svg class="h-6 w-6 gp-text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3
                                class="font-headline font-bold uppercase tracking-wider gp-text-on-surface group-hover:gp-text-primary">
                                {{ __('dashboard.profile') }}
                            </h3>
                            <p class="text-sm gp-text-on-surface-variant">{{ __('dashboard.profile_desc') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('donate.index') }}"
                    class="group gp-card gp-ornate-border p-6 transition-all hover:-translate-y-0.5 hover:shadow-2xl">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center gp-card-lowest"
                            style="border:1px solid rgba(242,202,80,0.3);">
                            <svg class="h-6 w-6 gp-text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3
                                class="font-headline font-bold uppercase tracking-wider gp-text-on-surface group-hover:gp-text-primary">
                                {{ __('dashboard.donations') }}
                            </h3>
                            <p class="text-sm gp-text-on-surface-variant">{{ __('dashboard.donations_desc') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('voting.index') }}"
                    class="group gp-card gp-ornate-border p-6 transition-all hover:-translate-y-0.5 hover:shadow-2xl">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center gp-card-lowest"
                            style="border:1px solid rgba(242,202,80,0.3);">
                            <svg class="h-6 w-6 gp-text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                            </svg>
                        </div>
                        <div>
                            <h3
                                class="font-headline font-bold uppercase tracking-wider gp-text-on-surface group-hover:gp-text-primary">
                                {{ __('dashboard.voting') }}
                            </h3>
                            <p class="text-sm gp-text-on-surface-variant">{{ __('dashboard.voting_desc') }}</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
@endsection
