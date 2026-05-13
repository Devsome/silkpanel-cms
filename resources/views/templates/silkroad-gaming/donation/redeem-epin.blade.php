@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-xl px-4 md:px-8">

            <a href="{{ route('donate.index') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition mb-6">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('donation.choose_different_method') }}
            </a>

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8">
                <h1 class="text-3xl font-bold text-white uppercase tracking-widest mb-2">
                    {{ __('donation.title') }} – {{ $provider->name }}
                </h1>
                <p class="text-sm text-gray-400 mb-6">{{ __('donation.epin_description') }}</p>

                @if (session('error'))
                    <div class="mb-6 rounded-xl border border-red-800/40 bg-red-900/20 p-4">
                        <p class="text-sm text-red-300">{{ session('error') }}</p>
                    </div>
                @endif

                <form action="{{ route('donate.redeem-epin', $provider) }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="epin_code" class="block text-sm font-medium text-gray-300 mb-1">
                            {{ __('donation.epin_code') }}
                        </label>
                        <input id="epin_code" type="text" name="epin_code" required value="{{ old('epin_code') }}"
                            placeholder="{{ __('donation.epin_code_placeholder') }}"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                        @error('epin_code')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="epin_secret" class="block text-sm font-medium text-gray-300 mb-1">
                            {{ __('donation.epin_secret') }}
                        </label>
                        <input id="epin_secret" type="text" name="epin_secret" required value="{{ old('epin_secret') }}"
                            placeholder="{{ __('donation.epin_secret_placeholder') }}"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                        @error('epin_secret')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('donate.index') }}"
                            class="px-4 py-2 rounded-lg border border-gray-700 text-gray-400 text-sm font-semibold hover:text-white hover:border-gray-600 transition">
                            {{ __('donation.cancel') }}
                        </a>
                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 text-sm font-bold uppercase tracking-widest hover:brightness-110 transition">
                            {{ __('donation.redeem_epin') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
