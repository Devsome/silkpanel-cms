<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('news.index') }}"
                class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-6 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('news.back_to_list') }}
            </a>

            <article>
                @if ($news->thumbnail)
                    <div class="aspect-video rounded-lg overflow-hidden mb-6">
                        <img src="{{ asset('storage/' . $news->thumbnail) }}" alt="{{ e($news->name) }}"
                            class="w-full h-full object-cover">
                    </div>
                @endif

                <header class="mb-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->format('d M Y, H:i') : '' }}
                    </p>
                    <h1 class="mt-2 text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">
                        {{ e($news->name) }}
                    </h1>
                </header>

                <div class="prose prose-gray dark:prose-invert max-w-none">
                    {!! $news->content !!}
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
