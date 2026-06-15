@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">
            <a href="{{ route('dashboard') }}"
                class="mb-6 inline-flex items-center gap-2 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-muted hover:ag-text-primary transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            <div class="mb-8 ag-card-glow p-6 md:p-8">
                <p class="ag-section-eyebrow">{{ __('dashboard.voting_desc') }}</p>
                <h1 class="ag-section-title mt-2">{{ __('voting.title') }}</h1>
            </div>

            @if ($sites->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($sites as $entry)
                        @php
                            $site = $entry['site'];
                            $canVote = $entry['can_vote'];
                            $nextVote = $entry['next_vote'];
                        @endphp
                        <div class="ag-card overflow-hidden">
                            @if ($site->image)
                                <div class="h-14 overflow-hidden flex items-center justify-center"
                                    style="background:rgba(13,18,36,0.8);">
                                    <img src="{{ $site->image }}" alt="{{ e($site->name) }}"
                                        class="h-full w-auto object-contain" loading="lazy">
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="ag-font-display font-bold uppercase tracking-wide ag-text-surface">
                                    {{ e($site->name) }}
                                </h3>
                                <div class="mt-2 flex items-center gap-2 text-sm ag-text-muted">
                                    <svg class="h-4 w-4 ag-stat-amber" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                    {{ __('voting.reward') }}: {{ $site->reward }}
                                </div>

                                <div class="mt-4">
                                    @if ($canVote)
                                        <a href="{{ $site->url }}" target="_blank" rel="noopener noreferrer"
                                            class="ag-btn-primary inline-flex w-full items-center justify-center px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-widest">
                                            {{ __('voting.vote_now') }}
                                        </a>
                                    @else
                                        <div class="text-center">
                                            <p class="text-sm ag-text-muted">{{ __('voting.cooldown') }}</p>
                                            @if ($nextVote)
                                                <p class="mt-1 text-xs" style="color:rgba(34,211,238,0.5);">
                                                    {{ $nextVote->diffForHumans() }}
                                                </p>
                                            @endif
                                            <button disabled
                                                class="mt-2 inline-flex w-full items-center justify-center px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-widest cursor-not-allowed opacity-40"
                                                style="background:rgba(13,18,36,0.8);border:1px solid rgba(34,211,238,0.15);color:rgba(34,211,238,0.4);">
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
                <div class="ag-card p-10 text-center">
                    <p class="ag-text-muted">{{ __('voting.no_sites') }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection
