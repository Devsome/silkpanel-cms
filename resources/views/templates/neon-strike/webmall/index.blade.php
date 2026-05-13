@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">

            <div class="mb-6">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                    ← {{ __('dashboard.back_to_dashboard') }}
                </a>
            </div>

            <div class="bg-zinc-900 border border-violet-500/20 p-6 mb-6">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-1">
                    {{ __('webmall.ui.subtitle') }}</p>
                <h1 class="text-2xl font-black uppercase tracking-widest text-white">Webmall</h1>
            </div>

            @livewire('webmall')

        </div>
    </section>
@endsection
