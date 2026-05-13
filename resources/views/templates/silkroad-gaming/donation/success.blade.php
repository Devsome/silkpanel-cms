@extends('template::layouts.app')

@section('content')
    <section class="py-20">
        <div class="mx-auto max-w-3xl px-4 md:px-8">
            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-8 text-center">
                <div
                    class="w-16 h-16 rounded-full bg-emerald-900/30 border border-emerald-700/30 flex items-center justify-center mx-auto">
                    <svg class="h-8 w-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>

                <h1 class="mt-4 text-2xl font-bold text-white uppercase tracking-widest">
                    {{ __('donation.success_title') }}
                </h1>

                @if ($donation && $donation->isCompleted())
                    <p class="mt-3 text-gray-400">
                        {{ __('donation.success_silk_added', ['amount' => number_format($donation->silk_amount)]) }}
                    </p>
                @else
                    <p class="mt-3 text-gray-400">{{ __('donation.success_processing') }}</p>
                @endif

                <div class="mt-8 flex items-center justify-center gap-4">
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-cyan-500 text-gray-950 text-sm font-bold uppercase tracking-widest hover:brightness-110 transition">
                        {{ __('dashboard.title') }}
                    </a>
                    <a href="{{ route('donate.index') }}" class="text-sm text-gray-400 hover:text-emerald-400 transition">
                        {{ __('donation.back_to_donations') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
