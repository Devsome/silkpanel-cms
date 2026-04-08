@extends('template::layouts.app', ['title' => __('navigation.ranking_characters')])

@section('content')
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('navigation.rankings') }}</h1>
            </div>

            {{-- Ranking Navigation Pills --}}
            <div class="flex flex-wrap gap-2 mb-8">
                <a href="{{ route('ranking.characters') }}"
                    class="px-5 py-2.5 text-sm font-semibold rounded-full transition
                       {{ request()->routeIs('ranking.characters') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('navigation.ranking_characters') }}
                    </span>
                </a>
                <a href="{{ route('ranking.guilds') }}"
                    class="px-5 py-2.5 text-sm font-semibold rounded-full transition
                       {{ request()->routeIs('ranking.guilds') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('navigation.ranking_guilds') }}
                    </span>
                </a>
                <a href="{{ route('ranking.uniques') }}"
                    class="px-5 py-2.5 text-sm font-semibold rounded-full transition
                       {{ request()->routeIs('ranking.uniques') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        {{ __('navigation.ranking_uniques') }}
                    </span>
                </a>
            </div>

            <livewire:rankings.character-ranking />
        </div>
    </div>
@endsection
