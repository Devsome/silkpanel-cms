@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8">
            <a href="{{ route('news.index') }}"
                class="mb-6 inline-flex items-center gap-2 text-xs font-headline font-bold uppercase tracking-widest gp-text-on-surface-variant transition-colors hover:gp-text-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('news.back_to_list') }}
            </a>

            <article class="gp-card gp-ornate-border overflow-hidden">
                @if ($news->thumbnail)
                    <div class="aspect-video overflow-hidden bg-black/20">
                        <img src="{{ asset('storage/' . $news->thumbnail) }}" alt="{{ e($news->name) }}"
                            class="h-full w-full object-cover">
                    </div>
                @endif

                <div class="p-6 md:p-10">
                    <header class="mb-6 border-b pb-6" style="border-color: rgba(77, 70, 53, 0.3);">
                        <p class="text-xs font-headline uppercase tracking-wider gp-text-outline">
                            {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->format('d M Y, H:i') : '' }}
                        </p>
                        <h1
                            class="mt-3 text-3xl md:text-4xl font-headline font-black uppercase tracking-widest gp-text-primary">
                            {{ e($news->name) }}
                        </h1>
                    </header>

                    <div
                        class="gp-text-on-surface leading-relaxed [&_h1]:font-headline [&_h1]:uppercase [&_h1]:tracking-wide [&_h1]:text-3xl [&_h1]:gp-text-primary [&_h2]:font-headline [&_h2]:uppercase [&_h2]:tracking-wide [&_h2]:text-2xl [&_h2]:gp-text-primary [&_h3]:font-headline [&_h3]:uppercase [&_h3]:tracking-wide [&_h3]:text-xl [&_h3]:gp-text-primary [&_p]:mb-4 [&_a]:gp-text-primary [&_a]:underline [&_blockquote]:border-l-2 [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:gp-text-on-surface-variant [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_img]:my-6 [&_img]:w-full [&_img]:h-auto">
                        {!! $news->content !!}
                    </div>
                </div>
            </article>
        </div>
    </section>
@endsection
