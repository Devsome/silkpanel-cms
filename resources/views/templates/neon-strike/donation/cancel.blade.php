@extends('template::layouts.app')

@section('content')
    <section class="py-20">
        <div class="mx-auto max-w-lg px-4 text-center">
            <div
                class="w-16 h-16 border border-red-500/40 flex items-center justify-center mx-auto mb-6 shadow-[0_0_30px_rgba(239,68,68,0.15)]">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs font-mono uppercase tracking-[0.4em] text-red-400/70 mb-2">{{ __('donation.cancel_label') }}
            </p>
            <h1 class="text-2xl font-black uppercase tracking-widest text-white mb-4">{{ __('donation.cancel_title') }}</h1>
            <div class="h-px bg-linear-to-r from-transparent via-red-500/30 to-transparent mb-6"></div>
            <p class="text-zinc-500 leading-relaxed mb-8">{{ __('donation.cancel_description') }}</p>
            <a href="{{ route('donate.index') }}"
                class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold uppercase tracking-[0.2em] text-violet-400 border border-violet-500/40 hover:bg-violet-500/10 hover:border-violet-400 transition">
                {{ __('donation.try_again') }}
            </a>
        </div>
    </section>
@endsection
