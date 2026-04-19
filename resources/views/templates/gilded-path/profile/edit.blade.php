@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8 space-y-6">
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
