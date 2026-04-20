<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('voting.title') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mb-6 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>
            @if ($sites->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($sites as $entry)
                        @php
                            $site = $entry['site'];
                            $canVote = $entry['can_vote'];
                            $nextVote = $entry['next_vote'];
                        @endphp
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                            @if ($site->image)
                                <div
                                    class="h-12 overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <img src="{{ $site->image }}" alt="{{ e($site->name) }}"
                                        class="h-full w-auto object-contain" loading="lazy">
                                </div>
                            @endif
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                    {{ e($site->name) }}
                                </h3>
                                <div class="mt-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                    {{ __('voting.reward') }}: {{ $site->reward }}
                                </div>

                                <div class="mt-4">
                                    @if ($canVote)
                                        <a href="{{ $site->url }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition">
                                            {{ __('voting.vote_now') }}
                                        </a>
                                    @else
                                        <div class="text-center">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('voting.cooldown') }}
                                            </p>
                                            @if ($nextVote)
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                    {{ $nextVote->diffForHumans() }}
                                                </p>
                                            @endif
                                            <button disabled
                                                class="mt-2 inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-700 dark:text-gray-500 rounded-md cursor-not-allowed">
                                                {{ __('voting.vote_now') }}
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('voting.no_sites') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
