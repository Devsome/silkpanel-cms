@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-3xl px-4 md:px-8">
            <h1 class="text-3xl font-bold text-white uppercase tracking-widest mb-8">
                {{ e($translation->title) }}
            </h1>

            <div
                class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8 prose prose-invert prose-emerald max-w-none">
                @if (is_array($translation->content))
                    @foreach ($translation->content as $block)
                        @php
                            $type = $block['type'] ?? null;
                            $data = $block['data'] ?? [];
                        @endphp

                        @if ($type === 'heading')
                            @php $tag = $data['level'] ?? 'h2'; @endphp
                            <{{ $tag }}>
                                {{ $data['headline'] ?? '' }}
                                @if (!empty($data['subheadline']))
                                    <small class="block text-base font-normal mt-2 text-gray-400">
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
                                <p>{!! nl2br(e($paragraphText)) !!}</p>
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
                                <figure class="my-8">
                                    <img src="{{ asset('storage/' . $imageUrl) }}" alt="{{ e($data['alt'] ?? '') }}"
                                        class="rounded-xl" loading="lazy">
                                    @if (!empty($data['alt']))
                                        <figcaption class="text-center mt-3 text-sm text-gray-500">{{ e($data['alt']) }}
                                        </figcaption>
                                    @endif
                                </figure>
                        @endif
                    @endforeach
                @elseif (is_string($translation->content) && !empty($translation->content))
                    {!! $translation->content !!}
                @endif
            </div>
        </div>
    </section>
@endsection
