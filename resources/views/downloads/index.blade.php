<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
                {{ __('downloads.title') }}
            </h1>

            @if ($downloads->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($downloads as $download)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700">
                            @if ($download->image)
                                <div class="aspect-video overflow-hidden">
                                    <img src="{{ asset('storage/' . $download->image) }}" alt="{{ e($download->name) }}"
                                        class="w-full h-full object-cover" loading="lazy">
                                </div>
                            @else
                                <div class="aspect-video bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                                    </svg>
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                    {{ e($download->name) }}
                                </h3>
                                @if ($download->description)
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ e($download->description) }}
                                    </p>
                                @endif
                                <a href="{{ e($download->link) }}" target="_blank" rel="noopener noreferrer"
                                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    {{ __('downloads.download') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('downloads.no_downloads') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
