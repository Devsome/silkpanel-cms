@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8">
            <div class="mb-6">
                <a href="{{ route('donate.index') }}"
                    class="inline-flex items-center gap-2 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-muted hover:ag-text-primary transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('donation.choose_different_method') }}
                </a>
            </div>

            <div class="ag-card-glow p-6 md:p-8">
                <p class="ag-section-eyebrow">{{ __('donation.title') }} — {{ $provider->name }}</p>
                <h1 class="ag-section-title mt-2">{{ __('donation.redeem_epin') }}</h1>
                <p class="mt-2 text-sm ag-text-muted">{{ __('donation.epin_description') }}</p>

                @if (session('error'))
                    <div class="mt-6 ag-alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('donate.redeem-epin', $provider) }}" method="POST" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="epin_code"
                            class="block text-sm ag-font-display font-bold uppercase tracking-wider ag-text-surface mb-1.5">
                            {{ __('donation.epin_code') }}
                        </label>
                        <input id="epin_code" type="text" name="epin_code" required value="{{ old('epin_code') }}"
                            placeholder="{{ __('donation.epin_code_placeholder') }}"
                            class="ag-input block w-full px-4 py-2.5 transition" />
                        @error('epin_code')
                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="epin_secret"
                            class="block text-sm ag-font-display font-bold uppercase tracking-wider ag-text-surface mb-1.5">
                            {{ __('donation.epin_secret') }}
                        </label>
                        <input id="epin_secret" type="text" name="epin_secret" required value="{{ old('epin_secret') }}"
                            placeholder="{{ __('donation.epin_secret_placeholder') }}"
                            class="ag-input block w-full px-4 py-2.5 transition" />
                        @error('epin_secret')
                            <p class="mt-1 text-sm text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('donate.index') }}"
                            class="ag-btn-secondary px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-widest">
                            {{ __('donation.cancel') }}
                        </a>
                        <button type="submit"
                            class="ag-btn-primary px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-widest">
                            {{ __('donation.redeem_epin') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
