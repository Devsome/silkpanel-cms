@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <div class="bg-zinc-900 border border-violet-500/20">
                <div class="p-8">
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-2">
                        {{ __('pages.section_label') }}</p>
                    <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-wider text-white">
                        {{ e($translation->title) }}
                    </h1>
                    <div class="mt-4 h-px bg-linear-to-r from-violet-500/40 to-transparent"></div>
                </div>

                <div class="px-8 pb-8">
                    @if (is_array($translation->content))
                        <div class="space-y-4">
                            @foreach ($translation->content as $block)
                                @php
                                    $type = $block['type'] ?? null;
                                    $data = $block['data'] ?? [];
                                @endphp
                                @if ($type === 'heading')
                                    @php $tag = $data['level'] ?? 'h2'; @endphp
                                    <{{ $tag }} class="text-xl font-bold uppercase tracking-widest text-white mt-6">
                                        {{ $data['headline'] ?? ($data['text'] ?? '') }}
                                        </{{ $tag }}>
                                    @elseif ($type === 'paragraph')
                                        @php
                                            $paragraphText = $data['paragraph'] ?? ($data['text'] ?? '');
                                            if (is_array($paragraphText)) {
                                                $paragraphText = implode("\n", $paragraphText);
                                            }
                                        @endphp
                                        <p class="text-zinc-400 leading-relaxed">{!! nl2br(e($paragraphText)) !!}</p>
                                    @elseif ($type === 'rich_text')
                                        <div
                                            class="prose prose-invert prose-sm max-w-none
                                        prose-headings:font-bold prose-headings:uppercase prose-headings:tracking-widest prose-headings:text-white
                                        prose-p:text-zinc-400 prose-a:text-violet-400 prose-strong:text-zinc-200
                                        prose-code:text-fuchsia-400 prose-code:bg-zinc-800 prose-code:font-mono prose-code:text-xs
                                        prose-hr:border-zinc-800">
                                            {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($data['rich_text'] ?? '') }}
                                        </div>
                                    @elseif ($type === 'bbcode')
                                        @php
                                            $bbcode = $data['bbcode'] ?? '';
                                            if (is_array($bbcode)) {
                                                $bbcode = implode("\n", $bbcode);
                                            }
                                        @endphp
                                        <div
                                            class="prose prose-invert prose-sm max-w-none
                                        prose-headings:font-bold prose-headings:uppercase prose-headings:tracking-widest prose-headings:text-white
                                        prose-p:text-zinc-400 prose-a:text-violet-400 prose-strong:text-zinc-200
                                        prose-code:text-fuchsia-400 prose-code:bg-zinc-800 prose-code:font-mono prose-code:text-xs
                                        prose-hr:border-zinc-800">
                                            {!! str(app(\App\Services\BBCodeService::class)->toHtml($bbcode))->sanitizeHtml() !!}
                                        </div>
                                    @elseif ($type === 'image')
                                        @php
                                            $imageUrl = $data['url'] ?? '';
                                            if (is_array($imageUrl)) {
                                                $imageUrl = collect($imageUrl)->first() ?? '';
                                            }
                                        @endphp
                                        @if (!empty($imageUrl))
                                            <figure>
                                                <img src="{{ asset('storage/' . $imageUrl) }}"
                                                    alt="{{ e($data['alt'] ?? '') }}"
                                                    class="max-w-full border border-zinc-800">
                                            </figure>
                                        @endif
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div
                            class="prose prose-invert prose-sm max-w-none
                            prose-headings:font-bold prose-headings:uppercase prose-headings:tracking-widest prose-headings:text-white
                            prose-p:text-zinc-400 prose-a:text-violet-400 prose-strong:text-zinc-200
                            prose-code:text-fuchsia-400 prose-code:bg-zinc-800 prose-code:font-mono prose-code:text-xs
                            prose-hr:border-zinc-800">
                            {!! $translation->content !!}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>
@endsection
