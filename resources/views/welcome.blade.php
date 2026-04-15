<x-app-layout>
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

    <section class="relative overflow-hidden">
        @if (\App\Helpers\SettingHelper::get('background_image'))
            <div class="absolute inset-0">
                <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}" alt=""
                    class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/80"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-gray-900 to-gray-800"></div>
        @endif
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32 text-center">
            @if (\App\Helpers\SettingHelper::get('logo'))
                <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}"
                    alt="@settings('site_title', 'SilkPanel')" class="h-20 w-auto mx-auto mb-6">
            @endif
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white tracking-tight">
                @settings('site_title', 'SilkPanel CMS')
            </h1>
            <p class="mt-4 text-lg text-gray-300 max-w-2xl mx-auto">
                @settings('site_description', 'A powerful Silkroad Online private server.')
            </p>
            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('downloads.index') }}"
                    class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition">
                    {{ __('index.download') }}
                </a>
                @guest
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center px-6 py-3 text-sm font-semibold text-indigo-300 border border-indigo-500 hover:bg-indigo-500/10 rounded-lg transition">
                        {{ __('index.register_now') }}
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <div class="lg:col-span-8 space-y-8">

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ __('index.welcome_title') }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ __('index.welcome_text') }}
                        </p>
                    </div>

                    @if ($featuredNews)
                        <div>
                            <div class="flex items-center justify-between mb-5">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ __('index.latest_news') }}
                                </h2>
                                <a href="{{ route('news.index') }}"
                                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ __('index.view_all') }} &rarr;
                                </a>
                            </div>

                            <a href="{{ route('news.show', $featuredNews->slug) }}"
                                class="group block bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg transition">
                                @if ($featuredNews->thumbnail)
                                    <div class="aspect-[21/9] overflow-hidden">
                                        <img src="{{ asset('storage/' . $featuredNews->thumbnail) }}"
                                            alt="{{ e($featuredNews->name) }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    </div>
                                @endif
                                <div class="p-5">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                            {{ __('index.featured') }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $featuredNews->published_at ? \Carbon\Carbon::parse($featuredNews->published_at)->diffForHumans() : '' }}
                                        </span>
                                    </div>
                                    <h3
                                        class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                        {{ e($featuredNews->name) }}
                                    </h3>
                                    @if ($featuredNews->excerpt)
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                            {{ e($featuredNews->excerpt) }}
                                        </p>
                                    @endif
                                </div>
                            </a>

                            @if ($otherNews->isNotEmpty())
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach ($otherNews as $news)
                                        <a href="{{ route('news.show', $news->slug) }}"
                                            class="group flex gap-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:shadow-md transition">
                                            @if ($news->thumbnail)
                                                <div
                                                    class="flex-shrink-0 w-24 h-16 rounded-md overflow-hidden bg-gray-100 dark:bg-gray-700">
                                                    <img src="{{ asset('storage/' . $news->thumbnail) }}"
                                                        alt="{{ e($news->name) }}"
                                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                                    {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->diffForHumans() : '' }}
                                                </p>
                                                <h4
                                                    class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition truncate">
                                                    {{ e($news->name) }}
                                                </h4>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <livewire:event-timers />

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ __('index.about_title') }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ __('index.about_text') }}
                        </p>
                    </div>
                </div>

                <aside class="lg:col-span-4 space-y-6">

                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 space-y-3">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            {{ __('index.quick_links') }}
                        </h3>
                        <a href="{{ route('downloads.index') }}"
                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            {{ __('index.download') }}
                        </a>
                        @guest
                            <a href="{{ route('register') }}"
                                class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg transition">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                {{ __('index.register_now') }}
                            </a>
                        @endguest
                        <a href="{{ route('ranking.characters') }}"
                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            {{ __('index.rankings') }}
                        </a>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">
                            {{ __('index.server_info') }}
                        </h3>
                        <dl class="space-y-3">
                            @foreach ($serverStats as $stat)
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm text-gray-600 dark:text-gray-400">{{ $stat['label'] }}</dt>
                                    <dd class="text-sm font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>

                    <livewire:event-timers-list />

                    @if (is_array($races) && count($races) > 0)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                            <h3
                                class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                                {{ __('index.races') }}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($races as $race)
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                        {{ e($race) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (is_array($fortressWars) && count($fortressWars) > 0)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                            <h3
                                class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                                {{ __('index.fortress_war') }}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($fortressWars as $fortress)
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
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
</x-app-layout>
