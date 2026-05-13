@extends('template::layouts.app')

@section('content')
    <section class="py-20">
        <div class="mx-auto max-w-xl px-4 text-center">
            <p class="text-8xl font-black bg-linear-to-r from-red-400 to-orange-400 bg-clip-text text-transparent">500</p>
            <h1 class="mt-4 text-2xl font-bold text-white">{{ __('errors.500_title') }}</h1>
            <p class="mt-2 text-gray-400 text-sm">{{ __('errors.500_message') }}</p>
            <div class="mt-8">
                <a href="{{ url('/') }}"
                    class="px-6 py-2 rounded-lg bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 font-semibold hover:brightness-110 transition">
                    {{ __('errors.back_home') }}
                </a>
            </div>
        </div>
    </section>
@endsection
