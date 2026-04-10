<nav x-data="{ open: false, rankingOpen: false }"
    class="bg-white px-4 py-2.5 pb-3 border-b border-gray-200 dark:bg-gray-900 dark:border-gray-700 fixed left-0 right-0 top-0 z-10">
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

    <div class="flex justify-between items-center h-12 max-w-7xl mx-auto">
        <div class="flex items-center">
            <div class="shrink-0 flex items-center">
                <a href="{{ route('index') }}">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-white" />
                </a>
            </div>

            <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex items-center">
                <x-nav-link :href="route('index')" :active="request()->routeIs('index')">
                    {{ __('navigation.index') }}
                </x-nav-link>
                <x-nav-link :href="route('news.index')" :active="request()->routeIs('news.*')">
                    {{ __('navigation.news') }}
                </x-nav-link>
                <x-nav-link :href="route('downloads.index')" :active="request()->routeIs('downloads.*')">
                    {{ __('navigation.downloads') }}
                </x-nav-link>

                @if ($navPages->isNotEmpty())
                    <div class="relative border-b-2 border-transparent hover:border-primary-300" x-data="{ pagesDrop: false }"
                        @click.outside="pagesDrop = false">
                        <button @click="pagesDrop = !pagesDrop"
                            class="inline-flex items-center gap-1 px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none
                                {{ request()->routeIs('pages.*') ? 'text-gray-900 dark:text-white border-b-2 border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            {{ __('navigation.pages') }}
                            <svg class="w-4 h-4 transition-transform duration-200"
                                :class="pagesDrop ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="pagesDrop" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="absolute left-0 mt-2 w-52 rounded-lg border border-gray-200 bg-white p-1.5 shadow-lg dark:border-gray-700 dark:bg-gray-800 z-50"
                            style="display: none;">
                            @foreach ($navPages as $navPage)
                                <a href="{{ route('pages.show', $navPage->slug) }}"
                                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white transition">
                                    {{ e($navPage->title) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="relative border-b-2 border-transparent hover:border-primary-300" x-data="{ rankingDrop: false }"
                    @click.outside="rankingDrop = false">
                    <button @click="rankingDrop = !rankingDrop"
                        class="inline-flex items-center gap-1 px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none
                            {{ request()->routeIs('ranking.*') ? 'text-gray-900 dark:text-white border-b-2 border-indigo-500' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        {{ __('navigation.rankings') }}
                        <svg class="w-4 h-4 transition-transform duration-200" :class="rankingDrop ? 'rotate-180' : ''"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="rankingDrop" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="absolute left-0 mt-2 w-52 rounded-lg border border-gray-200 bg-white p-1.5 shadow-lg dark:border-gray-700 dark:bg-gray-800 z-50"
                        style="display: none;">
                        <a href="{{ route('ranking.characters') }}"
                            class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white transition">
                            {{ __('navigation.ranking_characters') }}
                        </a>
                        <a href="{{ route('ranking.guilds') }}"
                            class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white transition">
                            {{ __('navigation.ranking_guilds') }}
                        </a>
                        <a href="{{ route('ranking.uniques') }}"
                            class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white transition">
                            {{ __('navigation.ranking_uniques') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center">
            <div class="hidden sm:flex sm:items-center sm:gap-2">
                <button type="button" data-theme-toggle @click="$store.theme.toggle($event)"
                    :aria-pressed="$store.theme.isDark"
                    :aria-label="$store.theme.isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    :title="$store.theme.isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    class="cursor-pointer inline-flex size-10 items-center justify-center text-gray-500 transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <svg x-show="$store.theme.isDark" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-amber-300">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <svg x-show="!$store.theme.isDark" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="size-5 text-indigo-300 dark:text-indigo-200">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>

                @if ($showLanguageSwitch)
                    <x-dropdown align="right" width="40">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                {{ $currentLanguageLabel }}
                                <svg viewBox="0 0 20 20" fill="currentColor" class="-mr-1 ml-1 size-4">
                                    <path fill-rule="evenodd"
                                        d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @foreach ($frontendLanguages as $locale => $label)
                                <x-dropdown-link :href="route('language.switch', ['locale' => $locale])">
                                    {{ $label }}
                                </x-dropdown-link>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                @endif

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                {{ Auth::user()->silkroad_id }}
                                <svg viewBox="0 0 20 20" fill="currentColor" class="-mr-1 ml-1 size-4">
                                    <path fill-rule="evenodd"
                                        d="M5.22 8.22a.75.75 0 011.06 0L10 11.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 9.28a.75.75 0 010-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')">
                                {{ __('navigation.dashboard') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('navigation.profile') }}
                            </x-dropdown-link>
                            <div class="border-t border-gray-200 dark:border-gray-600"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('navigation.logout') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition">
                        {{ __('navigation.login') }}
                    </a>
                    @settingsRegistrationOpen
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition">
                            {{ __('navigation.register') }}
                        </a>
                    @endsettingsRegistrationOpen
                @endauth
            </div>

            <div class="flex items-center sm:hidden">
                <button type="button" data-theme-toggle @click="$store.theme.toggle($event)"
                    :aria-pressed="$store.theme.isDark"
                    :aria-label="$store.theme.isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    :title="$store.theme.isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                    class="mr-2 inline-flex size-10 items-center justify-center text-gray-500 transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <svg x-show="$store.theme.isDark" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-amber-300">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <svg x-show="!$store.theme.isDark" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="size-5 text-indigo-300 dark:text-indigo-200">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>

                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('index')" :active="request()->routeIs('index')">
                {{ __('navigation.index') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('news.index')" :active="request()->routeIs('news.*')">
                {{ __('navigation.news') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('downloads.index')" :active="request()->routeIs('downloads.*')">
                {{ __('navigation.downloads') }}
            </x-responsive-nav-link>

            @if ($navPages->isNotEmpty())
                <div class="px-4 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('navigation.pages') }}
                </div>
                @foreach ($navPages as $navPage)
                    <x-responsive-nav-link :href="route('pages.show', $navPage->slug)" :active="request()->is('pages/' . $navPage->slug)">
                        {{ e($navPage->title) }}
                    </x-responsive-nav-link>
                @endforeach
            @endif

            <div class="px-4 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                {{ __('navigation.rankings') }}
            </div>
            <x-responsive-nav-link :href="route('ranking.characters')" :active="request()->routeIs('ranking.characters')">
                {{ __('navigation.ranking_characters') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('ranking.guilds')" :active="request()->routeIs('ranking.guilds')">
                {{ __('navigation.ranking_guilds') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('ranking.uniques')" :active="request()->routeIs('ranking.uniques')">
                {{ __('navigation.ranking_uniques') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
            @auth
                <div class="px-4 mb-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('navigation.signed_in_as') }}</p>
                    <p class="font-medium text-base text-gray-800 dark:text-white">{{ Auth::user()->silkroad_id }}</p>
                </div>
            @endauth

            <div class="space-y-1">
                @if ($showLanguageSwitch)
                    <div class="px-4 py-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('navigation.language') }}
                    </div>
                    @foreach ($frontendLanguages as $locale => $label)
                        <x-responsive-nav-link :href="route('language.switch', ['locale' => $locale])">
                            {{ $label }}
                        </x-responsive-nav-link>
                    @endforeach
                @endif

                @guest
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('navigation.login') }}
                    </x-responsive-nav-link>
                    @settingsRegistrationOpen
                        <x-responsive-nav-link :href="route('register')">
                            {{ __('navigation.register') }}
                        </x-responsive-nav-link>
                    @endsettingsRegistrationOpen
                @endguest

                @auth
                    <x-responsive-nav-link :href="route('dashboard')">
                        {{ __('navigation.dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('navigation.profile') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('navigation.logout') }}
                        </x-responsive-nav-link>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
