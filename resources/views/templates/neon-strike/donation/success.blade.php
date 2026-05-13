@extends('template::layouts.app')

@section('content')
    <section class="py-20">
        <div class="mx-auto max-w-lg px-4 text-center">
            <div
                class="w-16 h-16 border border-violet-500/40 flex items-center justify-center mx-auto mb-6 shadow-[0_0_30px_rgba(139,92,246,0.2)]">
                <svg class="w-8 h-8 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs font-mono uppercase tracking-[0.4em] text-violet-400/70 mb-2">
                {{ __('donation.success_label') }}</p>
            <h1 class="text-2xl font-black uppercase tracking-widest text-white mb-4">{{ __('donation.success_title') }}
            </h1>
            <div class="h-px bg-linear-to-r from-transparent via-violet-500/40 to-transparent mb-6"></div>
            <p class="text-zinc-500 leading-relaxed mb-8">{{ __('donation.success_description') }}</p>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_20px_rgba(139,92,246,0.4)]">
                {{ __('donation.go_to_dashboard') }}
            </a>
        </div>
    </section>
@endsection
