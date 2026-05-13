@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-3xl px-4 md:px-8">

            <a href="{{ route('news.index') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('news.back_to_news') }}
            </a>

            <article class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur overflow-hidden">
                @if ($news->thumbnail)
                    <div class="aspect-video overflow-hidden bg-black/20">
                        <img src="{{ asset('storage/' . $news->thumbnail) }}" alt="{{ e($news->name) }}"
                            class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="p-6 md:p-10">
                    <header class="mb-6 border-b border-gray-800 pb-6">
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                            {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->format('d M Y, H:i') : '' }}
                        </p>
                        <h1 class="mt-3 text-2xl sm:text-3xl font-bold text-white leading-tight">
                            {{ e($news->name) }}
                        </h1>
                    </header>

                    <div class="prose prose-invert prose-emerald max-w-none">
                        {!! $news->content !!}
                    </div>
                </div>
            </article>

        </div>
    </section>
@endsection
