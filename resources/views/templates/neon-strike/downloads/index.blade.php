@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-1">
                    {{ __('downloads.section_label') }}</p>
                <h1 class="text-3xl font-black uppercase tracking-widest text-white">{{ __('downloads.title') }}</h1>
                <div class="mt-3 h-px bg-linear-to-r from-violet-500/40 to-transparent"></div>
            </div>

            @if ($downloads->isEmpty())
                <div class="bg-zinc-900 border border-zinc-800 p-12 text-center">
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">{{ __('downloads.no_downloads') }}
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach ($downloads as $download)
                        <div
                            class="bg-zinc-900 border border-violet-500/15 hover:border-violet-500/35 transition flex flex-col">
                            @if ($download->image)
                                <div class="aspect-video overflow-hidden">
                                    <img src="{{ asset('storage/' . $download->image) }}" alt="{{ e($download->name) }}"
                                        class="w-full h-full object-cover opacity-60">
                                </div>
                            @else
                                <div
                                    class="aspect-video bg-zinc-950/50 flex items-center justify-center border-b border-zinc-800">
                                    <svg class="w-12 h-12 text-violet-500/15" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </div>
                            @endif
                            <div class="p-5 flex-1 flex flex-col">
                                <h2 class="font-bold uppercase tracking-wider text-zinc-200 mb-2">{{ e($download->name) }}
                                </h2>
                                @if ($download->description)
                                    <p class="text-sm text-zinc-500 leading-relaxed flex-1 mb-4">
                                        {{ e($download->description) }}</p>
                                @else
                                    <div class="flex-1"></div>
                                @endif
                                @if ($download->link)
                                    <a href="{{ e($download->link) }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center justify-center gap-2 w-full py-2.5 text-xs font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_15px_rgba(139,92,246,0.3)]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        {{ __('downloads.download') }}
                                    </a>
                                @else
                                    <span
                                        class="inline-flex items-center justify-center w-full py-2.5 text-xs font-bold uppercase tracking-[0.2em] text-zinc-600 border border-zinc-800 cursor-not-allowed">
                                        {{ __('downloads.unavailable') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
