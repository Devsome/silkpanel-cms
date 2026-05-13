@extends('template::layouts.app')

@section('content')
    {{-- ═══════ HERO BANNER ═══════ --}}
    <section class="relative overflow-hidden border-b border-violet-500/20">
        <div class="absolute inset-0 pointer-events-none"
            style="background-image: linear-gradient(rgba(139,92,246,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(139,92,246,0.04) 1px, transparent 1px); background-size: 32px 32px;">
        </div>
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute right-0 top-0 w-1/2 h-full bg-gradient-to-l from-violet-600/8 to-transparent"></div>
            <div class="absolute right-1/4 top-1/2 -translate-y-1/2 w-80 h-80 bg-fuchsia-500/10 rounded-full blur-[80px]">
            </div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-end gap-0 min-h-[320px]">
                <div class="flex-1 py-10 lg:pr-8">
                    <a href="{{ route('ranking.characters') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-mono uppercase tracking-[0.2em] text-zinc-600 hover:text-violet-400 transition mb-6">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        {{ __('ranking.back_to_characters') }}
                    </a>
                    <p class="text-xs font-mono uppercase tracking-[0.4em] text-violet-400/60 mb-2">
                        {{ __('ranking.character') }}</p>
                    <h1 class="text-4xl sm:text-5xl font-black uppercase tracking-[0.08em] leading-none mb-4">
                        <span
                            class="bg-linear-to-r from-violet-300 via-fuchsia-300 to-cyan-300 bg-clip-text text-transparent">{{ e($character->CharName16) }}</span>
                    </h1>
                    @if ($character->guild && $character->guild->ID !== 0)
                        <a href="{{ route('ranking.guilds.show', $character->guild->ID) }}"
                            class="inline-flex items-center gap-2 px-3 py-1.5 border border-fuchsia-500/30 bg-fuchsia-500/10 text-fuchsia-300 text-xs font-mono uppercase tracking-wider hover:border-fuchsia-500/60 transition mb-4">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ e($character->guild->Name) }}
                        </a>
                    @endif
                    <div class="flex flex-wrap gap-2 mt-3">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 border border-cyan-500/30"><span
                                class="text-[10px] font-mono uppercase tracking-wider text-zinc-600">LVL</span><span
                                class="text-sm font-black font-mono text-cyan-400">{{ $character->CurLevel }}</span></div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 border border-violet-500/30"><span
                                class="text-[10px] font-mono uppercase tracking-wider text-zinc-600">STR</span><span
                                class="text-sm font-black font-mono text-violet-400">{{ number_format($character->Strength) }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 border border-fuchsia-500/30"><span
                                class="text-[10px] font-mono uppercase tracking-wider text-zinc-600">INT</span><span
                                class="text-sm font-black font-mono text-fuchsia-400">{{ number_format($character->Intellect) }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 border border-red-500/30"><span
                                class="text-[10px] font-mono uppercase tracking-wider text-zinc-600">HP</span><span
                                class="text-sm font-black font-mono text-red-400">{{ number_format($character->HP) }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 border border-violet-400/30"><span
                                class="text-[10px] font-mono uppercase tracking-wider text-zinc-600">MP</span><span
                                class="text-sm font-black font-mono text-violet-300">{{ number_format($character->MP) }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-zinc-950 border border-amber-500/30"><span
                                class="text-[10px] font-mono uppercase tracking-wider text-zinc-600">EXP</span><span
                                class="text-sm font-black font-mono text-amber-400">{{ $character->getCharRefLevelExperience() }}%</span>
                        </div>
                    </div>
                </div>
                <div class="relative lg:w-72 shrink-0 flex items-end justify-center">
                    <div
                        class="absolute bottom-0 w-72 h-64 bg-gradient-to-t from-violet-600/15 to-transparent rounded-full blur-3xl pointer-events-none">
                    </div>
                    <img src="{{ $characterImage2d }}" alt="{{ e($character->CharName16) }}"
                        class="relative z-10 h-72 w-auto object-contain object-bottom drop-shadow-[0_0_30px_rgba(139,92,246,0.4)]">
                </div>
            </div>
        </div>
    </section>

    {{-- EXP Bar --}}
    <div class="border-b border-violet-500/15 bg-zinc-950/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center gap-4">
                <span
                    class="text-[10px] font-mono uppercase tracking-[0.3em] text-zinc-600 shrink-0">{{ __('ranking.experience') }}</span>
                <div class="flex-1 h-1.5 bg-zinc-900 overflow-hidden">
                    <div class="h-full bg-linear-to-r from-violet-500 via-fuchsia-500 to-cyan-500 transition-all shadow-[0_0_8px_rgba(139,92,246,0.6)]"
                        style="width: {{ $character->getCharRefLevelExperience() }}%"></div>
                </div>
                <span
                    class="text-xs font-mono font-bold text-amber-400 shrink-0">{{ $character->getCharRefLevelExperience() }}%</span>
            </div>
        </div>
    </div>

    {{-- Equipment + Avatar --}}
    <section class="py-10" x-data="{ tab: 'equipment' }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-0 mb-6 border-b border-zinc-800">
                <button @click="tab = 'equipment'"
                    :class="tab === 'equipment' ? 'border-b-2 border-violet-500 text-violet-400 bg-violet-500/5' :
                        'border-b-2 border-transparent text-zinc-600 hover:text-zinc-300'"
                    class="px-6 py-3 text-xs font-mono font-bold uppercase tracking-[0.25em] -mb-px transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {{ __('ranking.equipment') }}
                </button>
                <button @click="tab = 'avatar'"
                    :class="tab === 'avatar' ? 'border-b-2 border-fuchsia-500 text-fuchsia-400 bg-fuchsia-500/5' :
                        'border-b-2 border-transparent text-zinc-600 hover:text-zinc-300'"
                    class="px-6 py-3 text-xs font-mono font-bold uppercase tracking-[0.25em] -mb-px transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('ranking.avatar') }}
                </button>
            </div>

            <div x-show="tab === 'equipment'" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="border border-violet-500/15 bg-zinc-900/60 p-6">
                    @include('template::ranking.partials.equipment', ['equipment' => $equipment])
                </div>
            </div>
            <div x-show="tab === 'avatar'" x-cloak x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="border border-fuchsia-500/15 bg-zinc-900/60 p-6">
                    @include('template::ranking.partials.avatar', ['avatar' => $avatar])
                </div>
            </div>
        </div>
    </section>
@endsection
