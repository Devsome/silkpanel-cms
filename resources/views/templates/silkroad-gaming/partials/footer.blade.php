@php
    $partners = \App\Helpers\SettingHelper::get('partners', []);
    $contactEmail = \App\Helpers\SettingHelper::get('contact_email');
    $contactPhone = \App\Helpers\SettingHelper::get('contact_phone');
    $contactAddress = \App\Helpers\SettingHelper::get('contact_address');
    $socialFacebook = \App\Helpers\SettingHelper::get('social_facebook');
    $socialTwitter = \App\Helpers\SettingHelper::get('social_twitter');
    $socialInstagram = \App\Helpers\SettingHelper::get('social_instagram');
    $socialDiscord = \App\Helpers\SettingHelper::get('social_discord');
    $tosEnabled = (bool) \App\Helpers\SettingHelper::get('tos_enabled', false);
@endphp

<footer class="mt-auto border-t border-gray-800/50 bg-gray-950">
    {{-- Partners --}}
    @if (is_array($partners) && count($partners) > 0)
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 border-b border-gray-800/50">
            <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-emerald-400/70">
                {{ __('footer.partners') }}
            </h3>
            <div class="flex flex-wrap items-center gap-6">
                @foreach ($partners as $partner)
                    @if (!empty($partner['name']))
                        <a href="{{ e($partner['url'] ?? '#') }}" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-2 text-gray-400 hover:text-emerald-400 transition"
                            title="{{ e($partner['description'] ?? $partner['name']) }}">
                            @if (!empty($partner['logo']))
                                <img src="{{ asset('storage/' . $partner['logo']) }}" alt="{{ e($partner['name']) }}"
                                    class="h-8 w-auto object-contain opacity-70 hover:opacity-100 transition">
                            @endif
                            <span class="text-sm font-medium">{{ e($partner['name']) }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Brand --}}
            <div>
                @if (\App\Helpers\SettingHelper::get('logo'))
                    <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}"
                        alt="@settings('site_title', 'SilkPanel')" class="h-8 w-auto mb-3 opacity-80">
                @else
                    <span
                        class="text-lg font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">
                        @settings('site_title', 'SilkPanel')
                    </span>
                @endif
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                    @settings('site_description', 'A powerful Silkroad Online private server.')
                </p>
            </div>

            {{-- Contact --}}
            @if ($contactEmail || $contactPhone || $contactAddress)
                <div>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-emerald-400/70">
                        {{ __('footer.contact') }}
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        @if ($contactEmail)
                            <li>
                                <a href="mailto:{{ e($contactEmail) }}" class="hover:text-emerald-400 transition">
                                    {{ e($contactEmail) }}
                                </a>
                            </li>
                        @endif
                        @if ($contactPhone)
                            <li>{{ e($contactPhone) }}</li>
                        @endif
                        @if ($contactAddress)
                            <li>{{ e($contactAddress) }}</li>
                        @endif
                    </ul>
                </div>
            @endif

            {{-- Quick Links --}}
            <div>
                <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-emerald-400/70">
                    {{ __('footer.links') }}
                </h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('news.index') }}"
                            class="hover:text-emerald-400 transition">{{ __('navigation.news') }}</a></li>
                    <li><a href="{{ route('downloads.index') }}"
                            class="hover:text-emerald-400 transition">{{ __('navigation.downloads') }}</a></li>
                    <li><a href="{{ route('ranking.characters') }}"
                            class="hover:text-emerald-400 transition">{{ __('navigation.rankings') }}</a></li>
                    @if ($tosEnabled)
                        <li><a href="{{ route('terms') }}"
                                class="hover:text-emerald-400 transition">{{ __('footer.terms') }}</a></li>
                    @endif
                </ul>
            </div>

            {{-- Social --}}
            @if ($socialFacebook || $socialTwitter || $socialInstagram || $socialDiscord)
                <div>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-emerald-400/70">
                        {{ __('footer.social') }}
                    </h3>
                    <div class="flex items-center gap-3">
                        @if ($socialFacebook)
                            <a href="{{ e($socialFacebook) }}" target="_blank" rel="noopener noreferrer"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-800/50 text-gray-400 hover:bg-emerald-500/10 hover:text-emerald-400 transition"
                                title="Facebook">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </a>
                        @endif
                        @if ($socialTwitter)
                            <a href="{{ e($socialTwitter) }}" target="_blank" rel="noopener noreferrer"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-800/50 text-gray-400 hover:bg-emerald-500/10 hover:text-emerald-400 transition"
                                title="Twitter / X">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                            </a>
                        @endif
                        @if ($socialInstagram)
                            <a href="{{ e($socialInstagram) }}" target="_blank" rel="noopener noreferrer"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-800/50 text-gray-400 hover:bg-emerald-500/10 hover:text-emerald-400 transition"
                                title="Instagram">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                                </svg>
                            </a>
                        @endif
                        @if ($socialDiscord)
                            <a href="{{ e($socialDiscord) }}" target="_blank" rel="noopener noreferrer"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-800/50 text-gray-400 hover:bg-emerald-500/10 hover:text-emerald-400 transition"
                                title="Discord">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Copyright --}}
    <div class="border-t border-gray-800/50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-xs text-gray-600">
                &copy; {{ date('Y') }} @settings('site_title', 'SilkPanel CMS'). {{ __('footer.rights') }}
            </p>
        </div>
    </div>
</footer>
