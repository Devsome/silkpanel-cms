@extends('template::layouts.app')

@section('content')
    @php
        $customRankings = collect(\App\Models\Setting::get('ranking_custom_rankings', []))
            ->filter(fn($row) => is_array($row) && ($row['enabled'] ?? true) && filled($row['key'] ?? '') && filled($row['title'] ?? ''))
            ->values();
    @endphp
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">

            {{-- Page header --}}
            <div class="mb-8">
                <p class="ag-section-eyebrow">{{ __('navigation.rankings') }}</p>
                <h1 class="ag-section-title mt-2">{{ __('navigation.ranking_uniques') }}</h1>
            </div>

            {{-- Tab navigation --}}
            <div class="flex overflow-x-auto border-b ag-divider mb-8 gap-1">
                <a href="{{ route('ranking.characters') }}" class="ag-tab {{ request()->routeIs('ranking.characters') ? 'active' : '' }}">
                    {{ __('navigation.ranking_characters') }}
                </a>
                <a href="{{ route('ranking.guilds') }}" class="ag-tab {{ request()->routeIs('ranking.guilds') ? 'active' : '' }}">
                    {{ __('navigation.ranking_guilds') }}
                </a>
                <a href="{{ route('ranking.uniques') }}" class="ag-tab {{ request()->routeIs('ranking.uniques') ? 'active' : '' }}">
                    {{ __('navigation.ranking_uniques') }}
                </a>
                @foreach ($customRankings as $customRanking)
                    <a href="{{ route('ranking.custom', ['key' => $customRanking['key']]) }}"
                        class="ag-tab {{ request()->routeIs('ranking.custom') && request()->query('key') === $customRanking['key'] ? 'active' : '' }}">
                        {{ e($customRanking['title']) }}
                    </a>
                @endforeach
            </div>

            <livewire:rankings.unique-ranking />
        </div>
    </section>
@endsection
