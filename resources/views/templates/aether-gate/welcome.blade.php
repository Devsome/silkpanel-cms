@extends('template::layouts.app')

@section('content')
    @php
        $latestNews = \App\Models\News::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $featuredNews = $latestNews->first();
        $otherNews = $latestNews->slice(1);

        $races = \App\Helpers\SettingHelper::get('sro_race', []);
        $fortressWars = \App\Helpers\SettingHelper::get('sro_fortress_war', []);
        $maxPlayers = (int) \App\Helpers\SettingHelper::get('sro_max_player', 0);

        $serverStats = [
            ['label' => __('index.cap'), 'value' => \App\Helpers\SettingHelper::get('sro_cap', '—')],
            ['label' => __('index.exp_sp'), 'value' => \App\Helpers\SettingHelper::get('sro_exp_sp', '—')],
            ['label' => __('index.party_exp'), 'value' => \App\Helpers\SettingHelper::get('sro_party_exp', '—')],
            ['label' => __('index.drop_rate'), 'value' => \App\Helpers\SettingHelper::get('sro_drop_rate', '—')],
            ['label' => __('index.gold_drop'), 'value' => \App\Helpers\SettingHelper::get('sro_gold_drop_rate', '—')],
            ['label' => __('index.trade_rate'), 'value' => \App\Helpers\SettingHelper::get('sro_trade_rate', '—')],
            ['label' => __('index.max_player'), 'value' => \App\Helpers\SettingHelper::get('sro_max_player', '—')],
            ['label' => __('index.ip_limit'), 'value' => \App\Helpers\SettingHelper::get('sro_ip_limit', '—')],
            ['label' => __('index.hwid_limit'), 'value' => \App\Helpers\SettingHelper::get('sro_hwid_limit', '—')],
        ];
    @endphp

    {{-- ====== HERO SECTION ====== --}}
    <section class="relative min-h-[600px] md:min-h-[680px] flex flex-col justify-end overflow-hidden">
        {{-- Background image or gradient --}}
        @if (\App\Helpers\SettingHelper::get('background_image'))
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}');">
            </div>
        @else
            <div class="absolute inset-0"
                style="background: radial-gradient(ellipse 80% 60% at 60% 40%, #0a1f40 0%, #06080f 70%);"></div>
            {{-- Decorative geometric lines --}}
            <svg class="absolute inset-0 w-full h-full opacity-5" viewBox="0 0 1200 680" preserveAspectRatio="xMidYMid slice">
                <line x1="0" y1="340" x2="1200" y2="340" stroke="#22d3ee" stroke-width="0.5" />
                <line x1="600" y1="0" x2="600" y2="680" stroke="#22d3ee" stroke-width="0.5" />
                <circle cx="600" cy="340" r="200" fill="none" stroke="#22d3ee" stroke-width="0.5" />
                <circle cx="600" cy="340" r="350" fill="none" stroke="#22d3ee" stroke-width="0.5" />
                <polygon points="600,140 760,440 440,440" fill="none" stroke="#22d3ee" stroke-width="0.5" />
            </svg>
        @endif

        {{-- Gradient overlay --}}
        <div class="absolute inset-0 ag-hero-overlay"></div>
        {{-- Side gradient --}}
        <div class="absolute inset-0"
            style="background: linear-gradient(to right, rgba(6,8,15,0.9) 0%, rgba(6,8,15,0.3) 60%, rgba(6,8,15,0.6) 100%);">
        </div>

        {{-- Hero content --}}
        <div class="relative mx-auto max-w-[1600px] w-full px-4 md:px-8 pb-16 pt-20">
            <div class="max-w-2xl">
                <p class="ag-section-eyebrow mb-3">{{ __('index.welcome_title') }}</p>
                <h1 class="ag-font-display font-bold leading-tight mb-6"
                    style="font-size: clamp(2rem, 5vw, 3.5rem); color: var(--ag-on-surface);">
                    @settings('site_title', 'SilkPanel CMS')
                </h1>
                <p class="text-base md:text-lg ag-text-muted max-w-lg mb-8 leading-relaxed font-light">
                    @settings('site_description', 'Experience the rebirth of the Silk Road. Join thousands of warriors on an
                    epic journey.')
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('downloads.index') }}"
                        class="inline-flex items-center gap-2 px-7 py-3 ag-btn-primary text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ __('index.download') }}
                    </a>
                    @guest
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 px-7 py-3 ag-btn-secondary text-sm">
                            {{ __('index.register_now') }}
                        </a>
                    @endguest
                </div>
            </div>
        </div>

        {{-- Server status bar --}}
        <div class="relative" style="background: rgba(6,8,15,0.8); border-top: 1px solid rgba(34,211,238,0.1);">
            <div class="mx-auto max-w-[1600px] px-4 md:px-8">
                <div class="flex items-center gap-6 py-3 overflow-x-auto scrollbar-none">
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="ag-online-dot"></span>
                        <span
                            class="ag-font-display text-xs font-semibold tracking-widest uppercase ag-text-primary">{{ __('index.server_online') }}</span>
                    </div>
                    <div class="w-px h-5 bg-cyan-900/50 shrink-0"></div>
                    @foreach (array_slice($serverStats, 0, 5) as $stat)
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-xs ag-text-muted">{{ $stat['label'] }}</span>
                            <span class="ag-font-mono text-xs ag-text-primary font-bold">{{ $stat['value'] }}</span>
                        </div>
                        @if (!$loop->last)
                            <div class="w-px h-4 bg-cyan-900/40 shrink-0"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ====== MAIN CONTENT ====== --}}
    <div class="mx-auto max-w-[1600px] px-4 md:px-8 py-12">
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

            {{-- Main column --}}
            <div class="xl:col-span-8 space-y-12">

                {{-- News Section --}}
                @if ($featuredNews)
                    @php $featuredExcerpt = \Illuminate\Support\Str::limit(strip_tags($featuredNews->content ?? ''), 320); @endphp
                    <section>
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <p class="ag-section-eyebrow">{{ __('index.latest_news') }}</p>
                                <h2 class="ag-section-title mt-1">{{ __('index.whats_new') }}</h2>
                            </div>
                            <a href="{{ route('news.index') }}"
                                class="flex items-center gap-1 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-primary hover:opacity-80 transition-opacity">
                                {{ __('index.view_all') }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        {{-- Featured news hero --}}
                        <a href="{{ route('news.show', $featuredNews->slug) }}"
                            class="group block overflow-hidden mb-4 transition-all duration-300 hover:shadow-[0_0_40px_rgba(34,211,238,0.07)]"
                            style="border: 1px solid rgba(34,211,238,0.12); background: var(--ag-surface-container);">

                            {{-- Full-width thumbnail --}}
                            @if ($featuredNews->thumbnail)
                                <div class="overflow-hidden" style="height: 260px;">
                                    <img src="{{ asset('storage/' . $featuredNews->thumbnail) }}"
                                        alt="{{ e($featuredNews->name) }}"
                                        class="w-full h-full object-cover transition-all duration-700 group-hover:scale-[1.03]"
                                        style="filter: brightness(0.88) saturate(0.9); transition: transform 0.7s cubic-bezier(0.4,0,0.2,1), filter 0.7s;">
                                </div>
                            @endif

                            <div class="p-6 md:p-8">
                                {{-- Meta row --}}
                                <div class="flex items-center gap-3 mb-4">
                                    <span
                                        class="ag-font-display text-[10px] font-bold tracking-[0.18em] uppercase px-2.5 py-1"
                                        style="background: rgba(34,211,238,0.1); border: 1px solid rgba(34,211,238,0.22); color: var(--ag-primary);">
                                        {{ __('index.featured') }}
                                    </span>
                                    <span class="text-xs ag-text-muted">
                                        {{ \Carbon\Carbon::parse($featuredNews->published_at)->format('d M Y') }}
                                    </span>
                                    <span class="text-xs ag-text-muted opacity-50">·</span>
                                    <span class="text-xs ag-text-muted">
                                        {{ \Carbon\Carbon::parse($featuredNews->published_at)->diffForHumans() }}
                                    </span>
                                </div>

                                {{-- Title --}}
                                <h3 class="ag-font-display font-black ag-text-surface group-hover:ag-text-primary transition-colors leading-snug mb-4"
                                    style="font-size: clamp(1.2rem, 2.2vw, 1.6rem); letter-spacing: -0.02em;">
                                    {{ e($featuredNews->name) }}
                                </h3>

                                {{-- Excerpt --}}
                                @if ($featuredExcerpt)
                                    <p class="text-sm ag-text-muted leading-relaxed" style="max-width: 68ch;">
                                        {{ $featuredExcerpt }}
                                    </p>
                                @endif

                                {{-- Read more --}}
                                <div class="mt-6 pt-5 flex items-center justify-between"
                                    style="border-top: 1px solid var(--ag-outline);">
                                    <span
                                        class="inline-flex items-center gap-2 text-xs ag-font-display font-bold tracking-[0.15em] uppercase ag-text-primary">
                                        {{ __('index.read_more') }}
                                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1.5 transition-transform duration-200"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                    {{-- Word count hint --}}
                                    @php $wordCount = str_word_count(strip_tags($featuredNews->content ?? '')); @endphp
                                    @if ($wordCount > 0)
                                        <span class="text-xs ag-text-muted">~{{ max(1, round($wordCount / 200)) }} min
                                            read</span>
                                    @endif
                                </div>
                            </div>
                        </a>

                        {{-- Other news --}}
                        @if ($otherNews->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($otherNews as $article)
                                    <a href="{{ route('news.show', $article->slug) }}"
                                        class="group flex gap-4 p-4 transition-all"
                                        style="background: var(--ag-surface-container); border: 1px solid var(--ag-outline);">
                                        @if ($article->thumbnail)
                                            <div class="w-[72px] h-[54px] shrink-0 overflow-hidden">
                                                <img src="{{ asset('storage/' . $article->thumbnail) }}"
                                                    alt="{{ e($article->name) }}"
                                                    class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105"
                                                    style="filter: brightness(0.8); transition: transform 0.5s, filter 0.5s;">
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="text-sm font-semibold ag-text-surface group-hover:ag-text-primary transition-colors line-clamp-2 leading-snug">
                                                {{ e($article->name) }}
                                            </h4>
                                            <p class="mt-1 text-xs ag-text-muted">
                                                {{ \Carbon\Carbon::parse($article->published_at)->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif

                {{-- Server Rates --}}
                <section style="border: 1px solid var(--ag-outline); background: var(--ag-surface-container);">
                    <div class="flex items-center gap-3 px-4 py-2.5 border-b ag-divider">
                        <span class="ag-online-dot shrink-0"></span>
                        <p class="ag-font-display text-[10px] font-semibold tracking-[0.18em] uppercase ag-text-primary">
                            {{ __('index.server_rates') }}</p>
                        @if (!empty($races))
                            <div class="ml-auto flex items-center gap-1.5">
                                @foreach ($races as $race)
                                    <span
                                        style="font-size:10px;padding:1px 7px;background:rgba(167,139,250,0.1);border:1px solid rgba(167,139,250,0.2);color:rgba(167,139,250,0.9);"
                                        class="ag-font-display font-semibold tracking-wider uppercase">{{ e($race) }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if (!empty($fortressWars))
                            <div class="flex items-center gap-1.5 {{ empty($races) ? 'ml-auto' : '' }}">
                                @foreach ($fortressWars as $fw)
                                    <span
                                        style="font-size:10px;padding:1px 7px;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.2);color:rgba(251,191,36,0.85);"
                                        class="ag-font-display font-semibold tracking-wider uppercase">{{ e($fw) }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-wrap divide-x" style="divide-color: var(--ag-outline);">
                        @foreach ($serverStats as $stat)
                            <div class="flex items-center gap-2.5 px-4 py-3">
                                <span class="text-xs ag-text-muted whitespace-nowrap">{{ $stat['label'] }}</span>
                                <span
                                    class="ag-font-mono text-sm font-bold ag-stat-amber leading-none">{{ $stat['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Rankings preview --}}
                @php
                    $topChars = \Illuminate\Support\Facades\Cache::remember('homepage.ranking.chars', 300, function () {
                        try {
                            return \Illuminate\Support\Facades\DB::connection('shard')
                                ->table('_Char as chars')
                                ->leftJoin('_Guild as g', 'chars.GuildID', '=', 'g.ID')
                                ->where('chars.DeletedDate', '=', '0001-01-01 00:00:00.000')
                                ->where('chars.CharType', 0)
                                ->orderByDesc('chars.CurLevel')
                                ->orderByDesc('chars.Exp')
                                ->limit(5)
                                ->select(['chars.CharName16', 'chars.CurLevel', 'g.Name as GuildName'])
                                ->get();
                        } catch (\Exception $e) {
                            return collect();
                        }
                    });
                @endphp
                @if ($topChars->isNotEmpty())
                    <section>
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <p class="ag-section-eyebrow">{{ __('index.leaderboard') }}</p>
                                <h2 class="ag-section-title mt-1">{{ __('index.top_warriors') }}</h2>
                            </div>
                            <a href="{{ route('ranking.characters') }}"
                                class="flex items-center gap-1 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-primary hover:opacity-80 transition-opacity">
                                {{ __('index.full_rankings') }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <div class="ag-card overflow-hidden">
                            @foreach ($topChars as $i => $char)
                                <a href="{{ route('ranking.characters') }}"
                                    class="flex items-center gap-4 px-5 py-4 transition-colors hover:bg-cyan-900/5 {{ !$loop->last ? 'border-b ag-divider' : '' }}">
                                    <span
                                        class="ag-font-mono text-sm font-bold w-6 shrink-0
                                        {{ $i === 0 ? 'ag-rank-1' : ($i === 1 ? 'ag-rank-2' : ($i === 2 ? 'ag-rank-3' : 'ag-text-muted')) }}">
                                        {{ $i + 1 }}
                                    </span>
                                    <div class="w-8 h-8 flex items-center justify-center shrink-0"
                                        style="background: rgba(34,211,238,0.08); border: 1px solid rgba(34,211,238,0.12);">
                                        <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold ag-text-surface truncate">
                                            {{ e($char->CharName16) }}</p>
                                        @if ($char->GuildName)
                                            <p class="text-xs ag-text-muted truncate">{{ e($char->GuildName) }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p
                                            class="ag-font-display text-xs font-semibold tracking-wider uppercase ag-text-muted">
                                            {{ __('index.level') }}</p>
                                        <p class="ag-stat-number text-base">{{ $char->CurLevel }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Download Section --}}
                <section class="ag-card ag-bracket overflow-hidden"
                    style="background: linear-gradient(135deg, var(--ag-surface-container) 0%, rgba(9,30,60,0.5) 100%);">
                    <div class="p-8 md:p-10">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                            <div>
                                <p class="ag-section-eyebrow">{{ __('index.get_started') }}</p>
                                <h2 class="ag-section-title mt-2 mb-3">{{ __('index.download_client') }}</h2>
                                <p class="text-sm ag-text-muted max-w-md">{{ __('index.download_description') }}</p>
                            </div>
                            <a href="{{ route('downloads.index') }}"
                                class="shrink-0 inline-flex items-center gap-2 px-8 py-4 ag-btn-primary text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                {{ __('index.download_now') }}
                            </a>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Sidebar --}}
            <aside class="xl:col-span-4 space-y-6">

                {{-- Online players --}}
                @push('styles')
                    <style>
                        @keyframes ag-orb-pulse {

                            0%,
                            100% {
                                transform: scale(1);
                                opacity: 1;
                            }

                            50% {
                                transform: scale(1.08);
                                opacity: 0.85;
                            }
                        }

                        @keyframes ag-orb-ring {
                            0% {
                                transform: scale(1);
                                opacity: 0.5;
                            }

                            100% {
                                transform: scale(2.2);
                                opacity: 0;
                            }
                        }

                        .ag-orb-core {
                            animation: ag-orb-pulse 2.8s ease-in-out infinite;
                        }

                        .ag-orb-ring {
                            animation: ag-orb-ring 2.8s ease-out infinite;
                        }

                        .ag-orb-ring2 {
                            animation: ag-orb-ring 2.8s ease-out 0.9s infinite;
                        }
                    </style>
                @endpush
                <div
                    style="border: 1px solid rgba(34,211,238,0.14); background: var(--ag-surface-container); border-left: 2px solid var(--ag-primary);">
                    <div class="p-5 flex items-center gap-5">
                        {{-- Pulsing orb --}}
                        <div class="relative shrink-0 flex items-center justify-center" style="width:64px;height:64px;">
                            {{-- Expanding rings --}}
                            <div class="ag-orb-ring  absolute rounded-full"
                                style="width:36px;height:36px;border:1.5px solid rgba(34,211,238,0.5);"></div>
                            <div class="ag-orb-ring2 absolute rounded-full"
                                style="width:36px;height:36px;border:1.5px solid rgba(34,211,238,0.5);"></div>
                            {{-- Core --}}
                            <div class="ag-orb-core relative flex items-center justify-center rounded-full"
                                style="width:36px;height:36px;background:radial-gradient(circle at 38% 35%, rgba(34,211,238,0.35) 0%, rgba(34,211,238,0.08) 70%);border:1.5px solid rgba(34,211,238,0.55);box-shadow:0 0 18px rgba(34,211,238,0.25), inset 0 0 10px rgba(34,211,238,0.1);">
                                {{-- Person icon --}}
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="rgba(34,211,238,0.9)" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                        </div>

                        {{-- Counter --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="ag-online-dot"></span>
                                <p
                                    class="ag-font-display text-[10px] font-semibold tracking-[0.16em] uppercase ag-text-primary">
                                    {{ __('index.live') }}</p>
                            </div>
                            <x-online-counter class="ag-font-mono font-black ag-text-surface leading-none"
                                style="font-size:1.9rem;" />
                            <p class="text-xs ag-text-muted mt-1">{{ __('index.players_online') }}</p>
                            @if ($maxPlayers > 0)
                                <p class="text-[10px] mt-1" style="color:rgba(34,211,238,0.35);">
                                    {{ __('index.max_capacity', ['max' => number_format($maxPlayers)]) }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Event timers --}}
                <livewire:event-timers />

                {{-- Quick links --}}
                <div class="ag-card p-5">
                    <p class="ag-section-eyebrow mb-4">{{ __('index.quick_links') }}</p>
                    <div class="space-y-1">
                        @guest
                            <a href="{{ route('register') }}"
                                class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                                <div class="w-8 h-8 flex items-center justify-center shrink-0"
                                    style="background: rgba(34,211,238,0.1);">
                                    <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('index.create_account') }}</span>
                            </a>
                        @endguest
                        <a href="{{ route('ranking.characters') }}"
                            class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                            <div class="w-8 h-8 flex items-center justify-center shrink-0"
                                style="background: rgba(34,211,238,0.1);">
                                <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('navigation.rankings') }}</span>
                        </a>
                        <a href="{{ route('news.index') }}"
                            class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                            <div class="w-8 h-8 flex items-center justify-center shrink-0"
                                style="background: rgba(34,211,238,0.1);">
                                <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('navigation.news') }}</span>
                        </a>
                        <a href="{{ route('downloads.index') }}"
                            class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                            <div class="w-8 h-8 flex items-center justify-center shrink-0"
                                style="background: rgba(34,211,238,0.1);">
                                <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('navigation.downloads') }}</span>
                        </a>
                    </div>
                </div>

                @globalsWidget(5)

                {{-- Discord widget --}}
                <x-discord-widget />
            </aside>
        </div>
    </div>
@endsection
