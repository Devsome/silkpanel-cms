@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition mb-6">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8 mb-8">
                <h1 class="text-3xl font-bold text-white uppercase tracking-widest">{{ __('voting.title') }}</h1>
                <p class="mt-2 text-sm text-gray-400">{{ __('dashboard.voting_desc') }}</p>
            </div>

            @if ($sites->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($sites as $entry)
                        @php
                            $site = $entry['site'];
                            $canVote = $entry['can_vote'];
                            $nextVote = $entry['next_vote'];
                        @endphp
                        <div
                            class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur overflow-hidden flex flex-col">
                            @if ($site->image)
                                <div class="h-14 overflow-hidden flex items-center justify-center bg-gray-900/80">
                                    <img src="{{ $site->image }}" alt="{{ e($site->name) }}"
                                        class="h-full w-auto object-contain" loading="lazy">
                                </div>
                            @endif
                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="font-bold text-white uppercase tracking-wide text-sm">{{ e($site->name) }}</h3>
                                <div class="mt-2 flex items-center gap-2 text-sm text-gray-400">
                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                    {{ __('voting.reward') }}: <span
                                        class="text-emerald-400 font-semibold">{{ $site->reward }}</span>
                                </div>

                                <div class="mt-auto pt-4">
                                    @if ($canVote)
                                        <a href="{{ $site->url }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex w-full items-center justify-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-cyan-500 text-gray-950 text-xs font-bold uppercase tracking-widest hover:brightness-110 transition">
                                            {{ __('voting.vote_now') }}
                                        </a>
                                    @else
                                        <div class="text-center">
                                            <p class="text-xs text-gray-500">{{ __('voting.cooldown') }}</p>
                                            @if ($nextVote)
                                                <p class="mt-0.5 text-xs text-gray-600">{{ $nextVote->diffForHumans() }}
                                                </p>
                                            @endif
                                            <button disabled
                                                class="mt-2 inline-flex w-full items-center justify-center px-4 py-2 rounded-lg border border-gray-800 bg-gray-900/60 text-gray-600 text-xs font-bold uppercase tracking-widest cursor-not-allowed">
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
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-10 text-center">
                    <p class="text-gray-500">{{ __('voting.no_sites') }}</p>
                </div>
            @endif

        </div>
    </section>
@endsection
