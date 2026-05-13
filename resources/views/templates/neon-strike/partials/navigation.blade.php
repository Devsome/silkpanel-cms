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
    class="fixed inset-x-0 top-0 z-50 border-b border-violet-500/20 bg-black/90 backdrop-blur-xl">

    {{-- Top accent line --}}
    <div class="h-px bg-linear-to-r from-transparent via-violet-500 to-transparent"></div>

    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

        {{-- Logo --}}
        <div class="flex items-center gap-8">
            <a href="{{ route('index') }}" class="flex items-center gap-2 group">
                @if (\App\Helpers\SettingHelper::get('logo'))
                    <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}"
                        alt="@settings('site_title', 'SilkPanel')" class="h-8 w-auto">
                @else
                    <span
                        class="text-lg font-black uppercase tracking-[0.2em] bg-linear-to-r from-violet-400 via-fuchsia-400 to-cyan-400 bg-clip-text text-transparent ns-glitch">
                        @settings('site_title', 'SilkPanel')
                    </span>
                @endif
            </a>

            {{-- Desktop Links --}}
            <div class="hidden lg:flex items-center gap-0.5">
                @php
                    $navLink = fn($active) => $active
                        ? 'relative px-3 py-1.5 text-sm font-medium text-violet-400 after:absolute after:inset-x-0 after:bottom-0 after:h-px after:bg-linear-to-r after:from-violet-500 after:to-fuchsia-500'
                        : 'relative px-3 py-1.5 text-sm font-medium text-zinc-400 hover:text-white transition-colors';
                @endphp

                <a href="{{ route('index') }}" class="{{ $navLink(request()->routeIs('index')) }}">
                    {{ __('navigation.index') }}
                </a>
                <a href="{{ route('news.index') }}" class="{{ $navLink(request()->routeIs('news.*')) }}">
                    {{ __('navigation.news') }}
                </a>
                <a href="{{ route('downloads.index') }}" class="{{ $navLink(request()->routeIs('downloads.*')) }}">
                    {{ __('navigation.downloads') }}
                </a>

                @if ($navPages->isNotEmpty())
                    <div class="relative" x-data="{ pagesDrop: false }" @click.outside="pagesDrop = false">
                        <button @click="pagesDrop = !pagesDrop"
                            class="{{ $navLink(request()->routeIs('pages.*')) }} inline-flex items-center gap-1">
                            {{ __('navigation.pages') }}
                            <svg class="w-3 h-3 transition-transform" :class="pagesDrop ? 'rotate-180' : ''"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="pagesDrop" x-transition style="display:none"
                            class="absolute left-0 mt-2 w-48 bg-zinc-950 border border-violet-500/25 shadow-[0_0_30px_rgba(139,92,246,0.15)] py-1">
                            @foreach ($navPages as $navPage)
                                <a href="{{ route('pages.show', $navPage->slug) }}"
                                    class="block px-4 py-2 text-sm text-zinc-400 hover:text-violet-400 hover:bg-violet-500/10 transition font-mono uppercase tracking-wider text-xs">
                                    {{ e($navPage->title) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="relative" x-data="{ rankingDrop: false }" @click.outside="rankingDrop = false">
                    <button @click="rankingDrop = !rankingDrop"
                        class="{{ $navLink(request()->routeIs('ranking.*')) }} inline-flex items-center gap-1">
                        {{ __('navigation.rankings') }}
                        <svg class="w-3 h-3 transition-transform" :class="rankingDrop ? 'rotate-180' : ''"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="rankingDrop" x-transition style="display:none"
                        class="absolute left-0 mt-2 w-48 bg-zinc-950 border border-violet-500/25 shadow-[0_0_30px_rgba(139,92,246,0.15)] py-1">
                        <a href="{{ route('ranking.characters') }}"
                            class="block px-4 py-2 text-xs text-zinc-400 hover:text-violet-400 hover:bg-violet-500/10 transition font-mono uppercase tracking-wider">
                            {{ __('navigation.ranking_characters') }}
                        </a>
                        <a href="{{ route('ranking.guilds') }}"
                            class="block px-4 py-2 text-xs text-zinc-400 hover:text-violet-400 hover:bg-violet-500/10 transition font-mono uppercase tracking-wider">
                            {{ __('navigation.ranking_guilds') }}
                        </a>
                        <a href="{{ route('ranking.uniques') }}"
                            class="block px-4 py-2 text-xs text-zinc-400 hover:text-violet-400 hover:bg-violet-500/10 transition font-mono uppercase tracking-wider">
                            {{ __('navigation.ranking_uniques') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Side --}}
        <div class="flex items-center gap-2">
            @if ($showLanguageSwitch)
                <div class="hidden sm:block relative" x-data="{ langOpen: false }" @click.outside="langOpen = false">
                    <button @click="langOpen = !langOpen"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-mono uppercase tracking-wider text-zinc-500 hover:text-violet-400 border border-transparent hover:border-violet-500/30 transition">
                        {{ $currentLanguageLabel }}
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="langOpen" x-transition style="display:none"
                        class="absolute right-0 mt-2 w-32 bg-zinc-950 border border-violet-500/25 py-1">
                        @foreach ($frontendLanguages as $locale => $label)
                            <a href="{{ route('language.switch', ['locale' => $locale]) }}"
                                class="block px-4 py-2 text-xs font-mono uppercase tracking-wider text-zinc-400 hover:text-violet-400 hover:bg-violet-500/10 transition">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="hidden sm:flex items-center gap-2">
                @auth
                    <div class="relative" x-data="{ userDrop: false }" @click.outside="userDrop = false">
                        <button @click="userDrop = !userDrop"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm text-zinc-300 hover:text-white border border-violet-500/20 hover:border-violet-500/50 hover:bg-violet-500/5 transition">
                            <span
                                class="flex h-6 w-6 items-center justify-center bg-violet-500/20 text-violet-400 text-xs font-bold font-mono">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="text-xs font-mono uppercase tracking-wider">{{ Auth::user()->name }}</span>
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="userDrop" x-transition style="display:none"
                            class="absolute right-0 mt-2 w-52 bg-zinc-950 border border-violet-500/25 shadow-[0_0_30px_rgba(139,92,246,0.15)] py-1">
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-2 px-4 py-2 text-xs font-mono uppercase tracking-wider text-zinc-400 hover:text-violet-400 hover:bg-violet-500/10 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                {{ __('navigation.dashboard') }}
                            </a>
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-2 px-4 py-2 text-xs font-mono uppercase tracking-wider text-zinc-400 hover:text-violet-400 hover:bg-violet-500/10 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('navigation.profile') }}
                            </a>
                            <div class="my-1 ns-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-xs font-mono uppercase tracking-wider text-zinc-500 hover:text-red-400 hover:bg-red-500/10 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    {{ __('navigation.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-1.5 text-xs font-mono uppercase tracking-wider text-zinc-400 border border-zinc-700 hover:border-violet-500/50 hover:text-violet-400 transition">
                        {{ __('navigation.login') }}
                    </a>
                    @settingsRegistrationOpen
                        <a href="{{ route('register') }}"
                            class="px-4 py-1.5 text-xs font-mono uppercase tracking-wider text-black font-bold bg-linear-to-r from-violet-500 to-fuchsia-500 hover:from-violet-400 hover:to-fuchsia-400 transition shadow-[0_0_15px_rgba(139,92,246,0.4)]">
                            {{ __('navigation.register') }}
                        </a>
                    @endsettingsRegistrationOpen
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <button @click="open = !open" class="lg:hidden p-2 text-zinc-400 hover:text-violet-400 transition">
                <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-cloak x-transition
        class="lg:hidden border-t border-violet-500/20 bg-zinc-950/95 backdrop-blur">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('index') }}"
                class="block px-3 py-2 text-xs font-mono uppercase tracking-wider {{ request()->routeIs('index') ? 'text-violet-400' : 'text-zinc-400 hover:text-white' }} transition">{{ __('navigation.index') }}</a>
            <a href="{{ route('news.index') }}"
                class="block px-3 py-2 text-xs font-mono uppercase tracking-wider {{ request()->routeIs('news.*') ? 'text-violet-400' : 'text-zinc-400 hover:text-white' }} transition">{{ __('navigation.news') }}</a>
            <a href="{{ route('downloads.index') }}"
                class="block px-3 py-2 text-xs font-mono uppercase tracking-wider {{ request()->routeIs('downloads.*') ? 'text-violet-400' : 'text-zinc-400 hover:text-white' }} transition">{{ __('navigation.downloads') }}</a>
            <a href="{{ route('ranking.characters') }}"
                class="block px-3 py-2 text-xs font-mono uppercase tracking-wider text-zinc-400 hover:text-white transition">{{ __('navigation.rankings') }}</a>
            @auth
                <a href="{{ route('dashboard') }}"
                    class="block px-3 py-2 text-xs font-mono uppercase tracking-wider {{ request()->routeIs('dashboard') ? 'text-violet-400' : 'text-zinc-400 hover:text-white' }} transition">{{ __('navigation.dashboard') }}</a>
                <a href="{{ route('voting.index') }}"
                    class="block px-3 py-2 text-xs font-mono uppercase tracking-wider {{ request()->routeIs('voting.*') ? 'text-violet-400' : 'text-zinc-400 hover:text-white' }} transition">{{ __('navigation.voting') ?? 'Voting' }}</a>
                @if (\App\Helpers\SettingHelper::get('webmall_enabled', false))
                    <a href="{{ route('webmall.index') }}"
                        class="block px-3 py-2 text-xs font-mono uppercase tracking-wider {{ request()->routeIs('webmall.*') ? 'text-violet-400' : 'text-zinc-400 hover:text-white' }} transition">{{ __('navigation.webmall') ?? 'Webmall' }}</a>
                @endif
                <a href="{{ route('donate.index') }}"
                    class="block px-3 py-2 text-xs font-mono uppercase tracking-wider {{ request()->routeIs('donate.*') ? 'text-violet-400' : 'text-zinc-400 hover:text-white' }} transition">{{ __('navigation.donate') ?? 'Donate' }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit"
                        class="block w-full text-left px-3 py-2 text-xs font-mono uppercase tracking-wider text-zinc-500 hover:text-red-400 transition">{{ __('navigation.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="block px-3 py-2 text-xs font-mono uppercase tracking-wider text-zinc-400 hover:text-white transition">{{ __('navigation.login') }}</a>
                @settingsRegistrationOpen
                    <a href="{{ route('register') }}"
                        class="block px-3 py-2 text-xs font-mono uppercase tracking-wider text-violet-400">{{ __('navigation.register') }}</a>
                @endsettingsRegistrationOpen
            @endauth
        </div>
    </div>
</nav>
