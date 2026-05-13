@extends('template::layouts.app')

@section('content')
    <section class="min-h-[70vh] flex items-center justify-center">
        <div class="text-center px-6">
            <p
                class="text-[8rem] font-black leading-none font-mono bg-linear-to-br from-red-500 via-fuchsia-500 to-violet-500 bg-clip-text text-transparent select-none opacity-20">
                500</p>
            <div class="-mt-8 relative z-10">
                <p class="text-xs font-mono uppercase tracking-[0.4em] text-red-400/70 mb-2">Error</p>
                <h1 class="text-2xl font-black uppercase tracking-widest text-white mb-3">Server Error</h1>
                <p class="text-sm text-zinc-500 mb-8 max-w-md mx-auto">
                    {{ __('errors.500.message') ?? 'An unexpected error occurred on our end. Please try again later.' }}</p>
                <a href="{{ url('/') }}"
                    class="inline-block px-6 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition">
                    {{ __('errors.go_home') ?? 'Go Home' }}
                </a>
            </div>
        </div>
    </section>
@endsection
