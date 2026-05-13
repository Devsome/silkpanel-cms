@extends('template::layouts.app')

@section('content')
    <section class="py-20">
        <div class="mx-auto max-w-xl px-4 text-center">
            <p class="text-8xl font-black bg-gradient-to-r from-yellow-400 to-amber-400 bg-clip-text text-transparent">503
            </p>
            <h1 class="mt-4 text-2xl font-bold text-white">{{ __('errors.503_title') }}</h1>
            <p class="mt-2 text-gray-400 text-sm">{{ $exception->getMessage() ?: __('errors.503_message') }}</p>
            <div class="mt-8 flex items-center justify-center gap-3">
                <span
                    class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-yellow-900/30 text-yellow-400 border border-yellow-800/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                    {{ __('errors.503_maintenance') }}
                </span>
            </div>
        </div>
    </section>
@endsection
