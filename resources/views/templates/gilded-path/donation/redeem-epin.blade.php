@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8">
            <div class="mb-6">
                <a href="{{ route('donate.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-headline font-bold uppercase tracking-widest gp-text-on-surface-variant hover:gp-text-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('donation.choose_different_method') }}
                </a>
            </div>

            <div class="gp-card gp-ornate-border p-6 md:p-8">
                <h1 class="text-3xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('donation.title') }} - {{ $provider->name }}</h1>
                <p class="mt-2 text-sm gp-text-on-surface-variant">{{ __('donation.epin_description') }}</p>

                @if (session('error'))
                    <div class="mt-6 gp-card p-4" style="border:1px solid rgba(255, 100, 100, 0.5);">
                        <p class="text-sm text-red-300">{{ session('error') }}</p>
                    </div>
                @endif

                <form action="{{ route('donate.redeem-epin', $provider) }}" method="POST" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="epin_code"
                            class="block text-sm font-headline font-bold uppercase tracking-wider gp-text-on-surface">{{ __('donation.epin_code') }}</label>
                        <input id="epin_code" type="text" name="epin_code" required value="{{ old('epin_code') }}"
                            placeholder="{{ __('donation.epin_code_placeholder') }}"
                            class="mt-2 block w-full gp-input px-3 py-2" />
                        @error('epin_code')
                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="epin_secret"
                            class="block text-sm font-headline font-bold uppercase tracking-wider gp-text-on-surface">{{ __('donation.epin_secret') }}</label>
                        <input id="epin_secret" type="text" name="epin_secret" required value="{{ old('epin_secret') }}"
                            placeholder="{{ __('donation.epin_secret_placeholder') }}"
                            class="mt-2 block w-full gp-input px-3 py-2" />
                        @error('epin_secret')
                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('donate.index') }}"
                            class="px-4 py-2 text-xs font-headline font-bold uppercase tracking-widest gp-card-low gp-text-on-surface-variant">
                            {{ __('donation.cancel') }}
                        </a>
                        <button type="submit"
                            class="px-4 py-2 text-xs font-headline font-bold uppercase tracking-widest gp-gold-btn">
                            {{ __('donation.redeem_epin') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
