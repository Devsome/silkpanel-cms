@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <div class="mb-8">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-1">
                    {{ __('voting.section_label') }}</p>
                <h1 class="text-3xl font-black uppercase tracking-widest text-white">{{ __('voting.title') }}</h1>
                <div class="mt-3 h-px bg-linear-to-r from-violet-500/40 to-transparent"></div>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 border border-violet-500/40 bg-violet-500/10 text-violet-300 text-sm font-mono">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 border border-red-500/40 bg-red-500/10 text-red-300 text-sm font-mono">
                    {{ session('error') }}
                </div>
            @endif

            @if ($sites->isEmpty())
                <div class="bg-zinc-900 border border-zinc-800 p-12 text-center">
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">{{ __('voting.no_sites') }}</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($sites as $item)
                        @php
                            $site = $item['site'];
                            $canVote = $item['can_vote'];
                            $nextVote = $item['next_vote'];
                        @endphp
                        <div
                            class="bg-zinc-900 border {{ $canVote ? 'border-violet-500/15 hover:border-violet-500/35' : 'border-zinc-800' }} transition p-5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 border {{ $canVote ? 'border-violet-500/30' : 'border-zinc-700' }} flex items-center justify-center flex-shrink-0 overflow-hidden {{ $site->image ? '' : '' }}">
                                    @if ($site->image)
                                        <img src="{{ e($site->image) }}" alt="{{ e($site->name) }}"
                                            class="w-full h-full object-contain {{ $canVote ? '' : 'opacity-30 grayscale' }}">
                                    @elseif ($canVote)
                                        <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-zinc-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p
                                        class="font-bold uppercase tracking-wider {{ $canVote ? 'text-zinc-200' : 'text-zinc-500' }} text-sm">
                                        {{ e($site->name) }}</p>
                                    @if ($site->reward)
                                        <p class="text-xs font-mono text-zinc-500 mt-0.5">+{{ $site->reward }}
                                            {{ __('voting.silk_reward') }}</p>
                                    @endif
                                    @if (!$canVote && $nextVote)
                                        <p class="text-xs font-mono text-zinc-700 mt-0.5">{{ __('voting.cooldown') }}
                                            {{ $nextVote->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </div>

                            @if ($canVote && $site->url)
                                <a href="{{ e($site->url) }}" target="_blank" rel="noopener noreferrer"
                                    class="flex-shrink-0 px-5 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_15px_rgba(139,92,246,0.3)] whitespace-nowrap">
                                    {{ __('voting.vote_now') }}
                                </a>
                            @elseif (!$canVote)
                                <span
                                    class="flex-shrink-0 px-5 py-2 text-xs font-mono uppercase tracking-[0.2em] text-zinc-600 border border-zinc-800 cursor-not-allowed whitespace-nowrap">
                                    {{ __('voting.cooldown') }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </section>
@endsection
