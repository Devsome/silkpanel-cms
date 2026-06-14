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
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Ranking Tab Nav --}}
            <div class="mb-6 flex items-center gap-1 border-b border-zinc-800">
                @foreach ([['route' => 'ranking.characters', 'label' => __('navigation.ranking_characters')], ['route' => 'ranking.guilds', 'label' => __('navigation.ranking_guilds')], ['route' => 'ranking.uniques', 'label' => __('navigation.ranking_uniques')]] as $tab)
                    <a href="{{ route($tab['route']) }}"
                        class="px-5 py-2.5 text-xs font-mono font-bold uppercase tracking-widest whitespace-nowrap border-b-2 -mb-px transition
                            {{ request()->routeIs($tab['route']) ? 'border-violet-500 text-violet-400' : 'border-transparent text-zinc-500 hover:text-zinc-200 hover:border-violet-700/50' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
                @forelse ($customRankings as $customRanking)
                    <a href="{{ route('ranking.custom', ['key' => $customRanking['key']]) }}"
                        class="px-5 py-2.5 text-xs font-mono font-bold uppercase tracking-widest whitespace-nowrap border-b-2 -mb-px transition
                            {{ $activeCustomKey === ($customRanking['key'] ?? null) ? 'border-violet-500 text-violet-400' : 'border-transparent text-zinc-500 hover:text-zinc-200 hover:border-violet-700/50' }}">
                        {{ e($customRanking['title']) }}
                    </a>
                @empty
                    <a href="{{ route('ranking.custom') }}"
                        class="px-5 py-2.5 text-xs font-mono font-bold uppercase tracking-widest whitespace-nowrap border-b-2 -mb-px transition border-transparent text-zinc-500 hover:text-zinc-200 hover:border-violet-700/50">
                        {{ __('navigation.ranking_custom') }}
                    </a>
                @endforelse
            </div>

            <livewire:rankings.custom-ranking :ranking-key="$rankingKey ?? null" />
        </div>
    </section>
@endsection
