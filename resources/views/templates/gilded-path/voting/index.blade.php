@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <div class="mb-8 gp-card gp-ornate-border p-6 md:p-8">
                <h1 class="text-3xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('voting.title') }}</h1>
                <p class="mt-2 text-sm gp-text-on-surface-variant">{{ __('dashboard.voting_desc') }}</p>
            </div>

            @if ($sites->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($sites as $entry)
                        @php
                            $site = $entry['site'];
                            $canVote = $entry['can_vote'];
                            $nextVote = $entry['next_vote'];
                        @endphp
                        <div class="gp-card gp-ornate-border overflow-hidden">
                            @if ($site->image)
                                <div class="h-14 overflow-hidden gp-card-lowest flex items-center justify-center">
                                    <img src="{{ $site->image }}" alt="{{ e($site->name) }}"
                                        class="h-full w-auto object-contain" loading="lazy">
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="font-headline font-bold uppercase tracking-wide gp-text-on-surface">
                                    {{ e($site->name) }}</h3>
                                <div class="mt-2 flex items-center gap-2 text-sm gp-text-on-surface-variant">
                                    <svg class="h-4 w-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                    {{ __('voting.reward') }}: {{ $site->reward }}
                                </div>

                                <div class="mt-4">
                                    @if ($canVote)
                                        <a href="{{ $site->url }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex w-full items-center justify-center px-4 py-2 text-xs font-headline font-bold uppercase tracking-widest gp-gold-btn">
                                            {{ __('voting.vote_now') }}
                                        </a>
                                    @else
                                        <div class="text-center">
                                            <p class="text-sm gp-text-on-surface-variant">{{ __('voting.cooldown') }}</p>
                                            @if ($nextVote)
                                                <p class="mt-1 text-xs gp-text-outline">{{ $nextVote->diffForHumans() }}
                                                </p>
                                            @endif
                                            <button disabled
                                                class="mt-2 inline-flex w-full items-center justify-center px-4 py-2 text-xs font-headline font-bold uppercase tracking-widest gp-card-lowest gp-text-outline cursor-not-allowed"
                                                style="border:1px solid rgba(77,70,53,0.4);">
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
                <div class="gp-card gp-ornate-border p-10 text-center">
                    <p class="gp-text-on-surface-variant">{{ __('voting.no_sites') }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection
