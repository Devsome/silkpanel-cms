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

<footer class="mt-auto" style="border-top: 1px solid rgba(34,211,238,0.08);">
    {{-- Top section --}}
    <div class="mx-auto max-w-[1600px] px-4 md:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10">

            {{-- Brand column --}}
            <div class="md:col-span-4">
                <div class="flex items-center gap-2 mb-4">
                    @if (\App\Helpers\SettingHelper::get('logo'))
                        <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}"
                            alt="@settings('site_title', 'SilkPanel')" class="h-8 w-auto opacity-80">
                    @else
                        <span class="ag-font-display text-base font-bold tracking-wider ag-text-primary">
                            @settings('site_title', 'SilkPanel')
                        </span>
                    @endif
                </div>
                <p class="text-xs leading-relaxed ag-text-muted max-w-xs">
                    @settings('site_description', 'A premium Silkroad Online private server experience. Join thousands of players in the ultimate MMORPG journey.')
                </p>

                {{-- Social links --}}
                @if ($socialDiscord || $socialFacebook || $socialTwitter || $socialInstagram)
                    <div class="flex gap-3 mt-6">
                        @if ($socialDiscord)
                            <a href="{{ e($socialDiscord) }}" target="_blank" rel="noopener noreferrer"
                                class="w-8 h-8 flex items-center justify-center ag-card hover:border-cyan-400/30 transition-colors ag-text-muted hover:ag-text-primary">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.317 4.37a19.791 19.791 0 00-4.885-1.515.074.074 0 00-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 00-5.487 0 12.64 12.64 0 00-.617-1.25.077.077 0 00-.079-.037A19.736 19.736 0 003.677 4.37a.07.07 0 00-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 00.031.057 19.9 19.9 0 005.993 3.03.078.078 0 00.084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 00-.041-.106 13.107 13.107 0 01-1.872-.892.077.077 0 01-.008-.128 10.2 10.2 0 00.372-.292.074.074 0 01.077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 01.078.01c.12.098.246.198.373.292a.077.077 0 01-.006.127 12.299 12.299 0 01-1.873.892.077.077 0 00-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 00.084.028 19.839 19.839 0 006.002-3.03.077.077 0 00.032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 00-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                                </svg>
                            </a>
                        @endif
                        @if ($socialFacebook)
                            <a href="{{ e($socialFacebook) }}" target="_blank" rel="noopener noreferrer"
                                class="w-8 h-8 flex items-center justify-center ag-card hover:border-cyan-400/30 transition-colors ag-text-muted hover:ag-text-primary">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        @endif
                        @if ($socialTwitter)
                            <a href="{{ e($socialTwitter) }}" target="_blank" rel="noopener noreferrer"
                                class="w-8 h-8 flex items-center justify-center ag-card hover:border-cyan-400/30 transition-colors ag-text-muted hover:ag-text-primary">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Navigation links --}}
            <div class="md:col-span-2">
                <p class="ag-font-display text-xs font-semibold tracking-widest uppercase ag-text-primary mb-4">{{ __('footer.navigation') }}</p>
                <div class="space-y-2.5">
                    <a href="{{ route('index') }}" class="block text-xs ag-text-muted hover:ag-text-surface transition-colors">{{ __('navigation.index') }}</a>
                    <a href="{{ route('news.index') }}" class="block text-xs ag-text-muted hover:ag-text-surface transition-colors">{{ __('navigation.news') }}</a>
                    <a href="{{ route('downloads.index') }}" class="block text-xs ag-text-muted hover:ag-text-surface transition-colors">{{ __('navigation.downloads') }}</a>
                    <a href="{{ route('ranking.characters') }}" class="block text-xs ag-text-muted hover:ag-text-surface transition-colors">{{ __('navigation.rankings') }}</a>
                    @if ($tosEnabled)
                        <a href="{{ route('terms') }}" class="block text-xs ag-text-muted hover:ag-text-surface transition-colors">{{ __('footer.terms') }}</a>
                    @endif
                </div>
            </div>

            {{-- Account links --}}
            <div class="md:col-span-2">
                <p class="ag-font-display text-xs font-semibold tracking-widest uppercase ag-text-primary mb-4">{{ __('footer.account') }}</p>
                <div class="space-y-2.5">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block text-xs ag-text-muted hover:ag-text-surface transition-colors">{{ __('navigation.dashboard') }}</a>
                        <a href="{{ route('profile.edit') }}" class="block text-xs ag-text-muted hover:ag-text-surface transition-colors">{{ __('navigation.profile') }}</a>
                        <a href="{{ route('donate.index') }}" class="block text-xs ag-text-muted hover:ag-text-surface transition-colors">{{ __('navigation.donation') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="block text-xs ag-text-muted hover:ag-text-surface transition-colors">{{ __('navigation.login') }}</a>
                        <a href="{{ route('register') }}" class="block text-xs ag-text-muted hover:ag-text-surface transition-colors">{{ __('navigation.register') }}</a>
                    @endauth
                </div>
            </div>

            {{-- Contact --}}
            @if ($contactEmail || $contactPhone || $contactAddress)
                <div class="md:col-span-4">
                    <p class="ag-font-display text-xs font-semibold tracking-widest uppercase ag-text-primary mb-4">{{ __('footer.contact') }}</p>
                    <div class="space-y-2.5">
                        @if ($contactEmail)
                            <a href="mailto:{{ e($contactEmail) }}" class="flex items-center gap-2 text-xs ag-text-muted hover:ag-text-surface transition-colors">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ e($contactEmail) }}
                            </a>
                        @endif
                        @if ($contactPhone)
                            <p class="flex items-center gap-2 text-xs ag-text-muted">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ e($contactPhone) }}
                            </p>
                        @endif
                        @if ($contactAddress)
                            <p class="flex items-start gap-2 text-xs ag-text-muted">
                                <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ e($contactAddress) }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Partners --}}
        @if (!empty($partners))
            <div class="mt-10 pt-8 border-t ag-divider">
                <p class="ag-font-display text-xs font-semibold tracking-widest uppercase ag-text-muted mb-4">{{ __('footer.partners') }}</p>
                <div class="flex flex-wrap gap-4">
                    @foreach ($partners as $partner)
                        @if (!empty($partner['url']))
                            <a href="{{ e($partner['url']) }}" target="_blank" rel="noopener noreferrer"
                                class="ag-card px-4 py-2 hover:border-cyan-400/20 transition-colors opacity-60 hover:opacity-100">
                                @if (!empty($partner['image']))
                                    <img src="{{ asset('storage/' . $partner['image']) }}" alt="{{ e($partner['name'] ?? '') }}" class="h-6 w-auto">
                                @else
                                    <span class="text-xs ag-text-muted">{{ e($partner['name'] ?? '') }}</span>
                                @endif
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Bottom bar --}}
    <div style="border-top: 1px solid var(--ag-outline-variant);">
        <div class="mx-auto max-w-[1600px] px-4 md:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs ag-text-muted">
                &copy; {{ date('Y') }} @settings('site_title', 'SilkPanel CMS'). {{ __('footer.all_rights_reserved') }}
            </p>
            <p class="text-xs ag-text-muted">
                Powered by <span class="ag-text-primary font-semibold">SilkPanel CMS</span>
            </p>
        </div>
    </div>
</footer>
