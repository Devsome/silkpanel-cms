@extends('template::layouts.app')

@section('content')
    <main class="min-h-screen gp-page-background py-12 px-4 sm:px-6 lg:px-8">
        <article class="max-w-4xl mx-auto">
            <!-- Header -->
            <header class="mb-12 pb-8 border-b-2" style="border-color: var(--gp-primary);">
                <h1 class="text-4xl md:text-5xl font-bold font-headline mb-4" style="color: var(--gp-primary);">
                    {{ e($translation->title) }}
                </h1>
                @if ($translation->seo_description)
                    <p class="text-lg md:text-xl" style="color: var(--gp-surface-200);">
                        {{ e($translation->seo_description) }}
                    </p>
                @endif
            </header>

            <!-- Content -->
            <div class="prose prose-invert max-w-none gp-page-content">
                @if (is_array($translation->content))
                    @foreach ($translation->content as $block)
                        @php
                            $type = $block['type'] ?? null;
                            $data = $block['data'] ?? [];
                        @endphp

                        @if ($type === 'heading')
                            @php $tag = $data['level'] ?? 'h2'; @endphp
                            <{{ $tag }} class="font-headline mt-12 mb-4" style="color: var(--gp-primary);">
                                {{ $data['headline'] ?? '' }}
                                @if (!empty($data['subheadline']))
                                    <small class="block text-base font-normal mt-2" style="color: var(--gp-surface-200);">
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
                                <p class="text-base leading-7 mb-6" style="color: var(--gp-surface-300);">
                                    {!! nl2br(e($paragraphText)) !!}
                                </p>
                            @elseif ($type === 'image')
                                @php
                                    $imageUrl = $data['url'] ?? '';
                                    if (is_array($imageUrl)) {
                                        $imageUrl = collect($imageUrl)->first() ?? '';
                                    }
                                @endphp
                                <figure class="my-8 p-4 gp-ornate-border"
                                    style="background-color: var(--gp-surface-800); border-color: var(--gp-primary);">
                                    <img src="{{ asset('storage/' . $imageUrl) }}" alt="{{ e($data['alt'] ?? '') }}"
                                        class="w-full rounded-lg" loading="lazy">
                                    @if (!empty($data['alt']))
                                        <figcaption class="text-center mt-4 text-sm" style="color: var(--gp-surface-300);">
                                            {{ e($data['alt']) }}
                                        </figcaption>
                                    @endif
                                </figure>
                        @endif
                    @endforeach
                @elseif (is_string($translation->content) && !empty($translation->content))
                    <div class="prose prose-invert max-w-none" style="color: var(--gp-surface-300);">
                        {!! $translation->content !!}
                    </div>
                @endif
            </div>
        </article>
    </main>

    <style>
        .gp-page-background {
            background-color: var(--gp-background);
        }

        .gp-page-content h1 {
            font-size: 2.25rem;
            line-height: 2.5rem;
            margin-bottom: 1rem;
            color: var(--gp-primary);
        }

        .gp-page-content h2 {
            font-size: 1.875rem;
            line-height: 2.25rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: var(--gp-primary);
        }

        .gp-page-content h3 {
            font-size: 1.5rem;
            line-height: 2rem;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: var(--gp-primary);
        }

        .gp-page-content h4,
        .gp-page-content h5,
        .gp-page-content h6 {
            font-size: 1.125rem;
            line-height: 1.75rem;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            color: var(--gp-primary);
        }

        .gp-page-content p {
            color: var(--gp-surface-300);
            margin-bottom: 1.5rem;
            line-height: 1.75;
        }

        .gp-page-content ul,
        .gp-page-content ol {
            margin-left: 2rem;
            margin-bottom: 1.5rem;
        }

        .gp-page-content li {
            color: var(--gp-surface-300);
            margin-bottom: 0.5rem;
        }

        .gp-page-content strong {
            color: var(--gp-primary);
            font-weight: 600;
        }

        .gp-page-content em {
            color: var(--gp-surface-200);
            font-style: italic;
        }

        .gp-page-content a {
            color: var(--gp-primary);
            text-decoration: none;
            border-bottom: 1px solid var(--gp-primary);
            transition: all 0.3s ease;
        }

        .gp-page-content a:hover {
            color: var(--gp-surface-200);
            border-bottom-color: var(--gp-surface-200);
        }

        .gp-page-content blockquote {
            border-left: 4px solid var(--gp-primary);
            padding-left: 1.5rem;
            margin-left: 0;
            margin-bottom: 1.5rem;
            color: var(--gp-surface-200);
            font-style: italic;
        }

        .gp-page-content code {
            background-color: var(--gp-surface-800);
            color: var(--gp-primary);
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-family: monospace;
            font-size: 0.875rem;
        }

        .gp-page-content pre {
            background-color: var(--gp-surface-800);
            border: 1px solid var(--gp-primary);
            border-radius: 0.5rem;
            padding: 1rem;
            overflow-x: auto;
            margin-bottom: 1.5rem;
        }

        .gp-page-content pre code {
            background-color: transparent;
            padding: 0;
        }
    </style>
@endsection
