@extends('template::layouts.app')

@section('content')
    <section class="min-h-[70vh] flex items-center justify-center">
        <div class="text-center px-6">
            <p
                class="text-[8rem] font-black leading-none font-mono bg-linear-to-br from-amber-500 via-fuchsia-500 to-violet-500 bg-clip-text text-transparent select-none opacity-20">
                503</p>
            <div class="-mt-8 relative z-10">
                <p class="text-xs font-mono uppercase tracking-[0.4em] text-amber-400/70 mb-2">Maintenance</p>
                <h1 class="text-2xl font-black uppercase tracking-widest text-white mb-3">Service Unavailable</h1>
                <p class="text-sm text-zinc-500 mb-8 max-w-md mx-auto">
                    {{ $exception->getMessage() ?: __('errors.503.message') ?? 'We are currently performing maintenance. Please check back soon.' }}
                </p>
                <p class="text-xs font-mono uppercase tracking-wider text-zinc-600">
                    {{ __('errors.retry_later') ?? 'Please try again later' }}</p>
            </div>
        </div>
    </section>
@endsection
