@extends('template::layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Ranking Pill Navigation --}}
            <div class="mb-8 flex items-center gap-2 rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-1.5">
                <a href="{{ route('ranking.characters') }}"
                    class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-semibold rounded-xl transition
                        {{ request()->routeIs('ranking.characters') ? 'bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 shadow-lg shadow-emerald-500/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('navigation.ranking_characters') }}
                    </span>
                </a>
                <a href="{{ route('ranking.guilds') }}"
                    class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-semibold rounded-xl transition
                        {{ request()->routeIs('ranking.guilds') ? 'bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 shadow-lg shadow-emerald-500/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ __('navigation.ranking_guilds') }}
                    </span>
                </a>
                <a href="{{ route('ranking.uniques') }}"
                    class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-semibold rounded-xl transition
                        {{ request()->routeIs('ranking.uniques') ? 'bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 shadow-lg shadow-emerald-500/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        {{ __('navigation.ranking_uniques') }}
                    </span>
                </a>
            </div>

            <livewire:rankings.guild-ranking />
        </div>
    </div>
@endsection
