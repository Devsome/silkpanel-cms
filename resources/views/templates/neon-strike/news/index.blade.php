@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-1">
                    {{ __('news.section_label') }}</p>
                <h1 class="text-3xl font-black uppercase tracking-widest text-white">{{ __('news.title') }}</h1>
                <div class="mt-3 h-px bg-linear-to-r from-violet-500/40 to-transparent"></div>
            </div>

            @if ($news->isEmpty())
                <div class="bg-zinc-900 border border-zinc-800 p-12 text-center">
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">{{ __('news.no_news') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($news as $item)
                        <a href="{{ route('news.show', $item->slug) }}"
                            class="group block bg-zinc-900 border border-violet-500/15 hover:border-violet-500/40 transition shadow-[0_0_0_0_rgba(139,92,246,0)] hover:shadow-[0_0_25px_rgba(139,92,246,0.1)] overflow-hidden">
                            @if ($item->thumbnail)
                                <div class="relative aspect-video overflow-hidden">
                                    <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ e($item->name) }}"
                                        class="w-full h-full object-cover opacity-60 group-hover:opacity-80 group-hover:scale-105 transition duration-500">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-transparent to-transparent">
                                    </div>
                                </div>
                            @else
                                <div
                                    class="aspect-video bg-zinc-950 flex items-center justify-center border-b border-zinc-800">
                                    <svg class="w-10 h-10 text-violet-500/20" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="p-5">
                                <p class="text-xs font-mono text-zinc-600 mb-2">
                                    {{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}
                                </p>
                                <h2
                                    class="font-bold uppercase tracking-wide text-zinc-200 group-hover:text-violet-300 transition leading-snug line-clamp-2">
                                    {{ e($item->name) }}
                                </h2>
                                <div
                                    class="mt-4 flex items-center gap-1 text-xs font-mono uppercase tracking-wider text-violet-500/60 group-hover:text-violet-400 transition">
                                    {{ __('news.read_more') }} →
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($news->hasPages())
                    <div class="mt-8 flex items-center justify-center gap-2">
                        @if ($news->onFirstPage())
                            <span
                                class="px-3 py-1.5 text-xs font-mono uppercase tracking-wider text-zinc-700 border border-zinc-800">←</span>
                        @else
                            <a href="{{ $news->previousPageUrl() }}"
                                class="px-3 py-1.5 text-xs font-mono uppercase tracking-wider text-zinc-400 border border-zinc-700 hover:border-violet-500/50 hover:text-violet-400 transition">←</a>
                        @endif

                        @foreach ($news->getUrlRange(1, $news->lastPage()) as $page => $url)
                            @if ($page == $news->currentPage())
                                <span
                                    class="px-3 py-1.5 text-xs font-mono font-bold text-white border border-violet-500/60 bg-violet-500/15 shadow-[0_0_10px_rgba(139,92,246,0.2)]">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-3 py-1.5 text-xs font-mono text-zinc-400 border border-zinc-700 hover:border-violet-500/40 hover:text-violet-400 transition">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($news->hasMorePages())
                            <a href="{{ $news->nextPageUrl() }}"
                                class="px-3 py-1.5 text-xs font-mono uppercase tracking-wider text-zinc-400 border border-zinc-700 hover:border-violet-500/50 hover:text-violet-400 transition">→</a>
                        @else
                            <span
                                class="px-3 py-1.5 text-xs font-mono uppercase tracking-wider text-zinc-700 border border-zinc-800">→</span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
