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
    class="fixed top-0 w-full z-50 h-20 flex items-center"
    style="background: rgba(6,8,15,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid rgba(34,211,238,0.08);">

    {{-- Subtle top accent line --}}
    <div class="absolute top-0 left-0 right-0 h-px" style="background: linear-gradient(90deg, transparent 0%, rgba(34,211,238,0.4) 30%, rgba(34,211,238,0.6) 50%, rgba(34,211,238,0.4) 70%, transparent 100%);"></div>

    <div class="w-full max-w-[1600px] mx-auto flex items-center justify-between px-4 md:px-8">

        {{-- Brand --}}
        <div class="flex items-center gap-10">
            <a href="{{ route('index') }}" class="flex items-center gap-3 shrink-0">
                @if (\App\Helpers\SettingHelper::get('logo'))
                    <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}"
                        alt="@settings('site_title', 'SilkPanel')" class="h-9 w-auto">
                @else
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 relative">
                            <div class="absolute inset-0 border border-cyan-400/60" style="clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);"></div>
                            <div class="absolute inset-1 bg-cyan-400/20" style="clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);"></div>
                        </div>
                        <span class="ag-font-display text-lg font-bold tracking-wider" style="color: var(--ag-on-surface);">
                            @settings('site_title', 'SilkPanel')
                        </span>
                    </div>
                @endif
            </a>

            {{-- Desktop Nav Links --}}
            <div class="hidden lg:flex items-center gap-1">
                <a href="{{ route('index') }}"
                    class="px-3 py-2 ag-font-display text-xs font-semibold tracking-wider uppercase transition-colors duration-200
                        {{ request()->routeIs('index') ? 'ag-text-primary' : 'ag-text-muted hover:ag-text-surface' }}">
                    {{ __('navigation.index') }}
                </a>
                <a href="{{ route('news.index') }}"
                    class="px-3 py-2 ag-font-display text-xs font-semibold tracking-wider uppercase transition-colors duration-200
                        {{ request()->routeIs('news.*') ? 'ag-text-primary' : 'ag-text-muted hover:ag-text-surface' }}">
                    {{ __('navigation.news') }}
                </a>
                <a href="{{ route('ranking.characters') }}"
                    class="px-3 py-2 ag-font-display text-xs font-semibold tracking-wider uppercase transition-colors duration-200
                        {{ request()->routeIs('ranking.*') ? 'ag-text-primary' : 'ag-text-muted hover:ag-text-surface' }}">
                    {{ __('navigation.rankings') }}
                </a>
                <a href="{{ route('downloads.index') }}"
                    class="px-3 py-2 ag-font-display text-xs font-semibold tracking-wider uppercase transition-colors duration-200
                        {{ request()->routeIs('downloads.*') ? 'ag-text-primary' : 'ag-text-muted hover:ag-text-surface' }}">
                    {{ __('navigation.downloads') }}
                </a>

                @if ($navPages->isNotEmpty())
                    <div class="relative" x-data="{ pagesDrop: false }" @click.outside="pagesDrop = false">
                        <button @click="pagesDrop = !pagesDrop"
                            class="flex items-center gap-1 px-3 py-2 ag-font-display text-xs font-semibold tracking-wider uppercase transition-colors duration-200
                                {{ request()->routeIs('pages.*') ? 'ag-text-primary' : 'ag-text-muted hover:ag-text-surface' }}">
                            {{ __('navigation.pages') }}
                            <svg class="w-3 h-3 transition-transform duration-200" :class="pagesDrop ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="pagesDrop" x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute left-0 mt-2 w-48 py-1 ag-card shadow-2xl"
                            style="display:none; background: rgba(9,12,23,0.97); border-color: rgba(34,211,238,0.15);">
                            @foreach ($navPages as $navPage)
                                <a href="{{ route('pages.show', $navPage->slug) }}"
                                    class="block px-4 py-2.5 text-xs ag-font-display font-semibold tracking-wide uppercase ag-text-muted hover:ag-text-primary hover:bg-cyan-900/10 transition-colors">
                                    {{ e($navPage->title) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Side --}}
        <div class="flex items-center gap-3">

            {{-- Online counter --}}
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 ag-card-low text-xs">
                <span class="ag-online-dot"></span>
                <x-online-counter class="ag-font-mono text-xs ag-text-primary" />
            </div>

            {{-- Language switcher --}}
            @if ($showLanguageSwitch)
                <div class="relative" x-data="{ langDrop: false }" @click.outside="langDrop = false">
                    <button @click="langDrop = !langDrop"
                        class="flex items-center gap-1.5 px-3 py-1.5 ag-card-low text-xs ag-font-display font-semibold tracking-wider ag-text-muted hover:ag-text-surface transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                        {{ $currentLanguageLabel }}
                    </button>
                    <div x-show="langDrop" x-transition class="absolute right-0 mt-2 w-40 py-1 ag-card shadow-xl"
                        style="display:none; background: rgba(9,12,23,0.97); border-color: rgba(34,211,238,0.15);">
                        @foreach ($frontendLanguages as $locale => $label)
                            <a href="{{ route('language.switch', $locale) }}"
                                class="block px-4 py-2.5 text-xs ag-font-display font-semibold tracking-wide uppercase transition-colors
                                    {{ $locale === $currentFrontendLocale ? 'ag-text-primary' : 'ag-text-muted hover:ag-text-primary hover:bg-cyan-900/10' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Auth --}}
            @auth
                <div class="relative" x-data="{ userDrop: false }" @click.outside="userDrop = false">
                    <button @click="userDrop = !userDrop"
                        class="flex items-center gap-2 px-3 py-1.5 ag-card-glow ag-font-display text-xs font-semibold tracking-wider uppercase ag-text-primary hover:bg-cyan-900/10 transition-colors">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-xs" style="background: rgba(34,211,238,0.15); color: var(--ag-primary);">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                        <svg class="w-3 h-3 transition-transform" :class="userDrop ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="userDrop" x-transition class="absolute right-0 mt-2 w-52 py-1 ag-card shadow-xl"
                        style="display:none; background: rgba(9,12,23,0.97); border-color: rgba(34,211,238,0.15);">
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center gap-2.5 px-4 py-3 text-xs ag-font-display font-semibold tracking-wide uppercase ag-text-muted hover:ag-text-primary hover:bg-cyan-900/10 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/></svg>
                            {{ __('navigation.dashboard') }}
                        </a>
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2.5 px-4 py-3 text-xs ag-font-display font-semibold tracking-wide uppercase ag-text-muted hover:ag-text-primary hover:bg-cyan-900/10 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ __('navigation.profile') }}
                        </a>
                        <div class="my-1 border-t ag-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2.5 px-4 py-3 text-xs ag-font-display font-semibold tracking-wide uppercase text-red-400 hover:bg-red-900/10 hover:text-red-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                {{ __('navigation.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                    class="px-4 py-2 ag-btn-secondary text-xs">
                    {{ __('navigation.login') }}
                </a>
                <a href="{{ route('register') }}"
                    class="px-4 py-2 ag-btn-primary text-xs">
                    {{ __('navigation.register') }}
                </a>
            @endauth

            {{-- Mobile hamburger --}}
            <button @click="open = !open"
                class="lg:hidden p-2 ag-text-muted hover:ag-text-surface transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display:none" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition class="lg:hidden absolute top-20 left-0 right-0 border-t ag-divider"
        style="display:none; background: rgba(6,8,15,0.97); backdrop-filter: blur(16px);">
        <div class="px-4 py-4 space-y-1">
            <a href="{{ route('index') }}" class="block px-4 py-3 ag-font-display text-xs font-semibold tracking-widest uppercase {{ request()->routeIs('index') ? 'ag-text-primary' : 'ag-text-muted' }}">{{ __('navigation.index') }}</a>
            <a href="{{ route('news.index') }}" class="block px-4 py-3 ag-font-display text-xs font-semibold tracking-widest uppercase {{ request()->routeIs('news.*') ? 'ag-text-primary' : 'ag-text-muted' }}">{{ __('navigation.news') }}</a>
            <a href="{{ route('ranking.characters') }}" class="block px-4 py-3 ag-font-display text-xs font-semibold tracking-widest uppercase {{ request()->routeIs('ranking.*') ? 'ag-text-primary' : 'ag-text-muted' }}">{{ __('navigation.rankings') }}</a>
            <a href="{{ route('downloads.index') }}" class="block px-4 py-3 ag-font-display text-xs font-semibold tracking-widest uppercase {{ request()->routeIs('downloads.*') ? 'ag-text-primary' : 'ag-text-muted' }}">{{ __('navigation.downloads') }}</a>
            @foreach ($navPages as $navPage)
                <a href="{{ route('pages.show', $navPage->slug) }}" class="block px-4 py-3 ag-font-display text-xs font-semibold tracking-widest uppercase ag-text-muted">{{ e($navPage->title) }}</a>
            @endforeach
            @auth
                <div class="pt-2 border-t ag-divider">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-3 ag-font-display text-xs font-semibold tracking-widest uppercase ag-text-muted">{{ __('navigation.dashboard') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 ag-font-display text-xs font-semibold tracking-widest uppercase text-red-400">{{ __('navigation.logout') }}</button>
                    </form>
                </div>
            @else
                <div class="pt-2 border-t ag-divider flex gap-3">
                    <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2.5 ag-btn-secondary text-xs">{{ __('navigation.login') }}</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2.5 ag-btn-primary text-xs">{{ __('navigation.register') }}</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
