@extends('template::layouts.app')

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-400 px-4 md:px-8">
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
            </div>

            <livewire:rankings.character-ranking />
        </div>
    </div>
@endsection
