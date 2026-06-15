@extends('template::layouts.app')

@push('styles')
<style>
    @keyframes ag-news-reveal {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .ag-news-card {
        animation: ag-news-reveal 0.45s ease both;
    }
    .ag-news-thumb { overflow: hidden; }
    .ag-news-thumb img {
        transition: transform 0.55s cubic-bezier(0.4,0,0.2,1), filter 0.55s;
        filter: brightness(0.82) saturate(0.85);
    }
    .ag-news-article:hover .ag-news-thumb img {
        transform: scale(1.07);
        filter: brightness(1) saturate(1.1);
    }
    .ag-news-read-arrow { transition: transform 0.2s ease; }
    .ag-news-article:hover .ag-news-read-arrow { transform: translateX(5px); }
    .ag-news-article {
        transition: border-color 0.25s, box-shadow 0.25s;
    }
    .ag-news-article:hover {
        border-color: rgba(34,211,238,0.35) !important;
        box-shadow: 0 0 28px rgba(34,211,238,0.06);
    }

    /* Featured hero gradient overlay */
    .ag-featured-fade {
        background: linear-gradient(
            to top,
            rgba(6,8,15,1)     0%,
            rgba(6,8,15,0.88)  28%,
            rgba(6,8,15,0.35)  65%,
            rgba(6,8,15,0)     100%
        );
    }
    /* Animated underline on title */
    .ag-news-title-underline {
        background-image: linear-gradient(90deg, var(--ag-primary), transparent);
        background-size: 0% 1px;
        background-repeat: no-repeat;
        background-position: bottom left;
        transition: background-size 0.35s ease;
    }
    .ag-news-article:hover .ag-news-title-underline {
        background-size: 100% 1px;
    }
</style>
@endpush

@section('content')
@php
    $featured  = $news->first();
    $rest      = $news->currentPage() === 1 ? $news->slice(1) : $news;
@endphp

{{-- ── HEADER ── --}}
<div style="border-bottom:1px solid rgba(34,211,238,0.07);" class="bg-[var(--ag-background)]">
    <div class="mx-auto max-w-[1560px] px-4 md:px-8 py-10">
        <p class="ag-section-eyebrow mb-1">{{ __('news.title') }}</p>
        <h1 class="ag-font-display font-black ag-text-surface" style="font-size: clamp(1.8rem,4vw,3rem); letter-spacing:-0.02em;">
            {{ __('news.title') }}
        </h1>
    </div>
</div>

<div class="mx-auto max-w-[1560px] px-4 md:px-8 py-10 space-y-12">

    @if ($news->isEmpty())
        <div class="ag-card py-20 text-center">
            <svg class="mx-auto mb-4 h-12 w-12 opacity-30 ag-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <p class="ag-text-muted text-sm">{{ __('news.no_news') }}</p>
        </div>
    @else

        {{-- ── FEATURED HERO (page 1 only) ── --}}
        @if ($featured && $news->currentPage() === 1)
            <a href="{{ route('news.show', $featured->slug) }}"
               class="ag-news-article ag-news-card group relative block overflow-hidden"
               style="border:1px solid rgba(34,211,238,0.1);">

                {{-- Image --}}
                <div class="ag-news-thumb aspect-[21/8] w-full">
                    @if ($featured->thumbnail)
                        <img src="{{ asset('storage/'.$featured->thumbnail) }}"
                             alt="{{ e($featured->name) }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full ag-dot-bg"
                             style="background-color:var(--ag-surface-container);"></div>
                    @endif
                </div>

                {{-- Overlay --}}
                <div class="ag-featured-fade absolute inset-0"></div>

                {{-- Content --}}
                <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10 lg:p-14">
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <span class="ag-badge" style="background:rgba(34,211,238,0.12);border:1px solid rgba(34,211,238,0.28);color:var(--ag-primary);font-size:0.7rem;letter-spacing:0.12em;">
                            {{ __('index.featured') }}
                        </span>
                        <span class="text-xs ag-text-muted ag-font-display tracking-wider">
                            {{ \Carbon\Carbon::parse($featured->published_at)->format('d M Y') }}
                        </span>
                    </div>

                    <h2 class="ag-font-display font-black ag-text-surface leading-tight ag-news-title-underline pb-0.5"
                        style="font-size:clamp(1.5rem,3.5vw,2.5rem);max-width:44rem;letter-spacing:-0.02em;">
                        {{ e($featured->name) }}
                    </h2>

                    @if ($featured->content)
                        <p class="mt-3 ag-text-muted leading-relaxed line-clamp-2 text-sm md:text-base"
                           style="max-width:38rem;">
                            {{ \Illuminate\Support\Str::limit(strip_tags($featured->content), 200) }}
                        </p>
                    @endif

                    <span class="mt-6 inline-flex items-center gap-2 ag-font-display font-semibold text-xs uppercase tracking-[0.15em] ag-text-primary">
                        {{ __('index.read_more') }}
                        <svg class="w-3.5 h-3.5 ag-news-read-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>
            </a>
        @endif

        {{-- ── ARTICLE GRID ── --}}
        @if ($rest->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($rest as $i => $item)
                    <a href="{{ route('news.show', $item->slug) }}"
                       class="ag-news-article ag-news-card group flex flex-col overflow-hidden"
                       style="background:var(--ag-surface-container);border:1px solid var(--ag-outline);animation-delay:{{ $i * 0.06 }}s;">

                        {{-- Thumbnail --}}
                        <div class="ag-news-thumb aspect-video shrink-0 relative">
                            @if ($item->thumbnail)
                                <img src="{{ asset('storage/'.$item->thumbnail) }}"
                                     alt="{{ e($item->name) }}"
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center ag-dot-bg"
                                     style="background-color:var(--ag-surface-container-low);">
                                    <svg class="h-10 w-10 opacity-20 ag-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                </div>
                            @endif
                            {{-- Cyan sweep line --}}
                            <div class="absolute bottom-0 left-0 right-0 h-px origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"
                                 style="background:linear-gradient(90deg,var(--ag-primary),transparent);"></div>
                        </div>

                        {{-- Body --}}
                        <div class="flex flex-col flex-1 p-5">
                            <p class="ag-font-display text-xs font-semibold uppercase tracking-widest ag-text-muted mb-2">
                                {{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}
                            </p>

                            <h2 class="ag-font-display font-bold ag-text-surface leading-snug line-clamp-2 text-base ag-news-title-underline pb-0.5 transition-colors group-hover:ag-text-primary">
                                {{ e($item->name) }}
                            </h2>

                            @if ($item->content)
                                <p class="mt-3 text-sm ag-text-muted leading-relaxed line-clamp-3 flex-1">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($item->content), 140) }}
                                </p>
                            @endif

                            <div class="mt-4 pt-4 flex items-center justify-between"
                                 style="border-top:1px solid var(--ag-outline);">
                                <span class="inline-flex items-center gap-1.5 ag-font-display text-xs font-semibold uppercase tracking-[0.12em] ag-text-primary">
                                    {{ __('index.read_more') }}
                                    <svg class="w-3 h-3 ag-news-read-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                                <span class="text-xs ag-text-muted">
                                    {{ \Carbon\Carbon::parse($item->published_at)->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ── PAGINATION ── --}}
        @if ($news->hasPages())
            <div class="flex justify-center pt-4">
                <div class="ag-card p-4 inline-block">
                    {{ $news->links() }}
                </div>
            </div>
        @endif

    @endif
</div>
@endsection
