@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-5xl px-4 md:px-8">
            <a href="{{ route('news.index') }}"
                class="mb-6 inline-flex items-center gap-2 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-muted hover:ag-text-primary transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('news.back_to_list') }}
            </a>

            <article class="ag-card overflow-hidden">
                @if ($news->thumbnail)
                    <div class="aspect-video overflow-hidden bg-black/20">
                        <img src="{{ asset('storage/' . $news->thumbnail) }}" alt="{{ e($news->name) }}"
                            class="h-full w-full object-cover">
                    </div>
                @endif

                <div class="p-6 md:p-10">
                    <header class="mb-6 border-b pb-6 ag-divider">
                        <p class="text-xs ag-font-display uppercase tracking-wider ag-text-muted">
                            {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->format('d M Y, H:i') : '' }}
                        </p>
                        <h1 class="mt-3 text-3xl md:text-4xl ag-font-display font-black uppercase tracking-widest ag-text-primary">
                            {{ e($news->name) }}
                        </h1>
                    </header>

                    <div class="ag-news-content ag-text-surface leading-relaxed">
                        {!! $news->content !!}
                    </div>
                </div>
            </article>
        </div>
    </section>

    <style>
        .ag-news-content h1,
        .ag-news-content h2,
        .ag-news-content h3,
        .ag-news-content h4,
        .ag-news-content h5,
        .ag-news-content h6 {
            font-family: 'Chakra Petch', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--ag-primary);
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .ag-news-content h1 { font-size: 2rem; }
        .ag-news-content h2 { font-size: 1.5rem; }
        .ag-news-content h3 { font-size: 1.25rem; }
        .ag-news-content p { margin-bottom: 1.25rem; }
        .ag-news-content a { color: var(--ag-primary); text-decoration: underline; }
        .ag-news-content blockquote {
            border-left: 3px solid var(--ag-primary);
            padding-left: 1.25rem;
            margin-left: 0;
            margin-bottom: 1.25rem;
            opacity: 0.8;
            font-style: italic;
        }
        .ag-news-content ul { list-style: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; }
        .ag-news-content ol { list-style: decimal; padding-left: 1.5rem; margin-bottom: 1.25rem; }
        .ag-news-content img { max-width: 100%; height: auto; margin: 1.5rem 0; border: 1px solid rgba(34,211,238,0.15); }
        .ag-news-content code {
            background: rgba(34,211,238,0.08);
            color: var(--ag-primary);
            padding: 0.15rem 0.4rem;
            font-family: 'Space Mono', monospace;
            font-size: 0.85em;
        }
        .ag-news-content pre {
            background: rgba(13,18,36,0.9);
            border: 1px solid rgba(34,211,238,0.2);
            padding: 1rem;
            overflow-x: auto;
            margin-bottom: 1.25rem;
        }
        .ag-news-content strong { color: var(--ag-primary); font-weight: 700; }
    </style>
@endsection
