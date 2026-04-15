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

    {{-- Hero Section --}}
    <section class="relative overflow-hidden">
        @if (\App\Helpers\SettingHelper::get('background_image'))
            <div class="absolute inset-0">
                <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}" alt=""
                    class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-gray-950/70 via-gray-950/50 to-gray-950"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-950 via-gray-950 to-cyan-950"></div>
            <div class="absolute inset-0 opacity-20"
                style="background-image: radial-gradient(circle at 25% 25%, rgba(16,185,129,0.15) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(6,182,212,0.15) 0%, transparent 50%);">
            </div>
        @endif

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-28 sm:py-36 text-center">
            @if (\App\Helpers\SettingHelper::get('logo'))
                <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}"
                    alt="@settings('site_title', 'SilkPanel')" class="mx-auto mb-6 h-20 w-auto drop-shadow-2xl">
            @endif
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight">
                <span class="bg-gradient-to-r from-emerald-400 via-cyan-300 to-emerald-400 bg-clip-text text-transparent">
                    @settings('site_title', 'SilkPanel CMS')
                </span>
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-400">
                @settings('site_description', 'A powerful Silkroad Online private server.')
            </p>
            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('downloads.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-gray-950 bg-gradient-to-r from-emerald-400 to-cyan-400 hover:from-emerald-300 hover:to-cyan-300 rounded-xl shadow-lg shadow-emerald-500/25 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {{ __('index.download') }}
                </a>
                @guest
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center px-6 py-3 text-sm font-semibold text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/10 rounded-xl transition">
                        {{ __('index.register_now') }}
                    </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- Left Column: News (8/12) --}}
                <div class="lg:col-span-8 space-y-8">

                    {{-- Welcome Block --}}
                    <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6">
                        <h2 class="text-xl font-bold text-white">
                            {{ __('index.welcome_title') }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-400 leading-relaxed">
                            {{ __('index.welcome_text') }}
                        </p>
                    </div>

                    {{-- Featured News --}}
                    @if ($featuredNews)
                        <div>
                            <div class="mb-5 flex items-center justify-between">
                                <h2 class="text-xl font-bold text-white">
                                    {{ __('index.latest_news') }}
                                </h2>
                                <a href="{{ route('news.index') }}"
                                    class="text-sm text-emerald-400 hover:text-emerald-300 transition">
                                    {{ __('index.view_all') }} &rarr;
                                </a>
                            </div>

                            {{-- Hero Card --}}
                            <a href="{{ route('news.show', $featuredNews->slug) }}"
                                class="group block overflow-hidden rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur hover:border-emerald-500/30 transition-all hover:shadow-lg hover:shadow-emerald-500/5">
                                @if ($featuredNews->thumbnail)
                                    <div class="aspect-[21/9] overflow-hidden">
                                        <img src="{{ asset('storage/' . $featuredNews->thumbnail) }}"
                                            alt="{{ e($featuredNews->name) }}"
                                            class="h-full w-full object-cover group-hover:scale-105 transition duration-500">
                                    </div>
                                @endif
                                <div class="p-5">
                                    <div class="mb-2 flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-400">
                                            {{ __('index.featured') }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $featuredNews->published_at ? \Carbon\Carbon::parse($featuredNews->published_at)->diffForHumans() : '' }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition">
                                        {{ e($featuredNews->name) }}
                                    </h3>
                                    @if ($featuredNews->excerpt)
                                        <p class="mt-2 text-sm text-gray-400 line-clamp-2">
                                            {{ e($featuredNews->excerpt) }}
                                        </p>
                                    @endif
                                </div>
                            </a>

                            {{-- Other News --}}
                            @if ($otherNews->isNotEmpty())
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach ($otherNews as $news)
                                        <a href="{{ route('news.show', $news->slug) }}"
                                            class="group flex gap-4 rounded-xl border border-gray-800 bg-gray-900/50 backdrop-blur p-3 hover:border-emerald-500/30 transition-all">
                                            @if ($news->thumbnail)
                                                <div class="flex-shrink-0 h-16 w-24 overflow-hidden rounded-lg bg-gray-800">
                                                    <img src="{{ asset('storage/' . $news->thumbnail) }}"
                                                        alt="{{ e($news->name) }}"
                                                        class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[11px] text-gray-500">
                                                    {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->diffForHumans() : '' }}
                                                </p>
                                                <h4
                                                    class="truncate text-sm font-semibold text-white group-hover:text-emerald-400 transition">
                                                    {{ e($news->name) }}
                                                </h4>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- About Block --}}
                    <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6">
                        <h2 class="text-xl font-bold text-white">
                            {{ __('index.about_title') }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-400 leading-relaxed">
                            {{ __('index.about_text') }}
                        </p>
                    </div>
                </div>

                {{-- Right Column: Sidebar (4/12) --}}
                <aside class="lg:col-span-4 space-y-6">

                    {{-- Quick Actions --}}
                    <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-5 space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                            {{ __('index.quick_links') }}
                        </h3>
                        <a href="{{ route('downloads.index') }}"
                            class="flex items-center gap-3 w-full rounded-xl px-4 py-2.5 text-sm font-medium text-gray-950 bg-gradient-to-r from-emerald-400 to-cyan-400 hover:from-emerald-300 hover:to-cyan-300 shadow-lg shadow-emerald-500/20 transition">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            {{ __('index.download') }}
                        </a>
                        @guest
                            <a href="{{ route('register') }}"
                                class="flex items-center gap-3 w-full rounded-xl border border-emerald-500/30 px-4 py-2.5 text-sm font-medium text-emerald-400 hover:bg-emerald-500/10 transition">
                                <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                {{ __('index.register_now') }}
                            </a>
                        @endguest
                        <a href="{{ route('ranking.characters') }}"
                            class="flex items-center gap-3 w-full rounded-xl px-4 py-2.5 text-sm font-medium text-gray-300 bg-gray-800/50 hover:bg-gray-800 transition">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            {{ __('index.rankings') }}
                        </a>
                    </div>

                    {{-- Event Timers --}}
                    <livewire:event-timers-list />

                    {{-- Server Info --}}
                    <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-5">
                        <h3 class="mb-4 text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                            {{ __('index.server_info') }}
                        </h3>
                        <dl class="space-y-3">
                            @foreach ($serverStats as $stat)
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm text-gray-400">{{ $stat['label'] }}</dt>
                                    <dd class="text-sm font-bold text-white">{{ $stat['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>

                    {{-- Races --}}
                    @if (is_array($races) && count($races) > 0)
                        <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-5">
                            <h3 class="mb-3 text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                                {{ __('index.races') }}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($races as $race)
                                    <span
                                        class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-400">
                                        {{ e($race) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Fortress Wars --}}
                    @if (is_array($fortressWars) && count($fortressWars) > 0)
                        <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-5">
                            <h3 class="mb-3 text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                                {{ __('index.fortress_war') }}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($fortressWars as $fortress)
                                    <span
                                        class="inline-flex items-center rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-medium text-amber-400">
                                        {{ e($fortress) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection
