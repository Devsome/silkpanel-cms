@extends('template::layouts.app')

@push('styles')
    <style>
        @keyframes ag-float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes ag-scan {
            0% {
                transform: translateY(-100%);
                opacity: 0;
            }

            10% {
                opacity: 0.4;
            }

            90% {
                opacity: 0.4;
            }

            100% {
                transform: translateY(100%);
                opacity: 0;
            }
        }

        @keyframes ag-glow-pulse {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(34, 211, 238, 0.15), inset 0 0 20px rgba(34, 211, 238, 0.05);
            }

            50% {
                box-shadow: 0 0 40px rgba(34, 211, 238, 0.3), inset 0 0 30px rgba(34, 211, 238, 0.1);
            }
        }

        @keyframes ag-bar-fill {
            from {
                width: 0%;
            }
        }

        .ag-char-float {
            animation: ag-float 4s ease-in-out infinite;
        }

        .ag-portrait-glow {
            animation: ag-glow-pulse 3s ease-in-out infinite;
        }

        .ag-scan-line {
            animation: ag-scan 3s linear infinite;
            background: linear-gradient(to bottom, transparent, rgba(34, 211, 238, 0.15), transparent);
            height: 30%;
            width: 100%;
            position: absolute;
            left: 0;
            pointer-events: none;
        }

        .ag-stat-bar {
            animation: ag-bar-fill 1s cubic-bezier(0.4, 0, 0.2, 1) both;
        }

        .ag-slot-btn {
            position: relative;
            display: inline-flex;
            width: 56px;
            height: 56px;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(34, 211, 238, 0.2);
            background: rgba(6, 8, 15, 0.8);
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
            cursor: pointer;
        }

        .ag-slot-btn:hover {
            border-color: rgba(34, 211, 238, 0.6);
            box-shadow: 0 0 12px rgba(34, 211, 238, 0.2);
            transform: scale(1.05);
            z-index: 1;
        }

        .ag-slot-btn.has-item {
            border-color: rgba(34, 211, 238, 0.35);
            background: rgba(9, 12, 23, 0.9);
        }

        .ag-slot-empty {
            width: 44px;
            height: 44px;
            border: 1px dashed rgba(34, 211, 238, 0.12);
        }

        /* Rarity glow colors */
        .ag-item-sox {
            border-color: rgba(251, 191, 36, 0.5) !important;
            box-shadow: 0 0 8px rgba(251, 191, 36, 0.2) !important;
        }

        .ag-item-blue {
            border-color: rgba(96, 165, 250, 0.5) !important;
            box-shadow: 0 0 8px rgba(96, 165, 250, 0.15) !important;
        }

        .ag-hero-bg {
            background:
                radial-gradient(ellipse 60% 80% at 70% 50%, rgba(6, 24, 56, 0.9) 0%, rgba(6, 8, 15, 0.97) 70%),
                radial-gradient(ellipse 30% 50% at 30% 50%, rgba(34, 211, 238, 0.04) 0%, transparent 70%);
        }
    </style>
@endpush

