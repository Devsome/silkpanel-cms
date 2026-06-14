@extends('template::layouts.app')

@section('content')
    @php
        $customRankings = collect(\App\Models\Setting::get('ranking_custom_rankings', []))
            ->filter(fn($row) => is_array($row) && ($row['enabled'] ?? true) && filled($row['key'] ?? '') && filled($row['title'] ?? ''))
            ->values();
    @endphp
    <div class="py-8">
        <div class="mx-auto max-w-[1600px] px-4 md:px-8">
            {{-- Ranking Navigation --}}
            <div class="mb-8 flex items-center gap-2 gp-card p-1.5">
                <a href="{{ route('ranking.characters') }}"
                    class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-bold font-headline uppercase tracking-widest transition
                        {{ request()->routeIs('ranking.characters') ? 'gp-gold-btn shadow-lg' : 'gp-text-on-surface-variant hover:text-yellow-400 hover:bg-yellow-900/10' }}">
                    <span class="inline-flex items-center gap-1.5">
                        {{ __('navigation.ranking_characters') }}
                    </span>
                </a>
                <a href="{{ route('ranking.guilds') }}"
                    class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-bold font-headline uppercase tracking-widest transition
                        {{ request()->routeIs('ranking.guilds') ? 'gp-gold-btn shadow-lg' : 'gp-text-on-surface-variant hover:text-yellow-400 hover:bg-yellow-900/10' }}">
                    <span class="inline-flex items-center gap-1.5">
                        {{ __('navigation.ranking_guilds') }}
                    </span>
                </a>
                <a href="{{ route('ranking.uniques') }}"
                    class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-bold font-headline uppercase tracking-widest transition
                        {{ request()->routeIs('ranking.uniques') ? 'gp-gold-btn shadow-lg' : 'gp-text-on-surface-variant hover:text-yellow-400 hover:bg-yellow-900/10' }}">
                    <span class="inline-flex items-center gap-1.5">
                        {{ __('navigation.ranking_uniques') }}
                    </span>
                </a>
                @foreach ($customRankings as $customRanking)
                    <a href="{{ route('ranking.custom', ['key' => $customRanking['key']]) }}"
                        class="flex-1 sm:flex-none px-5 py-2 text-center text-sm font-bold font-headline uppercase tracking-widest transition
                            {{ request()->routeIs('ranking.custom') && request()->query('key') === $customRanking['key'] ? 'gp-gold-btn shadow-lg' : 'gp-text-on-surface-variant hover:text-yellow-400 hover:bg-yellow-900/10' }}">
                        <span class="inline-flex items-center gap-1.5">{{ e($customRanking['title']) }}</span>
                    </a>
                @endforeach
            </div>

            <livewire:rankings.guild-ranking />
        </div>
    </div>
@endsection
