<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-1 mb-8 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('ranking.characters') }}"
                    class="px-4 py-2 text-sm font-medium rounded-t-md transition
                       {{ request()->routeIs('ranking.characters') ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 border border-b-0 border-gray-200 dark:border-gray-700 -mb-px' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    {{ __('navigation.ranking_characters') }}
                </a>
                <a href="{{ route('ranking.guilds') }}"
                    class="px-4 py-2 text-sm font-medium rounded-t-md transition
                       {{ request()->routeIs('ranking.guilds') ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 border border-b-0 border-gray-200 dark:border-gray-700 -mb-px' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    {{ __('navigation.ranking_guilds') }}
                </a>
                <a href="{{ route('ranking.uniques') }}"
                    class="px-4 py-2 text-sm font-medium rounded-t-md transition
                       {{ request()->routeIs('ranking.uniques') ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 border border-b-0 border-gray-200 dark:border-gray-700 -mb-px' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    {{ __('navigation.ranking_uniques') }}
                </a>
            </div>

            <livewire:rankings.unique-ranking />
        </div>
    </div>
</x-app-layout>
