@extends('template::layouts.app')

@section('content')
    @php
        $customRankings = collect(\App\Models\Setting::get('ranking_custom_rankings', []))
            ->filter(
                fn($row) => is_array($row) &&
                    ($row['enabled'] ?? true) &&
                    filled($row['key'] ?? '') &&
                    filled($row['title'] ?? ''),
            )
            ->values();
        $activeCustomKey = filled($rankingKey ?? null) ? $rankingKey : (string) ($customRankings->first()['key'] ?? '');
    @endphp
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Ranking Pill Navigation --}}
            <div class="mb-8 flex items-center gap-2 rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-1.5">
                <a href="{{ route('ranking.characters') }}"
                    class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-semibold rounded-xl transition
                        {{ request()->routeIs('ranking.characters') ? 'bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 shadow-lg shadow-emerald-500/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="inline-flex items-center gap-1.5">{{ __('navigation.ranking_characters') }}</span>
                </a>
                <a href="{{ route('ranking.guilds') }}"
                    class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-semibold rounded-xl transition
                        {{ request()->routeIs('ranking.guilds') ? 'bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 shadow-lg shadow-emerald-500/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="inline-flex items-center gap-1.5">{{ __('navigation.ranking_guilds') }}</span>
                </a>
                <a href="{{ route('ranking.uniques') }}"
                    class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-semibold rounded-xl transition
                        {{ request()->routeIs('ranking.uniques') ? 'bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 shadow-lg shadow-emerald-500/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span class="inline-flex items-center gap-1.5">{{ __('navigation.ranking_uniques') }}</span>
                </a>
                @forelse ($customRankings as $customRanking)
                    <a href="{{ route('ranking.custom', ['key' => $customRanking['key']]) }}"
                        class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-semibold rounded-xl transition
                            {{ $activeCustomKey === ($customRanking['key'] ?? null) ? 'bg-linear-to-r from-emerald-500 to-cyan-500 text-gray-950 shadow-lg shadow-emerald-500/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <span class="inline-flex items-center gap-1.5">{{ e($customRanking['title']) }}</span>
                    </a>
                @empty
                    <a href="{{ route('ranking.custom') }}"
                        class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-semibold rounded-xl transition text-gray-400 hover:text-white hover:bg-white/5">
                        <span class="inline-flex items-center gap-1.5">{{ __('navigation.ranking_custom') }}</span>
                    </a>
                @endforelse
            </div>

            <livewire:rankings.custom-ranking :ranking-key="$rankingKey ?? null" />
        </div>
    </div>
@endsection
