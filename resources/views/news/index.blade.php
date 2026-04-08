<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
                {{ __('news.title') }}
            </h1>

            @if ($news->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($news as $item)
                        <a href="{{ route('news.show', $item->slug) }}"
                            class="group bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition">
                            @if ($item->thumbnail)
                                <div class="aspect-video overflow-hidden">
                                    <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ e($item->name) }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                        loading="lazy">
                                </div>
                            @else
                                <div class="aspect-video bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="p-4">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('d M Y') : '' }}
                                </p>
                                <h3
                                    class="mt-1 font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition line-clamp-2">
                                    {{ e($item->name) }}
                                </h3>
                                @if ($item->content)
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                        {{ Str::limit(strip_tags($item->content), 120) }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $news->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('news.no_news') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
