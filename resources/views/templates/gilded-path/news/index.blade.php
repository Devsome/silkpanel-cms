@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <div class="mb-8 gp-card gp-ornate-border p-6 md:p-8">
                <h1 class="text-3xl md:text-4xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('news.title') }}
                </h1>
                <p class="mt-3 text-sm md:text-base gp-text-on-surface-variant">
                    {{ __('index.welcome_text') }}
                </p>
            </div>

            @if ($news->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($news as $item)
                        <a href="{{ route('news.show', $item->slug) }}"
                            class="group gp-card gp-ornate-border overflow-hidden transition-all duration-300 hover:-translate-y-0.5 hover:shadow-2xl">
                            @if ($item->thumbnail)
                                <div class="aspect-video overflow-hidden bg-black/20">
                                    <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ e($item->name) }}"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                        loading="lazy">
                                </div>
                            @else
                                <div class="aspect-video flex items-center justify-center gp-card-lowest">
                                    <svg class="h-12 w-12 gp-text-outline" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            @endif

                            <div class="p-5">
                                <p class="text-xs font-headline uppercase tracking-wider gp-text-outline">
                                    {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('d M Y') : '' }}
                                </p>
                                <h2
                                    class="mt-2 line-clamp-2 text-lg font-headline font-bold uppercase tracking-wide gp-text-on-surface transition-colors group-hover:gp-text-primary">
                                    {{ e($item->name) }}
                                </h2>

                                @if ($item->content)
                                    <p class="mt-3 line-clamp-3 text-sm leading-relaxed gp-text-on-surface-variant">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($item->content), 160) }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($news->hasPages())
                    <div class="mt-8 gp-card gp-ghost-border p-4">
                        {{ $news->links() }}
                    </div>
                @endif
            @else
                <div class="gp-card gp-ornate-border p-10 text-center">
                    <p class="gp-text-on-surface-variant">{{ __('news.no_news') }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection
