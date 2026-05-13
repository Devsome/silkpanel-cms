@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <h1 class="text-3xl font-bold text-white uppercase tracking-widest mb-8">
                {{ __('news.title') }}
            </h1>

            @if ($news->count() === 0)
                <div class="text-center py-20">
                    <svg class="mx-auto h-12 w-12 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <p class="mt-4 text-gray-500">{{ __('news.no_news') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($news as $item)
                        <a href="{{ route('news.show', $item->slug) }}"
                            class="group flex flex-col rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur overflow-hidden hover:border-emerald-500/40 transition">
                            @if ($item->thumbnail)
                                <div class="aspect-video overflow-hidden bg-black/20">
                                    <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ e($item->name) }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                        loading="lazy">
                                </div>
                            @else
                                <div
                                    class="aspect-video flex items-center justify-center bg-linear-to-br from-emerald-900/30 to-cyan-900/20">
                                    <svg class="w-10 h-10 text-emerald-700" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex flex-col flex-1 p-5">
                                <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70 mb-2">
                                    {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('d M Y') : '' }}
                                </p>
                                <h2
                                    class="text-base font-bold text-white group-hover:text-emerald-300 transition line-clamp-2 mb-2">
                                    {{ e($item->name) }}
                                </h2>
                                @if ($item->content)
                                    <p class="text-sm text-gray-400 line-clamp-3 flex-1">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($item->content), 160) }}
                                    </p>
                                @endif
                                <p class="mt-4 text-xs text-gray-600">
                                    {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->diffForHumans() : $item->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($news->hasPages())
                    <div class="mt-8">
                        {{ $news->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
