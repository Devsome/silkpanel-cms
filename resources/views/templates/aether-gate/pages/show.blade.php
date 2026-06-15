@extends('template::layouts.app')

@section('content')
    <main class="py-12 px-4 sm:px-6 lg:px-8" style="background-color:var(--ag-background);">
        <article class="max-w-4xl mx-auto">
            {{-- Header --}}
            <header class="mb-12 pb-8 border-b-2" style="border-color:var(--ag-primary);">
                <h1 class="text-4xl md:text-5xl font-bold ag-font-display mb-4 ag-text-primary uppercase tracking-widest">
                    {{ e($translation->title) }}
                </h1>
                @if ($translation->seo_description)
                    <p class="text-lg md:text-xl ag-text-muted">
                        {{ e($translation->seo_description) }}
                    </p>
                @endif
            </header>

            {{-- Content --}}
            <div class="ag-page-content ag-text-surface leading-relaxed">
                @if (is_array($translation->content))
                    @foreach ($translation->content as $block)
                        @php
                            $type = $block['type'] ?? null;
                            $data = $block['data'] ?? [];
                        @endphp

                        @if ($type === 'heading')
                            @php $tag = $data['level'] ?? 'h2'; @endphp
                            <{{ $tag }} class="ag-font-display mt-12 mb-4 ag-text-primary uppercase tracking-wide">
                                {{ $data['headline'] ?? '' }}
                                @if (!empty($data['subheadline']))
                                    <small class="block text-base font-normal mt-2 ag-text-muted">
                                        {{ $data['subheadline'] }}
                                    </small>
                                @endif
                            </{{ $tag }}>
                        @elseif ($type === 'paragraph')
                            @php
                                $paragraphText = $data['paragraph'] ?? '';
                                if (is_array($paragraphText)) {
                                    $paragraphText = implode("\n", $paragraphText);
                                }
                            @endphp
                            <p class="text-base leading-7 mb-6 ag-text-muted">
                                {!! nl2br(e($paragraphText)) !!}
                            </p>
                        @elseif ($type === 'rich_text')
                            <div>
                                {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($data['rich_text'] ?? '') }}
                            </div>
                        @elseif ($type === 'bbcode')
                            @php
                                $bbcode = $data['bbcode'] ?? '';
                                if (is_array($bbcode)) {
                                    $bbcode = implode("\n", $bbcode);
                                }
                            @endphp
                            <div>
                                {!! str(app(\App\Services\BBCodeService::class)->toHtml($bbcode))->sanitizeHtml() !!}
                            </div>
                        @elseif ($type === 'image')
                            @php
                                $imageUrl = $data['url'] ?? '';
                                if (is_array($imageUrl)) {
                                    $imageUrl = collect($imageUrl)->first() ?? '';
                                }
                            @endphp
                            <figure class="my-8 p-4" style="background:rgba(13,18,36,0.8);border:1px solid rgba(34,211,238,0.2);">
                                <img src="{{ asset('storage/' . $imageUrl) }}" alt="{{ e($data['alt'] ?? '') }}"
                                    class="h-64 rounded-lg" loading="lazy">
                                @if (!empty($data['alt']))
                                    <figcaption class="text-center mt-4 text-sm ag-text-muted">
                                        {{ e($data['alt']) }}
                                    </figcaption>
                                @endif
                            </figure>
                        @endif
                    @endforeach
                @elseif (is_string($translation->content) && !empty($translation->content))
                    <div>
                        {!! $translation->content !!}
                    </div>
                @endif
            </div>
        </article>
    </main>

    <style>
        .ag-page-content h1, .ag-page-content h2, .ag-page-content h3,
        .ag-page-content h4, .ag-page-content h5, .ag-page-content h6 {
            font-family: 'Chakra Petch', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--ag-primary);
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .ag-page-content h1 { font-size: 2.25rem; }
        .ag-page-content h2 { font-size: 1.875rem; }
        .ag-page-content h3 { font-size: 1.5rem; }
        .ag-page-content p { margin-bottom: 1.5rem; line-height: 1.75; color: rgba(200,220,255,0.7); }
        .ag-page-content ul, .ag-page-content ol { margin-left: 2rem; margin-bottom: 1.5rem; color: rgba(200,220,255,0.7); }
        .ag-page-content li { margin-bottom: 0.5rem; }
        .ag-page-content strong { color: var(--ag-primary); font-weight: 600; }
        .ag-page-content em { font-style: italic; opacity: 0.85; }
        .ag-page-content a { color: var(--ag-primary); text-decoration: none; border-bottom: 1px solid var(--ag-primary); }
        .ag-page-content a:hover { opacity: 0.8; }
        .ag-page-content blockquote {
            border-left: 3px solid var(--ag-primary);
            padding-left: 1.5rem;
            margin-left: 0;
            margin-bottom: 1.5rem;
            font-style: italic;
            opacity: 0.8;
        }
        .ag-page-content code {
            background: rgba(34,211,238,0.08);
            color: var(--ag-primary);
            padding: 0.2rem 0.5rem;
            font-family: 'Space Mono', monospace;
            font-size: 0.875rem;
        }
        .ag-page-content pre {
            background: rgba(13,18,36,0.9);
            border: 1px solid rgba(34,211,238,0.2);
            padding: 1rem;
            overflow-x: auto;
            margin-bottom: 1.5rem;
        }
        .ag-page-content pre code { background: transparent; padding: 0; }
    </style>
@endsection
