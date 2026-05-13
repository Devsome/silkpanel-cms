@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-lg px-4 sm:px-6">

            <div class="mb-6">
                <a href="{{ route('donate.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                    ← {{ __('donation.back') }}
                </a>
            </div>

            <div class="bg-zinc-900 border border-violet-500/20 p-8">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-2">
                    {{ __('donation.epin_label') }}</p>
                <h1 class="text-2xl font-black uppercase tracking-widest text-white mb-4">{{ __('donation.redeem_epin') }}
                </h1>
                <div class="h-px bg-linear-to-r from-violet-500/40 to-transparent mb-6"></div>

                @if (session('success'))
                    <div class="mb-6 p-3 border border-violet-500/30 bg-violet-500/10 text-violet-300 text-xs font-mono">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 p-3 border border-red-500/30 bg-red-500/10 text-red-300 text-xs font-mono">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('donate.redeem-epin', $provider) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="code"
                            class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('donation.epin_code') }}</label>
                        <input id="code" type="text" name="code" value="{{ old('code') }}" required autofocus
                            class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm tracking-widest uppercase transition placeholder-zinc-600"
                            placeholder="XXXX-XXXX-XXXX">
                        @error('code')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 text-sm font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_20px_rgba(139,92,246,0.4)]">
                        {{ __('donation.redeem') }}
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
