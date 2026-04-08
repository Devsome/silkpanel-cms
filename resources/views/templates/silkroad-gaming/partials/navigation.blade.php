@php
    $frontendLanguages = $frontendLanguages ?? \App\Helpers\SettingHelper::frontendLanguagesWithLabels();
    $currentFrontendLocale = $currentFrontendLocale ?? app()->getLocale();
    $currentLanguageLabel = $frontendLanguages[$currentFrontendLocale] ?? strtoupper($currentFrontendLocale);
    $showLanguageSwitch = count($frontendLanguages) > 1;

    $navPages = \App\Models\Page::whereNotNull('published_at')
        ->where('published_at', '<=', now())
        ->with([
            'translations' => fn($q) => $q
                ->where('locale', app()->getLocale())
                ->orWhere('locale', config('app.fallback_locale')),
        ])
        ->get()
        ->map(function ($page) {
            $t =
                $page->translations->firstWhere('locale', app()->getLocale()) ??
                $page->translations->firstWhere('locale', config('app.fallback_locale'));
            return $t ? (object) ['slug' => $page->slug, 'title' => $t->title] : null;
        })
        ->filter();
@endphp

<nav x-data="{ open: false }"
    class="fixed inset-x-0 top-0 z-50 border-b border-emerald-500/20 bg-gray-950/80 backdrop-blur-xl">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <div class="flex items-center gap-8">
            <a href="{{ route('index') }}" class="flex items-center gap-2">
                @if (\App\Helpers\SettingHelper::get('logo'))
                    <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}"
                        alt="@settings('site_title', 'SilkPanel')" class="h-8 w-auto">
                @else
                    <span
                        class="text-lg font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">
                        @settings('site_title', 'SilkPanel')
                    </span>
                @endif
            </a>

            {{-- Desktop Links --}}
            <div class="hidden sm:flex items-center gap-1">
                <a href="{{ route('index') }}"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('index') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    {{ __('navigation.index') }}
                </a>
                <a href="{{ route('news.index') }}"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('news.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    {{ __('navigation.news') }}
                </a>
                <a href="{{ route('downloads.index') }}"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('downloads.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    {{ __('navigation.downloads') }}
                </a>

                {{-- Pages Dropdown --}}
                @if ($navPages->isNotEmpty())
                    <div class="relative" x-data="{ pagesDrop: false }" @click.outside="pagesDrop = false">
                        <button @click="pagesDrop = !pagesDrop"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('pages.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                            {{ __('navigation.pages') }}
                            <svg class="w-3.5 h-3.5 transition-transform" :class="pagesDrop ? 'rotate-180' : ''"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="pagesDrop" x-transition
                            class="absolute left-0 mt-2 w-48 rounded-xl border border-gray-800 bg-gray-900/95 backdrop-blur-lg p-1.5 shadow-2xl"
                            style="display: none;">
                            @foreach ($navPages as $navPage)
                                <a href="{{ route('pages.show', $navPage->slug) }}"
                                    class="block rounded-lg px-3 py-2 text-sm text-gray-300 hover:bg-emerald-500/10 hover:text-emerald-400 transition">
                                    {{ e($navPage->title) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Rankings Dropdown --}}
                <div class="relative" x-data="{ rankingDrop: false }" @click.outside="rankingDrop = false">
                    <button @click="rankingDrop = !rankingDrop"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('ranking.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        {{ __('navigation.rankings') }}
                        <svg class="w-3.5 h-3.5 transition-transform" :class="rankingDrop ? 'rotate-180' : ''"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="rankingDrop" x-transition
                        class="absolute left-0 mt-2 w-48 rounded-xl border border-gray-800 bg-gray-900/95 backdrop-blur-lg p-1.5 shadow-2xl"
                        style="display: none;">
                        <a href="{{ route('ranking.characters') }}"
                            class="block rounded-lg px-3 py-2 text-sm text-gray-300 hover:bg-emerald-500/10 hover:text-emerald-400 transition">
                            {{ __('navigation.ranking_characters') }}
                        </a>
                        <a href="{{ route('ranking.guilds') }}"
                            class="block rounded-lg px-3 py-2 text-sm text-gray-300 hover:bg-emerald-500/10 hover:text-emerald-400 transition">
                            {{ __('navigation.ranking_guilds') }}
                        </a>
                        <a href="{{ route('ranking.uniques') }}"
                            class="block rounded-lg px-3 py-2 text-sm text-gray-300 hover:bg-emerald-500/10 hover:text-emerald-400 transition">
                            {{ __('navigation.ranking_uniques') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-2">
            {{-- Language Switch --}}
            @if ($showLanguageSwitch)
                <div class="hidden sm:block relative" x-data="{ langOpen: false }" @click.outside="langOpen = false">
                    <button @click="langOpen = !langOpen"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 transition">
                        {{ $currentLanguageLabel }}
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="langOpen" x-transition
                        class="absolute right-0 mt-2 w-36 rounded-xl border border-gray-800 bg-gray-900/95 backdrop-blur-lg p-1.5 shadow-2xl"
                        style="display: none;">
                        @foreach ($frontendLanguages as $locale => $label)
                            <a href="{{ route('language.switch', ['locale' => $locale]) }}"
                                class="block rounded-lg px-3 py-2 text-sm text-gray-300 hover:bg-emerald-500/10 hover:text-emerald-400 transition">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Auth --}}
            <div class="hidden sm:flex items-center gap-2">
                @auth
                    <div class="relative" x-data="{ userDrop: false }" @click.outside="userDrop = false">
                        <button @click="userDrop = !userDrop"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-white/5 transition">
                            <span
                                class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            {{ Auth::user()->name }}
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="userDrop" x-transition
                            class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-800 bg-gray-900/95 backdrop-blur-lg p-1.5 shadow-2xl"
                            style="display: none;">
                            <a href="{{ route('dashboard') }}"
                                class="block rounded-lg px-3 py-2 text-sm text-gray-300 hover:bg-emerald-500/10 hover:text-emerald-400 transition">
                                {{ __('navigation.dashboard') }}
                            </a>
                            <a href="{{ route('profile.edit') }}"
                                class="block rounded-lg px-3 py-2 text-sm text-gray-300 hover:bg-emerald-500/10 hover:text-emerald-400 transition">
                                {{ __('navigation.profile') }}
                            </a>
                            <div class="my-1 border-t border-gray-800"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left rounded-lg px-3 py-2 text-sm text-gray-300 hover:bg-red-500/10 hover:text-red-400 transition">
                                    {{ __('navigation.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-white/5 transition">
                        {{ __('navigation.login') }}
                    </a>
                    @settingsRegistrationOpen
                        <a href="{{ route('register') }}"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium text-gray-950 bg-gradient-to-r from-emerald-400 to-cyan-400 hover:from-emerald-300 hover:to-cyan-300 shadow-lg shadow-emerald-500/25 transition">
                            {{ __('navigation.register') }}
                        </a>
                    @endsettingsRegistrationOpen
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <button @click="open = !open"
                class="sm:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden border-t border-gray-800">
        <div class="space-y-1 px-4 py-3">
            <a href="{{ route('index') }}"
                class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('index') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                {{ __('navigation.index') }}
            </a>
            <a href="{{ route('news.index') }}"
                class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('news.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                {{ __('navigation.news') }}
            </a>
            <a href="{{ route('downloads.index') }}"
                class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('downloads.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                {{ __('navigation.downloads') }}
            </a>

            @if ($navPages->isNotEmpty())
                <div class="pt-2 pb-1 px-3 text-xs uppercase tracking-wider text-gray-600">
                    {{ __('navigation.pages') }}</div>
                @foreach ($navPages as $navPage)
                    <a href="{{ route('pages.show', $navPage->slug) }}"
                        class="block rounded-lg px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 transition">
                        {{ e($navPage->title) }}
                    </a>
                @endforeach
            @endif

            <div class="pt-2 pb-1 px-3 text-xs uppercase tracking-wider text-gray-600">{{ __('navigation.rankings') }}
            </div>
            <a href="{{ route('ranking.characters') }}"
                class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('ranking.characters') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                {{ __('navigation.ranking_characters') }}
            </a>
            <a href="{{ route('ranking.guilds') }}"
                class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('ranking.guilds') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                {{ __('navigation.ranking_guilds') }}
            </a>
            <a href="{{ route('ranking.uniques') }}"
                class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('ranking.uniques') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                {{ __('navigation.ranking_uniques') }}
            </a>

            @if ($showLanguageSwitch)
                <div class="pt-2 pb-1 px-3 text-xs uppercase tracking-wider text-gray-600">
                    {{ __('navigation.language') }}</div>
                @foreach ($frontendLanguages as $locale => $label)
                    <a href="{{ route('language.switch', ['locale' => $locale]) }}"
                        class="block rounded-lg px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 transition">
                        {{ $label }}
                    </a>
                @endforeach
            @endif

            <div class="border-t border-gray-800 pt-3 mt-3 space-y-1">
                @auth
                    <div class="px-3 py-2 text-sm text-gray-500">{{ Auth::user()->name }}</div>
                    <a href="{{ route('dashboard') }}"
                        class="block rounded-lg px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 transition">
                        {{ __('navigation.dashboard') }}
                    </a>
                    <a href="{{ route('profile.edit') }}"
                        class="block rounded-lg px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 transition">
                        {{ __('navigation.profile') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left rounded-lg px-3 py-2 text-sm text-red-400 hover:bg-red-500/10 transition">
                            {{ __('navigation.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="block rounded-lg px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 transition">
                        {{ __('navigation.login') }}
                    </a>
                    @settingsRegistrationOpen
                        <a href="{{ route('register') }}"
                            class="block rounded-lg px-3 py-2 text-sm font-medium text-emerald-400 hover:bg-emerald-500/10 transition">
                            {{ __('navigation.register') }}
                        </a>
                    @endsettingsRegistrationOpen
                @endauth
            </div>
        </div>
    </div>
</nav>
