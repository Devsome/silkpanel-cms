<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <article>
                <header class="mb-6">
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">
                        {{ e($translation->title) }}
                    </h1>
                    @if ($translation->seo_description)
                        <p class="mt-2 text-lg text-gray-500 dark:text-gray-400">
                            {{ e($translation->seo_description) }}
                        </p>
                    @endif
                </header>

                <div class="prose prose-gray dark:prose-invert max-w-none">
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
                                        <small
                                            class="block text-base font-normal text-gray-500 dark:text-gray-400 mt-1">{{ $data['subheadline'] }}</small>
                                    @endif
                                    </{{ $tag }}>
                                @elseif ($type === 'paragraph')
                                    @php
                                        $paragraphText = $data['paragraph'] ?? '';
                                        if (is_array($paragraphText)) {
                                            $paragraphText = implode("\n", $paragraphText);
                                        }
                                    @endphp
                                    {!! nl2br(e($paragraphText)) !!}
                                @elseif ($type === 'image')
                                    @php
                                        $imageUrl = $data['url'] ?? '';
                                        if (is_array($imageUrl)) {
                                            $imageUrl = collect($imageUrl)->first() ?? '';
                                        }
                                    @endphp
                                    <figure>
                                        <img src="{{ asset('storage/' . $imageUrl) }}" alt="{{ e($data['alt'] ?? '') }}"
                                            class="rounded-lg" loading="lazy">
                                        @if (!empty($data['alt']))
                                            <figcaption>{{ e($data['alt']) }}</figcaption>
                                        @endif
                                    </figure>
                            @endif
                        @endforeach
                    @elseif (is_string($translation->content) && !empty($translation->content))
                        {!! $translation->content !!}
                    @endif
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
