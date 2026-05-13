@extends('template::layouts.app')

@section('content')
    @php
        $serverName = \App\Helpers\SettingHelper::get('site_title', 'SilkPanel');
        $serverDescription = \App\Helpers\SettingHelper::get('site_description', '');
        $expSpRate = \App\Helpers\SettingHelper::get('sro_exp_sp', null);
        $dropRate = \App\Helpers\SettingHelper::get('sro_drop_rate', null);
        $capLevel = (int) \App\Helpers\SettingHelper::get('sro_cap', 0);
        $maxPlayers = (int) \App\Helpers\SettingHelper::get('sro_max_player', 0);
        $races = \App\Helpers\SettingHelper::get('sro_races', null);
        $fortressInfo = \App\Helpers\SettingHelper::get('sro_fortress_war', null);
        $serverStatus = \App\Helpers\SettingHelper::get('server_status', 'online');
        $latestNews = \App\Models\News::whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();
    @endphp

    {{-- ═══════════════════════════════════════════════════════════
     HERO — Full-width with scanlines overlay
     ═══════════════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden bg-black min-h-[90vh] flex items-center">
        {{-- Background grid --}}
        <div class="absolute inset-0"
            style="background-image: linear-gradient(rgba(139,92,246,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(139,92,246,0.05) 1px, transparent 1px); background-size: 40px 40px;">
        </div>
        {{-- Ambient glow --}}
        <div class="absolute inset-0 pointer-events-none">
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-violet-600/10 rounded-full blur-[150px]">
            </div>
            <div class="absolute top-1/3 right-1/4 w-[400px] h-[400px] bg-fuchsia-600/8 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-1/4 left-1/4 w-[300px] h-[300px] bg-cyan-600/6 rounded-full blur-[80px]"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 text-center">
            {{-- Status badge --}}
            <div class="inline-flex items-center gap-2 px-3 py-1 border border-violet-500/30 bg-violet-500/10 mb-8">
                <span class="relative flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full bg-{{ $serverStatus === 'online' ? 'emerald' : 'red' }}-400 opacity-75 rounded-full"></span>
                    <span
                        class="relative inline-flex h-2 w-2 bg-{{ $serverStatus === 'online' ? 'emerald' : 'red' }}-400 rounded-full"></span>
                </span>
                <span class="text-xs font-mono uppercase tracking-[0.2em] text-violet-300">
                    {{ $serverStatus === 'online' ? __('index.server_online') ?? 'SERVER ONLINE' : __('index.server_offline') ?? 'SERVER OFFLINE' }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black uppercase tracking-[0.08em] leading-none ns-glitch">
                <span class="bg-linear-to-r from-violet-400 via-fuchsia-400 to-cyan-400 bg-clip-text text-transparent">
                    {{ $serverName }}
                </span>
            </h1>

            @if ($serverDescription)
                <p class="mt-6 text-zinc-400 text-lg max-w-2xl mx-auto leading-relaxed">
                    {{ $serverDescription }}
                </p>
            @endif

            {{-- CTA Buttons --}}
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                @guest
                    @settingsRegistrationOpen
                        <a href="{{ route('register') }}"
                            class="px-8 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_30px_rgba(139,92,246,0.5)] hover:shadow-[0_0_40px_rgba(139,92,246,0.7)]">
                            {{ __('index.register_now') }}
                        </a>
                    @endsettingsRegistrationOpen
                    <a href="{{ route('downloads.index') }}"
                        class="px-8 py-3 text-sm font-bold uppercase tracking-[0.2em] text-violet-400 border border-violet-500/40 hover:bg-violet-500/10 hover:border-violet-400 transition">
                        {{ __('index.download') }}
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                        class="px-8 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_30px_rgba(139,92,246,0.5)]">
                        {{ __('navigation.dashboard') }}
                    </a>
                    <a href="{{ route('donate.index') }}"
                        class="px-8 py-3 text-sm font-bold uppercase tracking-[0.2em] text-violet-400 border border-violet-500/40 hover:bg-violet-500/10 hover:border-violet-400 transition">
                        {{ __('navigation.donate') ?? 'Donate' }}
                    </a>
                @endguest
            </div>
        </div>

        {{-- Bottom gradient --}}
        <div class="absolute bottom-0 inset-x-0 h-24 bg-gradient-to-t from-black to-transparent"></div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     SERVER STATS BAR
     ═══════════════════════════════════════════════════════════ --}}
    <section class="border-y border-violet-500/20 bg-zinc-950/80 backdrop-blur">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 divide-x divide-violet-500/10">
                @php
                    $stats = [
                        [
                            'label' => __('index.online_players'),
                            'value' => null,
                            'type' => 'online',
                            'color' => 'text-emerald-400',
                        ],
                        [
                            'label' => __('index.cap'),
                            'value' => $capLevel > 0 ? $capLevel : '-',
                            'type' => 'text',
                            'color' => 'text-cyan-400',
                        ],
                        [
                            'label' => __('index.exp_sp'),
                            'value' => $expSpRate ?? '—',
                            'type' => 'text',
                            'color' => 'text-violet-400',
                        ],
                        [
                            'label' => __('index.drop_rate'),
                            'value' => $dropRate ?? '—',
                            'type' => 'text',
                            'color' => 'text-fuchsia-400',
                        ],
                        [
                            'label' => __('index.max_player'),
                            'value' => $maxPlayers > 0 ? number_format($maxPlayers) : '-',
                            'type' => 'text',
                            'color' => 'text-amber-400',
                        ],
                    ];
                @endphp
                @foreach ($stats as $stat)
                    <div class="py-5 px-4 sm:px-6 text-center first:border-l-0 last:border-r-0">
                        <p class="text-xs font-mono uppercase tracking-[0.2em] text-zinc-600 mb-1">{{ $stat['label'] }}</p>
                        @if ($stat['type'] === 'online')
                            <p class="text-2xl font-bold font-mono {{ $stat['color'] }}">@onlineCounter</p>
                        @else
                            <p class="text-2xl font-bold font-mono {{ $stat['color'] }}">{{ $stat['value'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     LATEST NEWS
     ═══════════════════════════════════════════════════════════ --}}
    @if ($latestNews->isNotEmpty())
        <section class="py-16 bg-black">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-1">
                            {{ __('index.news') }}</p>
                        <h2 class="text-2xl font-bold uppercase tracking-widest text-white">
                            {{ __('index.latest_news') }}</h2>
                    </div>
                    <a href="{{ route('news.index') }}"
                        class="hidden sm:inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-500 hover:text-violet-400 transition border border-transparent hover:border-violet-500/30 px-3 py-1.5">
                        {{ __('index.view_all') }} →
                    </a>
                </div>

                @if ($latestNews->count() >= 2)
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                        {{-- Featured news --}}
                        @php $featured = $latestNews->first(); @endphp
                        <a href="{{ route('news.show', $featured->slug) }}"
                            class="lg:col-span-3 group relative block overflow-hidden bg-zinc-900 border border-violet-500/20 hover:border-violet-500/50 transition shadow-[0_0_30px_rgba(139,92,246,0.05)] hover:shadow-[0_0_40px_rgba(139,92,246,0.15)]">
                            @if ($featured->thumbnail)
                                <div class="relative aspect-video overflow-hidden">
                                    <img src="{{ asset('storage/' . $featured->thumbnail) }}"
                                        alt="{{ e($featured->name) }}"
                                        class="w-full h-full object-cover opacity-70 group-hover:opacity-90 group-hover:scale-105 transition duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/30 to-transparent">
                                    </div>
                                </div>
                            @else
                                <div
                                    class="aspect-video bg-linear-to-br from-violet-900/40 to-zinc-900 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-violet-500/20" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="p-5">
                                <p class="text-xs font-mono uppercase tracking-wider text-violet-400/70 mb-2">
                                    {{ $featured->published_at ? \Carbon\Carbon::parse($featured->published_at)->diffForHumans() : '' }}
                                </p>
                                <h3
                                    class="text-lg font-bold uppercase tracking-wider text-white group-hover:text-violet-300 transition line-clamp-2">
                                    {{ e($featured->name) }}
                                </h3>
                            </div>
                        </a>

                        {{-- Side news grid --}}
                        <div class="lg:col-span-2 flex flex-col gap-4">
                            @foreach ($latestNews->skip(1)->take(3) as $item)
                                <a href="{{ route('news.show', $item->slug) }}"
                                    class="group flex gap-3 bg-zinc-900 border border-violet-500/20 hover:border-violet-500/40 transition p-4 items-center">
                                    @if ($item->thumbnail)
                                        <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="{{ e($item->name) }}"
                                            class="w-16 h-16 object-cover flex-shrink-0 opacity-70 group-hover:opacity-90 transition">
                                    @else
                                        <div
                                            class="w-16 h-16 flex-shrink-0 bg-violet-900/30 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-violet-500/30" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="text-xs font-mono text-zinc-600 mb-1">
                                            {{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}</p>
                                        <p
                                            class="text-sm font-medium text-zinc-300 group-hover:text-violet-300 transition line-clamp-2 uppercase tracking-wide">
                                            {{ e($item->name) }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($latestNews as $item)
                            <a href="{{ route('news.show', $item->slug) }}"
                                class="group bg-zinc-900 border border-violet-500/20 hover:border-violet-500/40 transition p-5">
                                <p class="text-xs font-mono text-zinc-600 mb-2">
                                    {{ \Carbon\Carbon::parse($item->published_at)->format('d M Y') }}
                                </p>
                                <h3
                                    class="text-base font-bold uppercase tracking-wider text-zinc-200 group-hover:text-violet-300 transition">
                                    {{ e($item->name) }}</h3>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
     SERVER INFO GRID
     ═══════════════════════════════════════════════════════════ --}}
    <section class="py-16 bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-1">
                    {{ __('index.server_info') }}</p>
                <h2 class="text-2xl font-bold uppercase tracking-widest text-white">{{ __('index.server_info') }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Quick Actions --}}
                <div class="bg-zinc-900 border border-violet-500/20 p-6 relative ns-corner">
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-5">
                        {{ __('index.quick_links') }}</p>
                    <div class="space-y-2">
                        <a href="{{ route('downloads.index') }}"
                            class="flex items-center gap-3 p-3 border border-zinc-800 hover:border-violet-500/40 hover:bg-violet-500/5 group transition">
                            <span
                                class="flex h-8 w-8 items-center justify-center border border-violet-500/30 text-violet-500 group-hover:bg-violet-500/15 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </span>
                            <span
                                class="text-xs font-mono uppercase tracking-wider text-zinc-400 group-hover:text-violet-300 transition">{{ __('index.download') }}</span>
                        </a>
                        <a href="{{ route('ranking.characters') }}"
                            class="flex items-center gap-3 p-3 border border-zinc-800 hover:border-violet-500/40 hover:bg-violet-500/5 group transition">
                            <span
                                class="flex h-8 w-8 items-center justify-center border border-violet-500/30 text-violet-500 group-hover:bg-violet-500/15 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </span>
                            <span
                                class="text-xs font-mono uppercase tracking-wider text-zinc-400 group-hover:text-violet-300 transition">{{ __('index.rankings') }}</span>
                        </a>
                        <a href="{{ route('news.index') }}"
                            class="flex items-center gap-3 p-3 border border-zinc-800 hover:border-violet-500/40 hover:bg-violet-500/5 group transition">
                            <span
                                class="flex h-8 w-8 items-center justify-center border border-violet-500/30 text-violet-500 group-hover:bg-violet-500/15 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </span>
                            <span
                                class="text-xs font-mono uppercase tracking-wider text-zinc-400 group-hover:text-violet-300 transition">{{ __('index.news') }}</span>
                        </a>
                        @guest
                            @settingsRegistrationOpen
                                <a href="{{ route('register') }}"
                                    class="flex items-center gap-3 p-3 border border-fuchsia-500/30 bg-fuchsia-500/5 hover:bg-fuchsia-500/10 hover:border-fuchsia-500/60 group transition">
                                    <span
                                        class="flex h-8 w-8 items-center justify-center border border-fuchsia-500/30 text-fuchsia-400 group-hover:bg-fuchsia-500/15 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                    </span>
                                    <span
                                        class="text-xs font-mono uppercase tracking-wider text-fuchsia-400 group-hover:text-fuchsia-300 transition">{{ __('index.register_now') }}</span>
                                </a>
                            @endsettingsRegistrationOpen
                        @endguest
                    </div>
                </div>

                {{-- Server Info --}}
                <div class="bg-zinc-900 border border-violet-500/20 p-6">
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-5">
                        {{ __('index.server_info') }}</p>
                    @php
                        $infoItems = array_filter([
                            $capLevel > 0
                                ? [
                                    'label' => __('index.cap'),
                                    'value' => 'CAP ' . $capLevel,
                                    'color' => 'text-cyan-400',
                                ]
                                : null,
                            $expSpRate
                                ? [
                                    'label' => __('index.exp_sp'),
                                    'value' => $expSpRate,
                                    'color' => 'text-violet-400',
                                ]
                                : null,
                            $dropRate
                                ? [
                                    'label' => __('index.drop_rate'),
                                    'value' => $dropRate,
                                    'color' => 'text-amber-400',
                                ]
                                : null,
                        ]);
                    @endphp
                    <div class="space-y-3">
                        @foreach ($infoItems as $info)
                            <div class="flex items-center justify-between py-2 border-b border-zinc-800/80">
                                <span
                                    class="text-xs font-mono uppercase tracking-wider text-zinc-500">{{ $info['label'] }}</span>
                                <span
                                    class="text-sm font-bold font-mono {{ $info['color'] }}">{{ $info['value'] }}</span>
                            </div>
                        @endforeach
                        @if ($races)
                            <div class="flex items-center justify-between py-2 border-b border-zinc-800/80">
                                <span
                                    class="text-xs font-mono uppercase tracking-wider text-zinc-500">{{ __('index.races') }}</span>
                                <span class="text-sm font-bold font-mono text-zinc-300">{{ e($races) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Online Counter --}}
                <div
                    class="bg-zinc-900 border border-violet-500/20 p-6 flex flex-col items-center justify-center text-center">
                    @php $onlineCount = \App\View\Components\OnlineCounter::getData(); @endphp
                    <div class="relative mb-4">
                        <div
                            class="w-20 h-20 border-2 border-violet-500/40 flex items-center justify-center shadow-[0_0_30px_rgba(139,92,246,0.2)]">
                            <span
                                class="text-3xl font-black font-mono bg-gradient-to-b from-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
                                {{ $onlineCount }}
                            </span>
                        </div>
                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-400"></span>
                        </span>
                    </div>
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-500">
                        {{ __('index.online_players') }}</p>
                    @if ($maxPlayers > 0)
                        <div class="mt-4 w-full">
                            <div class="h-1 bg-zinc-800 overflow-hidden">
                                <div class="h-full bg-linear-to-r from-violet-500 to-fuchsia-500 transition-all duration-1000"
                                    style="width: {{ min(100, round(($onlineCount / $maxPlayers) * 100)) }}%"></div>
                            </div>
                            <p class="mt-1 text-xs font-mono text-zinc-700">{{ $onlineCount }} /
                                {{ number_format($maxPlayers) }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
     EVENT TIMERS + DISCORD WIDGET
     ═══════════════════════════════════════════════════════════ --}}
    <section class="py-16 bg-black border-t border-violet-500/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Event Timers --}}
                <div class="bg-zinc-900 border border-violet-500/20 p-6">
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-5">
                        {{ __('index.event_timers') }}</p>
                    <livewire:event-timers-list />
                </div>

                {{-- Discord Widget --}}
                <div class="bg-zinc-900 border border-fuchsia-500/20 p-6">
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-fuchsia-400/70 mb-5">
                        {{ __('index.discord.title') }}</p>
                    @discordWidget
                </div>
            </div>
        </div>
    </section>

@endsection
