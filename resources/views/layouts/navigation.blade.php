<nav x-data="{ open: false }"
    class="bg-white px-4 py-2.5 pb-3 border-b border-gray-200 dark:bg-gray-900 dark:border-gray-400 fixed left-0 right-0 top-0 z-10">
    @php
        $frontendLanguages = $frontendLanguages ?? \App\Helpers\SettingHelper::frontendLanguagesWithLabels();
        $currentFrontendLocale = $currentFrontendLocale ?? app()->getLocale();
        $currentLanguageLabel = $frontendLanguages[$currentFrontendLocale] ?? strtoupper($currentFrontendLocale);
        $showLanguageSwitch = count($frontendLanguages) > 1;
    @endphp

    <div class="flex justify-between items-center h-12">
        <div class="flex">
            <div class="shrink-0 flex items-center">
                <a href="{{ route('index') }}">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>
            </div>

            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                <x-nav-link :href="route('index')" :active="request()->routeIs('index')">
                    {{ __('navigation.index') }}
                </x-nav-link>
            </div>
        </div>

        <div class="flex items-center">
            <div class="hidden sm:flex sm:items-center">
                @if ($showLanguageSwitch)
                    <x-dropdown align="right" width="40">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-500 dark:text-white hover:text-primary-700 focus:outline-none transition ease-in-out duration-150">
                                {{ __('navigation.language') }}: {{ $currentLanguageLabel }}
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="-mr-1 size-5 text-gray-400">
                                    <path
                                        d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
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

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-500 dark:text-white hover:text-primary-700 focus:outline-none transition ease-in-out duration-150">
                            {{ __('navigation.account') }}
                            <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                class="-mr-1 size-5 text-gray-400">
                                <path
                                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                    clip-rule="evenodd" fill-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @auth
                            <div class="px-4 py-3">
                                <p class="text-sm text-gray-700 dark:text-gray-400">
                                    {{ __('navigation.signed_in_as') }}
                                </p>
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                    {{ Auth::user()->name }}
                                </p>
                            </div>
                        @endauth

                        <div class="py-1">
                            @auth
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('navigation.profile') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('dashboard')">
                                    {{ __('navigation.dashboard') }}
                                </x-dropdown-link>
                            @endauth
                            @guest
                                <x-dropdown-link :href="route('login')">
                                    {{ __('navigation.login') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('register')">
                                    {{ __('navigation.register') }}
                                </x-dropdown-link>
                            @endguest
                        </div>

                        @auth
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                        {{ __('navigation.logout') }}
                                    </x-dropdown-link>
                                </form>
                            </div>
                        @endauth
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                @auth
                    <p class="text-sm text-gray-700 dark:text-gray-400">
                        {{ __('navigation.signed_in_as') }}
                    </p>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                @endauth
            </div>

            <div class="mt-3 space-y-1">
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
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('navigation.register') }}
                    </x-responsive-nav-link>
                @endguest

                @auth
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('navigation.profile') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                            {{ __('navigation.logout') }}
                        </x-responsive-nav-link>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