@section('content')
    @php
        $maxHP = max((int) ($character->MaxHP ?? ($character->HP ?? 1)), 1);
        $maxMP = max((int) ($character->MaxMP ?? ($character->MP ?? 1)), 1);
        $hpPct = min(100, round(($character->HP / $maxHP) * 100));
        $mpPct = min(100, round(($character->MP / $maxMP) * 100));
        $expPct = min(100, (float) $character->getCharRefLevelExperience());
    @endphp

    {{-- ═══ HERO BANNER ═══ --}}
    <div class="relative overflow-hidden ag-hero-bg" style="min-height: 320px;">

        {{-- Geometric background lines --}}
        <svg class="absolute inset-0 w-full h-full opacity-[0.04]" viewBox="0 0 1400 320" preserveAspectRatio="xMidYMid slice">
            <line x1="0" y1="160" x2="1400" y2="160" stroke="#22d3ee" stroke-width="0.5" />
            <line x1="700" y1="0" x2="700" y2="320" stroke="#22d3ee" stroke-width="0.5" />
            <circle cx="700" cy="160" r="120" fill="none" stroke="#22d3ee" stroke-width="0.5" />
            <circle cx="700" cy="160" r="250" fill="none" stroke="#22d3ee" stroke-width="0.5" />
            <line x1="0" y1="0" x2="350" y2="320" stroke="#22d3ee" stroke-width="0.5" />
            <line x1="1400" y1="0" x2="1050" y2="320" stroke="#22d3ee" stroke-width="0.5" />
        </svg>

        {{-- Left: Info --}}
        <div class="relative mx-auto max-w-[1600px] px-4 md:px-8 py-10 flex flex-col justify-between h-full"
            style="min-height: 320px;">

            {{-- Back link --}}
            <a href="{{ route('ranking.characters') }}"
                class="inline-flex items-center gap-2 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-muted hover:ag-text-primary transition-colors mb-8 w-fit">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('ranking.back_to_characters') }}
            </a>

            {{-- Character identity --}}
            <div class="max-w-xl">
                {{-- Rank badge --}}
                @if (isset($rank))
                    <div class="inline-flex items-center gap-2 mb-4 px-3 py-1 ag-card-low">
                        <span class="ag-font-mono text-xs ag-text-muted">#</span>
                        <span
                            class="ag-font-mono text-sm font-bold
                            {{ $rank === 1 ? 'ag-rank-1' : ($rank === 2 ? 'ag-rank-2' : ($rank === 3 ? 'ag-rank-3' : 'ag-text-primary')) }}">
                            {{ $rank }}
                        </span>
                        <span
                            class="text-xs ag-text-muted ag-font-display uppercase tracking-widest">{{ __('ranking.rank') }}</span>
                    </div>
                @endif

                <h1 class="ag-font-display font-bold ag-text-surface leading-none mb-3"
                    style="font-size: clamp(2rem, 5vw, 3.5rem); letter-spacing: 0.04em;">
                    {{ e($character->CharName16) }}
                </h1>

                <div class="flex flex-wrap items-center gap-4 mb-6">
                    {{-- Level --}}
                    <div class="flex items-center gap-2">
                        <span class="ag-section-eyebrow">{{ __('ranking.level') }}</span>
                        <span class="ag-font-mono text-2xl font-bold ag-text-primary">{{ $character->CurLevel }}</span>
                    </div>

                    @if ($character->guild && $character->guild->ID !== 0)
                        <div class="h-4 w-px" style="background: var(--ag-outline);"></div>
                        {{-- Guild --}}
                        <a href="{{ route('ranking.guilds.show', $character->guild->slug) }}"
                            class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                            <svg class="w-4 h-4 ag-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm font-semibold ag-text-secondary">{{ e($character->guild->Name) }}</span>
                        </a>
                    @endif
                </div>

                {{-- EXP bar --}}
                <div class="max-w-sm">
                    <div class="flex justify-between items-center mb-1.5">
                        <span
                            class="text-xs ag-font-display font-semibold tracking-widest uppercase ag-text-muted">EXP</span>
                        <span class="ag-font-mono text-xs ag-text-primary">{{ $expPct }}%</span>
                    </div>
                    <div class="h-1.5 w-full" style="background: rgba(34,211,238,0.1);">
                        <div class="h-full ag-stat-bar"
                            style="width: {{ $expPct }}%; background: linear-gradient(90deg, var(--ag-primary-dim), var(--ag-primary));">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom accent line --}}
        <div class="absolute bottom-0 left-0 right-0 h-px"
            style="background: linear-gradient(90deg, transparent, rgba(34,211,238,0.3), transparent);"></div>
    </div>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <div class="mx-auto max-w-[1600px] px-4 md:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ── LEFT COLUMN: Portrait + Stats ── --}}
            <div class="lg:col-span-4 xl:col-span-3 space-y-4">

                {{-- Portrait Panel --}}
                <div class="ag-portrait-glow relative overflow-hidden"
                    style="background: linear-gradient(135deg, rgba(9,12,23,1) 0%, rgba(6,24,56,0.8) 100%); border: 1px solid rgba(34,211,238,0.25);">

                    {{-- Corner brackets --}}
                    <div
                        style="position:absolute;top:8px;left:8px;width:16px;height:16px;border-top:2px solid rgba(34,211,238,0.5);border-left:2px solid rgba(34,211,238,0.5);">
                    </div>
                    <div
                        style="position:absolute;top:8px;right:8px;width:16px;height:16px;border-top:2px solid rgba(34,211,238,0.5);border-right:2px solid rgba(34,211,238,0.5);">
                    </div>
                    <div
                        style="position:absolute;bottom:8px;left:8px;width:16px;height:16px;border-bottom:2px solid rgba(34,211,238,0.5);border-left:2px solid rgba(34,211,238,0.5);">
                    </div>
                    <div
                        style="position:absolute;bottom:8px;right:8px;width:16px;height:16px;border-bottom:2px solid rgba(34,211,238,0.5);border-right:2px solid rgba(34,211,238,0.5);">
                    </div>

                    {{-- Scan line effect --}}
                    <div class="ag-scan-line"></div>

                    {{-- Radial glow --}}
                    <div class="absolute inset-0"
                        style="background: radial-gradient(ellipse at 50% 80%, rgba(34,211,238,0.06) 0%, transparent 70%); pointer-events:none;">
                    </div>

                    <div class="flex justify-center pt-8 pb-4 px-6">
                        <img src="{{ $characterImage2d }}" alt="{{ e($character->CharName16) }}"
                            class="h-56 w-auto object-contain object-bottom"
                            style="filter: drop-shadow(0 4px 20px rgba(34,211,238,0.2));">
                    </div>

                    {{-- Name plate --}}
                    <div class="px-5 pb-5 text-center">
                        <p class="ag-font-display font-bold ag-text-surface text-lg tracking-wider">
                            {{ e($character->CharName16) }}</p>
                        @if ($character->guild && $character->guild->ID !== 0)
                            <p class="text-xs ag-text-secondary mt-1">{{ e($character->guild->Name) }}</p>
                        @endif
                    </div>
                </div>

                {{-- Core Stats --}}
                <div class="ag-card overflow-hidden">
                    <div class="px-5 py-3 border-b ag-divider flex items-center gap-2">
                        <div class="w-1 h-4" style="background: var(--ag-primary);"></div>
                        <p class="ag-section-eyebrow">{{ __('ranking.character_stats') }}</p>
                    </div>
                    <div class="p-5 space-y-5">

                        {{-- Level --}}
                        <div class="flex items-center justify-between">
                            <span class="text-xs ag-text-muted uppercase tracking-wider">{{ __('ranking.level') }}</span>
                            <span class="ag-font-mono text-lg font-bold ag-text-primary">{{ $character->CurLevel }}</span>
                        </div>

                        {{-- HP Bar --}}
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-semibold tracking-wider uppercase"
                                    style="color: #4ade80;">HP</span>
                                <span class="ag-font-mono text-xs font-bold"
                                    style="color: #4ade80;">{{ number_format($character->HP) }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full" style="background: rgba(74,222,128,0.1);">
                                <div class="h-full rounded-full ag-stat-bar"
                                    style="width: {{ $hpPct }}%; background: linear-gradient(90deg, #16a34a, #4ade80);">
                                </div>
                            </div>
                        </div>

                        {{-- MP Bar --}}
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-semibold tracking-wider uppercase"
                                    style="color: #60a5fa;">MP</span>
                                <span class="ag-font-mono text-xs font-bold"
                                    style="color: #60a5fa;">{{ number_format($character->MP) }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full" style="background: rgba(96,165,250,0.1);">
                                <div class="h-full rounded-full ag-stat-bar"
                                    style="width: {{ $mpPct }}%; background: linear-gradient(90deg, #2563eb, #60a5fa); animation-delay: 0.1s;">
                                </div>
                            </div>
                        </div>

                        {{-- EXP Bar --}}
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs ag-text-muted font-semibold tracking-wider uppercase">EXP</span>
                                <span class="ag-font-mono text-xs ag-text-primary font-bold">{{ $expPct }}%</span>
                            </div>
                            <div class="h-2 w-full rounded-full" style="background: rgba(34,211,238,0.1);">
                                <div class="h-full rounded-full ag-stat-bar"
                                    style="width: {{ $expPct }}%; background: linear-gradient(90deg, var(--ag-primary-dim), var(--ag-primary)); animation-delay: 0.2s;">
                                </div>
                            </div>
                        </div>

                        <div class="pt-1 border-t ag-divider space-y-3">
                            {{-- STR --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full" style="background: #f87171;"></div>
                                    <span
                                        class="text-xs ag-text-muted uppercase tracking-wider">{{ __('ranking.strength') }}</span>
                                </div>
                                <span
                                    class="ag-font-mono text-sm font-bold ag-text-surface">{{ number_format($character->Strength) }}</span>
                            </div>
                            {{-- INT --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full" style="background: #a78bfa;"></div>
                                    <span
                                        class="text-xs ag-text-muted uppercase tracking-wider">{{ __('ranking.intellect') }}</span>
                                </div>
                                <span
                                    class="ag-font-mono text-sm font-bold ag-text-surface">{{ number_format($character->Intellect) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <x-character-globals :name="$character->CharName16" />
            </div>

            {{-- ── RIGHT COLUMN: Equipment / Avatar ── --}}
            <div class="lg:col-span-8 xl:col-span-9" x-data="{ tab: 'equipment' }">

                {{-- Tab bar --}}
                <div class="flex items-center gap-0 mb-6 border-b ag-divider">
                    <button @click="tab = 'equipment'" type="button"
                        :class="tab === 'equipment' ? 'ag-text-primary border-b-2 border-cyan-400' :
                            'ag-text-muted hover:ag-text-surface'"
                        class="ag-font-display text-xs font-semibold tracking-widest uppercase px-5 py-3 transition-colors border-b-2 border-transparent -mb-px">
                        {{ __('ranking.equipment') }}
                    </button>
                    @if ($avatar->isNotEmpty())
                        <button @click="tab = 'avatar'" type="button"
                            :class="tab === 'avatar' ? 'ag-text-primary border-b-2 border-cyan-400' :
                                'ag-text-muted hover:ag-text-surface'"
                            class="ag-font-display text-xs font-semibold tracking-widest uppercase px-5 py-3 transition-colors border-b-2 border-transparent -mb-px">
                            {{ __('ranking.avatar') }}
                        </button>
                    @endif
                </div>

                {{-- Equipment Tab --}}
                <div x-show="tab === 'equipment'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    @include('template::ranking.partials.equipment', [
                        'equipment' => $equipment,
                        'characterImage2d' => $characterFullImage2d,
                    ])
                </div>

                {{-- Avatar Tab --}}
                @if ($avatar->isNotEmpty())
                    <div x-show="tab === 'avatar'" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        @include('template::ranking.partials.avatar', [
                            'avatar' => $avatar,
                            'characterImage2d' => $characterFullImage2d,
                        ])
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
