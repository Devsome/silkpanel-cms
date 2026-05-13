@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            {{-- Back --}}
            <div class="mb-6">
                <a href="{{ route('news.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                    ← {{ __('news.back') }}
                </a>
            </div>

            <article class="bg-zinc-900 border border-violet-500/20">
                {{-- Thumbnail --}}
                @if ($news->thumbnail)
                    <div class="relative aspect-[16/6] overflow-hidden">
                        <img src="{{ asset('storage/' . $news->thumbnail) }}" alt="{{ e($news->name) }}"
                            class="w-full h-full object-cover opacity-70">
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-zinc-900/20 to-transparent"></div>
                    </div>
                @endif

                <div class="p-8">
                    {{-- Meta --}}
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-3">
                        {{ \Carbon\Carbon::parse($news->published_at)->format('d M Y') }}
                    </p>

                    {{-- Title --}}
                    <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-wider text-white leading-tight mb-6">
                        {{ e($news->name) }}
                    </h1>

                    <div class="h-px bg-linear-to-r from-violet-500/40 to-transparent mb-8"></div>

                    {{-- Content --}}
                    <div
                        class="prose prose-invert prose-sm max-w-none
                        prose-headings:font-bold prose-headings:uppercase prose-headings:tracking-widest prose-headings:text-white
                        prose-p:text-zinc-400 prose-p:leading-relaxed
                        prose-a:text-violet-400 prose-a:no-underline hover:prose-a:text-violet-300
                        prose-strong:text-zinc-200
                        prose-code:text-fuchsia-400 prose-code:bg-zinc-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:font-mono prose-code:text-xs
                        prose-hr:border-zinc-800
                        prose-img:border prose-img:border-zinc-800">
                        {!! $news->content !!}
                    </div>
                </div>
            </article>

            <div class="mt-6">
                <a href="{{ route('news.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition border border-transparent hover:border-violet-500/30 px-3 py-1.5">
                    ← {{ __('news.back') }}
                </a>
            </div>
        </div>
    </section>
@endsection
