@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- Back --}}
            <div class="mb-6">
                <a href="{{ route('donate.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                    ← {{ __('donation.back') }}
                </a>
            </div>

            {{-- Header --}}
            <div class="mb-8">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-1">
                    {{ __('donation.section_label') }}</p>
                <h1 class="text-3xl font-black uppercase tracking-widest text-white">{{ $provider->name }}</h1>
                <div class="mt-3 h-px bg-linear-to-r from-violet-500/40 to-transparent"></div>
            </div>

            @if (session('error'))
                <div class="mb-6 p-4 border border-red-500/40 bg-red-500/10 text-red-300 text-sm font-mono">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Packages --}}
            @if ($packages->isEmpty())
                <div class="bg-zinc-900 border border-zinc-800 p-12 text-center">
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">{{ __('donation.no_packages') }}
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($packages as $package)
                        <div
                            class="bg-zinc-900 border {{ $package->is_featured ?? false ? 'border-violet-500/50 shadow-[0_0_25px_rgba(139,92,246,0.15)]' : 'border-zinc-800 hover:border-violet-500/30' }} p-6 flex flex-col relative transition">

                            @if ($package->is_featured ?? false)
                                <span
                                    class="absolute -top-px left-4 px-2 py-0.5 text-xs font-mono uppercase tracking-wider bg-linear-to-r from-violet-600 to-fuchsia-600 text-white">
                                    {{ __('donation.featured') }}
                                </span>
                            @endif

                            {{-- Package image --}}
                            @if ($package->image)
                                <div class="aspect-video mb-4 overflow-hidden border border-zinc-800">
                                    <img src="{{ asset('storage/' . $package->image) }}" alt="{{ e($package->name) }}"
                                        class="w-full h-full object-cover opacity-80">
                                </div>
                            @endif

                            {{-- Name --}}
                            <h2 class="text-base font-bold uppercase tracking-widest text-white mb-2">
                                {{ e($package->name) }}</h2>

                            {{-- Silk amount --}}
                            <p
                                class="text-3xl font-black font-mono bg-linear-to-r from-violet-400 to-fuchsia-400 bg-clip-text text-transparent mb-1">
                                {{ number_format($package->silk_amount) }}
                                <span class="text-sm text-zinc-500 font-normal">Silk</span>
                            </p>

                            {{-- Description --}}
                            @if ($package->description)
                                <p class="text-xs text-zinc-500 leading-relaxed mt-2 mb-4 flex-1">
                                    {{ e($package->description) }}</p>
                            @else
                                <div class="flex-1 mb-4"></div>
                            @endif

                            {{-- Price --}}
                            <p class="text-lg font-bold text-zinc-200 mb-4">
                                {{ number_format($package->price, 2) }} {{ $package->currency }}
                            </p>

                            {{-- Checkout form --}}
                            <form method="POST" action="{{ route('donate.checkout', $package) }}">
                                @csrf
                                <input type="hidden" name="provider" value="{{ $provider->slug->value }}">
                                <button type="submit"
                                    class="w-full py-2.5 text-sm font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_20px_rgba(139,92,246,0.4)]">
                                    {{ __('donation.pay_with', ['provider' => $provider->name]) }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
