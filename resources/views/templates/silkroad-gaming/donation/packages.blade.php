@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">

            <a href="{{ route('donate.index') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition mb-6">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('donation.choose_different_method') }}
            </a>

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8 mb-8">
                <h1 class="text-3xl font-bold text-white uppercase tracking-widest">
                    {{ __('donation.title') }} – {{ $provider->name }}
                </h1>
            </div>

            @if (session('error'))
                <div class="mb-6 rounded-xl border border-red-800/40 bg-red-900/20 p-4">
                    <p class="text-sm text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            @if ($packages->isEmpty())
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-8 text-center">
                    <p class="text-gray-500">{{ __('donation.no_packages') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($packages as $package)
                        <div
                            class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur flex flex-col overflow-hidden hover:border-emerald-500/30 transition">
                            @if ($package->image)
                                <div class="h-48 overflow-hidden">
                                    <img src="{{ Storage::url($package->image) }}" alt="{{ $package->name }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @endif

                            <div class="p-6 flex flex-1 flex-col">
                                <h2 class="text-base font-bold text-white uppercase tracking-wider">{{ $package->name }}
                                </h2>
                                @if ($package->description)
                                    <p class="mt-2 text-sm text-gray-400 flex-1">{{ $package->description }}</p>
                                @endif

                                <div class="mt-4 flex items-center justify-between">
                                    <span
                                        class="text-2xl font-black bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">
                                        {{ number_format($package->silk_amount) }}
                                        <span class="text-sm font-semibold">{{ __('donation.silk') }}</span>
                                    </span>
                                    <span class="text-lg font-bold text-white">
                                        {{ $package->currency }} {{ number_format($package->price, 2) }}
                                    </span>
                                </div>

                                <div class="mt-6">
                                    <form action="{{ route('donate.checkout', $package) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="provider" value="{{ $provider->slug->value }}">
                                        <button type="submit"
                                            class="w-full px-4 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-cyan-500 text-gray-950 text-sm font-bold uppercase tracking-widest hover:brightness-110 transition">
                                            {{ __('donation.buy_now') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
