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
    class="fixed top-0 w-full z-50 backdrop-blur-md border-b shadow-2xl shadow-black flex justify-between items-center h-20"
    style="background: rgba(10,10,10,0.9); border-color: rgba(120,90,0,0.3);">
    <div class="mx-auto w-full max-w-[1600px] flex items-center justify-between px-4 md:px-8">
        {{-- Logo / Brand --}}
        <div class="flex items-center gap-8">
            <a href="{{ route('index') }}" class="flex items-center gap-2">
                @if (\App\Helpers\SettingHelper::get('logo'))
                    <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}"
                        alt="@settings('site_title', 'SilkPanel')" class="h-10 w-auto">
                @else
                    <span
                        class="text-2xl font-bold text-yellow-500 drop-shadow-[0_2px_2px_rgba(212,175,55,0.5)] font-headline tracking-widest uppercase">
                        @settings('site_title', 'SilkPanel')
                    </span>
                @endif
            </a>

            {{-- Desktop Links --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('index') }}"
                    class="font-headline tracking-widest uppercase text-sm transition-all {{ request()->routeIs('index') ? 'text-yellow-400 border-b-2 border-yellow-500 pb-1' : 'text-neutral-400 hover:text-yellow-200 hover:bg-white/5' }}">
                    {{ __('navigation.index') }}
                </a>
                <a href="{{ route('news.index') }}"
                    class="font-headline tracking-widest uppercase text-sm transition-all {{ request()->routeIs('news.*') ? 'text-yellow-400 border-b-2 border-yellow-500 pb-1' : 'text-neutral-400 hover:text-yellow-200 hover:bg-white/5' }}">
                    {{ __('navigation.news') }}
                </a>
                <a href="{{ route('downloads.index') }}"
                    class="font-headline tracking-widest uppercase text-sm transition-all {{ request()->routeIs('downloads.*') ? 'text-yellow-400 border-b-2 border-yellow-500 pb-1' : 'text-neutral-400 hover:text-yellow-200 hover:bg-white/5' }}">
                    {{ __('navigation.downloads') }}
                </a>

                {{-- Pages Dropdown --}}
                @if ($navPages->isNotEmpty())
                    <div class="relative" x-data="{ pagesDrop: false }" @click.outside="pagesDrop = false">
                        <button @click="pagesDrop = !pagesDrop"
                            class="inline-flex items-center gap-1 font-headline tracking-widest uppercase text-sm transition-all {{ request()->routeIs('pages.*') ? 'text-yellow-400 border-b-2 border-yellow-500 pb-1' : 'text-neutral-400 hover:text-yellow-200' }}">
                            {{ __('navigation.pages') }}
                            <svg class="w-3 h-3 transition-transform" :class="pagesDrop ? 'rotate-180' : ''"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="pagesDrop" x-transition class="absolute left-0 mt-2 w-48 gp-card p-1.5 shadow-2xl"
                            style="display: none;">
                            @foreach ($navPages as $navPage)
                                <a href="{{ route('pages.show', $navPage->slug) }}"
                                    class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                                    {{ e($navPage->title) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Rankings Dropdown --}}
                <div class="relative" x-data="{ rankingDrop: false }" @click.outside="rankingDrop = false">
                    <button @click="rankingDrop = !rankingDrop"
                        class="inline-flex items-center gap-1 font-headline tracking-widest uppercase text-sm transition-all {{ request()->routeIs('ranking.*') ? 'text-yellow-400 border-b-2 border-yellow-500 pb-1' : 'text-neutral-400 hover:text-yellow-200' }}">
                        {{ __('navigation.rankings') }}
                        <svg class="w-3 h-3 transition-transform" :class="rankingDrop ? 'rotate-180' : ''"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="rankingDrop" x-transition class="absolute left-0 mt-2 w-48 gp-card p-1.5 shadow-2xl"
                        style="display: none;">
                        <a href="{{ route('ranking.characters') }}"
                            class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                            {{ __('navigation.ranking_characters') }}
                        </a>
                        <a href="{{ route('ranking.guilds') }}"
                            class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                            {{ __('navigation.ranking_guilds') }}
                        </a>
                        <a href="{{ route('ranking.uniques') }}"
                            class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                            {{ __('navigation.ranking_uniques') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-4">
            {{-- Language Switch --}}
            @if ($showLanguageSwitch)
                <div class="hidden md:block relative" x-data="{ langOpen: false }" @click.outside="langOpen = false">
                    <button @click="langOpen = !langOpen"
                        class="inline-flex items-center gap-1 text-sm text-neutral-400 hover:text-yellow-400 transition">
                        {{ $currentLanguageLabel }}
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="langOpen" x-transition class="absolute right-0 mt-2 w-36 gp-card p-1.5 shadow-2xl"
                        style="display: none;">
                        @foreach ($frontendLanguages as $locale => $label)
                            <a href="{{ route('language.switch', ['locale' => $locale]) }}"
                                class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Auth --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <div class="relative" x-data="{ userDrop: false }" @click.outside="userDrop = false">
                        <button @click="userDrop = !userDrop"
                            class="inline-flex items-center gap-2 text-sm text-neutral-300 hover:text-yellow-400 transition">
                            <x-filament::icon icon="heroicon-o-user" label="{{ __('index.account') }}"
                                class="size-4 text-yellow-400" />
                            {{ Auth::user()->name }}
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="userDrop" x-transition class="absolute right-0 mt-2 w-48 gp-card p-1.5 shadow-2xl"
                            style="display: none;">
                            <a href="{{ route('dashboard') }}"
                                class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                                {{ __('navigation.dashboard') }}
                            </a>
                            <a href="{{ route('profile.edit') }}"
                                class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                                {{ __('navigation.profile') }}
                            </a>
                            <div class="my-1" style="border-top: 1px solid rgba(77,70,53,0.2);"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-3 py-2 text-sm text-neutral-400 hover:text-red-400 hover:bg-red-900/10 transition">
                                    {{ __('navigation.logout') }}
                                </button>
                            </form>
                            @if (Auth::user()->isAdmin())
                                <a href="{{ route('filament.admin.pages.dashboard') }}"
                                    class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                                    {{ __('Admin') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-neutral-400 hover:text-yellow-400 transition">
                        {{ __('navigation.login') }}
                    </a>
                    @settingsRegistrationOpen
                        <a href="{{ route('register') }}"
                            class="gp-gold-btn px-6 py-2 font-headline font-bold uppercase tracking-wider text-sm transition-all">
                            {{ __('navigation.register') }}
                        </a>
                    @endsettingsRegistrationOpen
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <button @click="open = !open"
                class="md:hidden inline-flex items-center justify-center p-2 text-yellow-500 hover:text-yellow-400 transition">
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
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden md:hidden"
        style="border-top: 1px solid rgba(120,90,0,0.3); background: rgba(10,10,10,0.95);">
        <div class="space-y-1 px-4 py-3">
            <a href="{{ route('index') }}"
                class="block px-3 py-2 text-sm font-headline uppercase tracking-widest {{ request()->routeIs('index') ? 'text-yellow-400 bg-yellow-900/20 border-l-4 border-yellow-500' : 'text-neutral-400 hover:text-yellow-200 hover:bg-white/5' }} transition">
                {{ __('navigation.index') }}
            </a>
            <a href="{{ route('news.index') }}"
                class="block px-3 py-2 text-sm font-headline uppercase tracking-widest {{ request()->routeIs('news.*') ? 'text-yellow-400 bg-yellow-900/20 border-l-4 border-yellow-500' : 'text-neutral-400 hover:text-yellow-200 hover:bg-white/5' }} transition">
                {{ __('navigation.news') }}
            </a>
            <a href="{{ route('downloads.index') }}"
                class="block px-3 py-2 text-sm font-headline uppercase tracking-widest {{ request()->routeIs('downloads.*') ? 'text-yellow-400 bg-yellow-900/20 border-l-4 border-yellow-500' : 'text-neutral-400 hover:text-yellow-200 hover:bg-white/5' }} transition">
                {{ __('navigation.downloads') }}
            </a>

            @if ($navPages->isNotEmpty())
                <div class="pt-2 pb-1 px-3 text-xs uppercase tracking-wider text-yellow-700">
                    {{ __('navigation.pages') }}
                </div>
                @foreach ($navPages as $navPage)
                    <a href="{{ route('pages.show', $navPage->slug) }}"
                        class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                        {{ e($navPage->title) }}
                    </a>
                @endforeach
            @endif

            <div class="pt-2 pb-1 px-3 text-xs uppercase tracking-wider text-yellow-700">
                {{ __('navigation.rankings') }}</div>
            <a href="{{ route('ranking.characters') }}"
                class="block px-3 py-2 text-sm {{ request()->routeIs('ranking.characters') ? 'text-yellow-400 bg-yellow-900/20' : 'text-neutral-400 hover:text-yellow-200 hover:bg-white/5' }} transition">
                {{ __('navigation.ranking_characters') }}
            </a>
            <a href="{{ route('ranking.guilds') }}"
                class="block px-3 py-2 text-sm {{ request()->routeIs('ranking.guilds') ? 'text-yellow-400 bg-yellow-900/20' : 'text-neutral-400 hover:text-yellow-200 hover:bg-white/5' }} transition">
                {{ __('navigation.ranking_guilds') }}
            </a>
            <a href="{{ route('ranking.uniques') }}"
                class="block px-3 py-2 text-sm {{ request()->routeIs('ranking.uniques') ? 'text-yellow-400 bg-yellow-900/20' : 'text-neutral-400 hover:text-yellow-200 hover:bg-white/5' }} transition">
                {{ __('navigation.ranking_uniques') }}
            </a>

            @if ($showLanguageSwitch)
                <div class="pt-2 pb-1 px-3 text-xs uppercase tracking-wider text-yellow-700">
                    {{ __('navigation.language') }}
                </div>
                @foreach ($frontendLanguages as $locale => $label)
                    <a href="{{ route('language.switch', ['locale' => $locale]) }}"
                        class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                        {{ $label }}
                    </a>
                @endforeach
            @endif

            <div class="pt-3 mt-3 space-y-1" style="border-top: 1px solid rgba(77,70,53,0.2);">
                @auth
                    <div class="px-3 py-2 text-sm text-neutral-500">{{ Auth::user()->name }}</div>
                    <a href="{{ route('dashboard') }}"
                        class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                        {{ __('navigation.dashboard') }}
                    </a>
                    <a href="{{ route('profile.edit') }}"
                        class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                        {{ __('navigation.profile') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-3 py-2 text-sm text-red-400 hover:bg-red-900/10 transition">
                            {{ __('navigation.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="block px-3 py-2 text-sm text-neutral-400 hover:text-yellow-400 hover:bg-yellow-900/10 transition">
                        {{ __('navigation.login') }}
                    </a>
                    @settingsRegistrationOpen
                        <a href="{{ route('register') }}"
                            class="block px-3 py-2 text-sm font-bold text-yellow-500 hover:bg-yellow-900/10 transition">
                            {{ __('navigation.register') }}
                        </a>
                    @endsettingsRegistrationOpen
                @endauth
            </div>
        </div>
    </div>
</nav>
