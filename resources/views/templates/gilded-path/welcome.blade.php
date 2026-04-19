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

    <div class="max-w-400 mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 px-4 md:px-8">

        {{-- Main Content (8-9 columns) --}}
        <div class="lg:col-span-8 xl:col-span-9 space-y-12">

            {{-- Hero Section --}}
            <section class="relative h-125 md:h-150 w-full overflow-hidden gp-ornate-border mt-8">
                @if (\App\Helpers\SettingHelper::get('background_image'))
                    <div class="absolute inset-0 bg-cover bg-center"
                        style="background-image: url('{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}')">
                    </div>
                @else
                    <div class="absolute inset-0"
                        style="background: linear-gradient(135deg, #1a1500 0%, #131313 50%, #0d1020 100%);"></div>
                @endif
                <div class="absolute inset-0 gp-hero-gradient"></div>
                <div class="relative h-full flex flex-col justify-center px-6 md:px-12 max-w-4xl">
                    <h1
                        class="text-4xl md:text-6xl gp-text-primary font-headline font-bold mb-4 tracking-widest drop-shadow-lg leading-tight uppercase">
                        @settings('site_title', 'SilkPanel CMS')
                    </h1>
                    <p class="gp-text-on-surface-variant text-lg max-w-xl mb-10 leading-relaxed font-light">
                        @settings('site_description', 'Experience the rebirth of the Silk Road.')
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('downloads.index') }}"
                            class="gp-gold-btn px-8 py-4 font-headline font-black text-base uppercase tracking-widest shadow-2xl transition-all">
                            {{ __('index.download') }}
                        </a>
                        @guest
                            <a href="{{ route('register') }}"
                                class="border px-8 py-4 font-headline font-bold text-base uppercase tracking-widest transition-all hover:bg-neutral-800"
                                style="border-color: var(--gp-outline); color: var(--gp-on-surface);">
                                {{ __('index.register_now') }}
                            </a>
                        @endguest
                    </div>
                </div>
            </section>

            {{-- Welcome Block --}}
            <div class="gp-card p-6">
                <h2 class="text-xl font-bold font-headline gp-text-primary uppercase tracking-widest">
                    {{ __('index.welcome_title') }}
                </h2>
                <p class="mt-2 text-sm gp-text-on-surface-variant leading-relaxed">
                    {{ __('index.welcome_text') }}
                </p>
            </div>

            {{-- News Section --}}
            @if ($featuredNews)
                <section class="space-y-8">
                    <div class="flex justify-between items-end pb-4"
                        style="border-bottom: 1px solid var(--gp-outline-variant);">
                        <h2 class="font-headline text-3xl gp-text-on-surface font-bold uppercase tracking-widest">
                            {{ __('index.latest_news') }}
                        </h2>
                        <a class="gp-text-primary text-sm font-bold uppercase tracking-widest hover:underline"
                            href="{{ route('news.index') }}">
                            {{ __('index.view_all') }}
                        </a>
                    </div>

                    <div class="grid grid-cols-1 gap-8">
                        {{-- Featured News --}}
                        <a href="{{ route('news.show', $featuredNews->slug) }}"
                            class="group flex flex-col md:flex-row gap-6 p-4 transition-colors cursor-pointer gp-card hover:bg-[#2a2a2a]">
                            @if ($featuredNews->thumbnail)
                                <div class="w-full md:w-64 h-40 shrink-0 overflow-hidden"
                                    style="background-color: var(--gp-surface-container-highest);">
                                    <img src="{{ asset('storage/' . $featuredNews->thumbnail) }}"
                                        alt="{{ e($featuredNews->name) }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                            @endif
                            <div class="flex flex-col justify-center">
                                <span class="text-xs gp-text-tertiary font-bold uppercase tracking-widest mb-2">
                                    {{ __('index.featured') }}
                                </span>
                                <h4
                                    class="text-xl font-headline font-bold gp-text-on-surface group-hover:text-yellow-500 transition-colors mb-2">
                                    {{ e($featuredNews->name) }}
                                </h4>
                                @if ($featuredNews->excerpt)
                                    <p class="gp-text-on-surface-variant text-sm line-clamp-2">
                                        {{ e($featuredNews->excerpt) }}
                                    </p>
                                @endif
                                <span class="text-xs gp-text-outline mt-2">
                                    {{ $featuredNews->published_at ? \Carbon\Carbon::parse($featuredNews->published_at)->diffForHumans() : '' }}
                                </span>
                            </div>
                        </a>

                        {{-- Other News --}}
                        @if ($otherNews->isNotEmpty())
                            @foreach ($otherNews as $news)
                                <a href="{{ route('news.show', $news->slug) }}"
                                    class="group flex flex-col md:flex-row gap-6 p-4 transition-colors cursor-pointer gp-card hover:bg-[#2a2a2a]">
                                    @if ($news->thumbnail)
                                        <div class="w-full md:w-64 h-40 shrink-0 overflow-hidden"
                                            style="background-color: var(--gp-surface-container-highest);">
                                            <img src="{{ asset('storage/' . $news->thumbnail) }}"
                                                alt="{{ e($news->name) }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        </div>
                                    @endif
                                    <div class="flex flex-col justify-center">
                                        <span class="text-xs gp-text-secondary font-bold uppercase tracking-widest mb-2">
                                            {{ __('index.news') }}
                                        </span>
                                        <h4
                                            class="text-xl font-headline font-bold gp-text-on-surface group-hover:text-yellow-500 transition-colors mb-2">
                                            {{ e($news->name) }}
                                        </h4>
                                        @if ($news->excerpt)
                                            <p class="gp-text-on-surface-variant text-sm line-clamp-2">
                                                {{ e($news->excerpt) }}
                                            </p>
                                        @endif
                                        <span class="text-xs gp-text-outline mt-2">
                                            {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->diffForHumans() : '' }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </section>
            @endif

            {{-- About Block --}}
            <div class="gp-card p-6">
                <h2 class="text-xl font-bold font-headline gp-text-primary uppercase tracking-widest">
                    {{ __('index.about_title') }}
                </h2>
                <p class="mt-2 text-sm gp-text-on-surface-variant leading-relaxed">
                    {{ __('index.about_text') }}
                </p>
            </div>

            {{-- CTA Section --}}
            <section class="pb-20">
                <div class="relative h-80 flex flex-col items-center justify-center text-center p-8 overflow-hidden gp-ornate-border"
                    style="background-color: #0f0f0f;">
                    <h2 class="relative z-10 text-3xl md:text-4xl font-headline font-bold gp-text-primary mb-4 uppercase"
                        style="letter-spacing: 0.2em;">
                        {{ __('index.welcome_title') }}
                    </h2>
                    <p class="relative z-10 gp-text-on-surface-variant max-w-xl mb-8 text-base">
                        @settings('site_description', 'Join thousands of players.')
                    </p>
                    <div class="relative z-10 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('downloads.index') }}"
                            class="gp-gold-btn px-8 py-4 font-headline font-black text-lg uppercase tracking-widest shadow-2xl hover:scale-105 transition-all">
                            {{ __('index.download') }}
                        </a>
                        @guest
                            <a href="{{ route('register') }}"
                                class="px-8 py-4 font-headline font-black text-lg uppercase tracking-widest transition-all gp-text-primary gp-card hover:bg-[#393939]"
                                style="border: 1px solid rgba(242,202,80,0.3);">
                                {{ __('index.register_now') }}
                            </a>
                        @endguest
                    </div>
                </div>
            </section>
        </div>

        <aside class="lg:col-span-4 xl:col-span-3 space-y-8 mt-8">

            <div class="gp-card gp-ornate-border p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider gp-text-primary">
                        {{ __('index.online_players') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        <span class="text-[10px] font-medium text-green-500 uppercase tracking-wider">Online</span>
                    </span>
                </div>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-bold tabular-nums text-yellow-600">@onlineCounter</span>
                    @if ($maxPlayers > 0)
                        <span class="text-sm gp-text-on-surface mb-0.5">/
                            {{ number_format($maxPlayers) }}</span>
                    @endif
                </div>
                @if ($maxPlayers > 0)
                    @php
                        $onlineCount = \App\View\Components\OnlineCounter::getData();
                        $pct = $maxPlayers > 0 ? min(100, round(($onlineCount / $maxPlayers) * 100)) : 0;
                        $barColor = $pct >= 80 ? 'bg-red-500' : ($pct >= 50 ? 'bg-yellow-500' : 'bg-green-500');
                    @endphp
                    <div class="mt-3 h-1.5 w-full bg-gray-700/50 rounded-full overflow-hidden">
                        <div class="{{ $barColor }} h-full rounded-full transition-all duration-700"
                            style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="mt-1.5 text-[11px] gp-text-on-surface-variant text-right">
                        {{ $pct }}% {{ __('index.server_capacity') }}</p>
                @endif
            </div>

            {{-- Server Info --}}
            <div class="gp-card gp-ornate-border p-6 shadow-2xl">
                <h3 class="font-headline gp-text-primary font-bold uppercase tracking-widest text-sm mb-6">
                    {{ __('index.server_info') }}
                </h3>
                <dl class="space-y-3">
                    @foreach ($serverStats as $stat)
                        <div class="flex items-center justify-between">
                            <dt class="text-sm gp-text-on-surface-variant">{{ $stat['label'] }}</dt>
                            <dd class="text-sm font-bold gp-text-on-surface">{{ $stat['value'] }}</dd>
                        </div>
                    @endforeach

                    @if (is_array($races) && count($races) > 0)
                        <h3 class="mb-3 text-xs font-bold uppercase tracking-widest text-yellow-600 font-headline">
                            {{ __('index.races') }}
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($races as $race)
                                <span
                                    class="inline-flex items-center bg-yellow-900/20 px-2.5 py-1 text-xs font-medium text-yellow-400">
                                    {{ Str::ucfirst(e($race)) }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if (is_array($fortressWars) && count($fortressWars) > 0)
                        <h3 class="mb-3 text-xs font-bold uppercase tracking-widest text-yellow-600 font-headline">
                            {{ __('index.fortress_war') }}
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($fortressWars as $fortress)
                                <span
                                    class="inline-flex items-center bg-red-900/20 px-2.5 py-1 text-xs font-medium text-red-400">
                                    {{ Str::ucfirst(e($fortress)) }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                </dl>
            </div>

            <div class="gp-card p-5 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-widest text-yellow-600 font-headline">
                    {{ __('index.quick_links') }}
                </h3>
                <a href="{{ route('downloads.index') }}"
                    class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-medium gp-gold-btn shadow-lg transition">
                    <x-filament::icon icon="heroicon-m-arrow-down-tray" label="{{ __('index.download') }}"
                        class="size-4" />
                    {{ __('index.download') }}
                </a>
                @guest
                    <a href="{{ route('register') }}"
                        class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-yellow-500 transition gp-card hover:bg-yellow-900/10"
                        style="border: 1px solid rgba(242,202,80,0.3);">
                        <x-filament::icon icon="heroicon-m-user-plus" label="{{ __('index.register_now') }}"
                            class="size-4" />
                        {{ __('index.register_now') }}
                    </a>
                @endguest
                <a href="{{ route('ranking.characters') }}"
                    class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-medium gp-text-on-surface-variant gp-card-low hover:bg-[#2a2a2a] transition">
                    <x-filament::icon icon="heroicon-m-chart-bar" label="{{ __('index.rankings') }}" class="size-4" />
                    {{ __('index.rankings') }}
                </a>
            </div>

            <livewire:event-timers-list />

            @discordWidget

        </aside>
    </div>
@endsection
