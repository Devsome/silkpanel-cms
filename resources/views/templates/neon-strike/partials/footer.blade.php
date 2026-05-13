@php
    $partners = \App\Helpers\SettingHelper::get('partners', []);
    $contactEmail = \App\Helpers\SettingHelper::get('contact_email');
    $contactPhone = \App\Helpers\SettingHelper::get('contact_phone');
    $socialFacebook = \App\Helpers\SettingHelper::get('social_facebook');
    $socialTwitter = \App\Helpers\SettingHelper::get('social_twitter');
    $socialInstagram = \App\Helpers\SettingHelper::get('social_instagram');
    $socialDiscord = \App\Helpers\SettingHelper::get('social_discord');
    $tosEnabled = (bool) \App\Helpers\SettingHelper::get('tos_enabled', false);
    $currentYear = date('Y');
@endphp

<footer class="mt-auto border-t border-violet-500/20 bg-zinc-950">
    <div class="h-px bg-linear-to-r from-transparent via-violet-500/40 to-transparent"></div>

    {{-- Partners --}}
    @if (is_array($partners) && count($partners) > 0)
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 border-b border-zinc-800/60">
            <p class="mb-4 text-xs font-mono uppercase tracking-[0.25em] text-violet-400/60">{{ __('footer.partners') }}
            </p>
            <div class="flex flex-wrap items-center gap-6">
                @foreach ($partners as $partner)
                    @if (!empty($partner['name']))
                        <a href="{{ e($partner['url'] ?? '#') }}" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-2 text-zinc-500 hover:text-violet-400 transition"
                            title="{{ e($partner['description'] ?? $partner['name']) }}">
                            @if (!empty($partner['logo']))
                                <img src="{{ asset('storage/' . $partner['logo']) }}" alt="{{ e($partner['name']) }}"
                                    class="h-7 w-auto object-contain opacity-60 hover:opacity-100 transition">
                            @endif
                            <span class="text-xs font-mono uppercase tracking-wider">{{ e($partner['name']) }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            {{-- Brand --}}
            <div class="lg:col-span-2">
                @if (\App\Helpers\SettingHelper::get('logo'))
                    <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}"
                        alt="@settings('site_title', 'SilkPanel')" class="h-8 w-auto mb-3 opacity-80">
                @else
                    <span
                        class="text-lg font-black uppercase tracking-[0.2em] bg-linear-to-r from-violet-400 via-fuchsia-400 to-cyan-400 bg-clip-text text-transparent">
                        @settings('site_title', 'SilkPanel')
                    </span>
                @endif
                <p class="mt-3 text-sm text-zinc-500 leading-relaxed max-w-xs">
                    @settings('site_description', 'A cyberpunk-enhanced Silkroad Online experience.')
                </p>
                {{-- Social Links --}}
                @if ($socialFacebook || $socialTwitter || $socialInstagram || $socialDiscord)
                    <div class="mt-4 flex items-center gap-3">
                        @if ($socialDiscord)
                            <a href="{{ e($socialDiscord) }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center justify-center w-8 h-8 border border-zinc-700 text-zinc-500 hover:text-violet-400 hover:border-violet-500/50 transition text-xs font-mono">D</a>
                        @endif
                        @if ($socialTwitter)
                            <a href="{{ e($socialTwitter) }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center justify-center w-8 h-8 border border-zinc-700 text-zinc-500 hover:text-violet-400 hover:border-violet-500/50 transition text-xs font-mono">X</a>
                        @endif
                        @if ($socialFacebook)
                            <a href="{{ e($socialFacebook) }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center justify-center w-8 h-8 border border-zinc-700 text-zinc-500 hover:text-violet-400 hover:border-violet-500/50 transition text-xs font-mono">F</a>
                        @endif
                        @if ($socialInstagram)
                            <a href="{{ e($socialInstagram) }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center justify-center w-8 h-8 border border-zinc-700 text-zinc-500 hover:text-violet-400 hover:border-violet-500/50 transition text-xs font-mono">I</a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Contact --}}
            @if ($contactEmail || $contactPhone)
                <div>
                    <p class="mb-3 text-xs font-mono uppercase tracking-[0.25em] text-violet-400/60">
                        {{ __('footer.contact') }}</p>
                    <ul class="space-y-2">
                        @if ($contactEmail)
                            <li><a href="mailto:{{ e($contactEmail) }}"
                                    class="text-sm text-zinc-500 hover:text-violet-400 transition font-mono">{{ e($contactEmail) }}</a>
                            </li>
                        @endif
                        @if ($contactPhone)
                            <li><span class="text-sm text-zinc-500 font-mono">{{ e($contactPhone) }}</span></li>
                        @endif
                    </ul>
                </div>
            @endif

            {{-- Links --}}
            <div>
                <p class="mb-3 text-xs font-mono uppercase tracking-[0.25em] text-violet-400/60">
                    {{ __('footer.links') }}</p>
                <ul class="space-y-2">
                    <li><a href="{{ route('index') }}"
                            class="text-sm text-zinc-500 hover:text-violet-400 transition">{{ __('navigation.index') }}</a>
                    </li>
                    <li><a href="{{ route('news.index') }}"
                            class="text-sm text-zinc-500 hover:text-violet-400 transition">{{ __('navigation.news') }}</a>
                    </li>
                    <li><a href="{{ route('ranking.characters') }}"
                            class="text-sm text-zinc-500 hover:text-violet-400 transition">{{ __('navigation.rankings') }}</a>
                    </li>
                    @if ($tosEnabled)
                        <li><a href="{{ route('terms') }}"
                                class="text-sm text-zinc-500 hover:text-violet-400 transition">{{ __('footer.tos') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="border-t border-zinc-800/60">
        <div
            class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs font-mono text-zinc-600">
                &copy; {{ $currentYear }} @settings('site_title', 'SilkPanel') — <span class="text-violet-500/60">NEON
                    STRIKE</span>
            </p>
            <p class="text-xs font-mono text-zinc-700">
                Powered by <span class="text-violet-500/60">SilkPanel</span>
            </p>
        </div>
    </div>
</footer>
