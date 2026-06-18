<x-filament-panels::page>
    <style>
        .sp-version-hero {
            background: linear-gradient(135deg,
                    rgba(109, 40, 217, 0.08) 0%,
                    rgba(139, 92, 246, 0.05) 50%,
                    transparent 100%);
            border: 1px solid rgba(139, 92, 246, 0.15);
        }

        .dark .sp-version-hero {
            background: linear-gradient(135deg,
                    rgba(109, 40, 217, 0.15) 0%,
                    rgba(139, 92, 246, 0.08) 50%,
                    transparent 100%);
            border-color: rgba(139, 92, 246, 0.25);
        }

        .sp-version-card {
            transition: transform 0.15s ease;
        }

        .sp-version-card:hover {
            transform: translateY(-1px);
        }

        .sp-version-line {
            background: linear-gradient(to bottom, rgba(139, 92, 246, 0.25), transparent);
        }

        details.sp-older summary::-webkit-details-marker {
            display: none;
        }

        details.sp-older summary {
            list-style: none;
        }
    </style>

    {{-- Hero header --}}
    <div class="sp-version-hero rounded-2xl p-8 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-base text-gray-500 dark:text-gray-400">
                Changelog and release history for your installation.
            </div>

            <div class="flex items-center gap-4">
                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest font-medium mb-1">
                        Installed</p>
                    <span class="font-mono text-sm font-semibold text-gray-700 dark:text-gray-300">
                        v{{ $localVersion }}
                    </span>
                </div>

                @if (!$isUpToDate && $remoteVersion !== 'unknown')
                    <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest font-medium mb-1">
                            Available</p>
                        <span class="font-mono text-sm font-semibold text-gray-700 dark:text-gray-300">
                            v{{ $remoteVersion }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        @if (!$isUpToDate && $versionsBehind > 0)
            <div class="mt-5 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    You are {{ $versionsBehind }} {{ Str::plural('version', $versionsBehind) }} behind. Run these
                    commands
                    to update:
                </p>
                <div class="space-y-1.5">
                    <div
                        class="flex items-center gap-2 font-mono text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2">
                        <span class="text-gray-400 select-none">$</span>
                        <span>git pull origin master --no-edit && composer install --no-interaction</span>
                    </div>
                    <div
                        class="flex items-center gap-2 font-mono text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2">
                        <span class="text-gray-400 select-none">$</span>
                        <span>php artisan migrate --force && npm install && npm run build</span>
                    </div>
                </div>
            </div>
        @elseif ($isUpToDate)
            <div
                class="mt-5 flex items-center gap-2 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700">
                <x-filament::icon icon="heroicon-o-check-circle"
                    class="w-4 h-4 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    You're running the latest version.
                </p>
            </div>
        @endif
    </div>

    {{-- Missed versions (remote changelog, newer than local) --}}
    @if (!empty($missedVersions))
        <div class="mb-10">
            <h2 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-5">
                Available Updates
            </h2>
            <div class="space-y-5">
                @foreach ($missedVersions as $version => $entry)
                    <div class="relative pl-7 sp-version-card">
                        <div
                            class="absolute left-0 top-2 w-3 h-3 rounded-full bg-gray-300 dark:bg-gray-600 border-2 border-gray-200 dark:border-gray-700 ring-4 ring-white dark:ring-gray-950">
                        </div>
                        @if (!$loop->last)
                            <div class="absolute left-1.5 top-5 bottom-0 w-px sp-version-line"></div>
                        @endif

                        <div
                            class="bg-white dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700/50 rounded-xl p-5">
                            <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-1 mb-3">
                                <div class="flex items-baseline gap-3">
                                    <span
                                        class="font-mono text-xs font-semibold text-gray-500 dark:text-gray-400">v{{ $version }}</span>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $entry['title'] ?? '' }}</h3>
                                </div>
                                @if (!empty($entry['released_at']))
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ \Carbon\Carbon::parse($entry['released_at'])->format('M j, Y') }}
                                    </span>
                                @endif
                            </div>
                            @if (!empty($entry['description']))
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $entry['description'] }}</p>
                            @endif
                            @if (!empty($entry['features']))
                                <ul class="space-y-1.5">
                                    @foreach ($entry['features'] as $feature)
                                        <li class="flex items-start gap-2 text-xs text-gray-600 dark:text-gray-400">
                                            <span
                                                class="mt-1 w-1 h-1 rounded-full bg-gray-400 dark:bg-gray-500 flex-shrink-0"></span>
                                            <span>
                                                <span
                                                    class="font-medium text-gray-700 dark:text-gray-300">{{ $feature['title'] ?? '' }}</span>
                                                @if (!empty($feature['description']))
                                                    — {{ $feature['description'] }}
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Local changelog --}}
    <div>
        <h2 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-5">
            Release History
        </h2>

        @php
            $allVersions = collect($localChangelog);
            $current = $allVersions->filter(fn($_, $v) => $v === $localVersion);
            $older = $allVersions->filter(fn($_, $v) => version_compare($v, $localVersion, '<'));
        @endphp

        @forelse ($current as $version => $entry)
            {{-- Current installed version --}}
            <div class="relative pl-7 mb-5 sp-version-card">
                <div
                    class="absolute left-0 top-2 w-3 h-3 rounded-full bg-primary-600 dark:bg-primary-500 ring-4 ring-white dark:ring-gray-950">
                </div>
                @if ($older->isNotEmpty())
                    <div class="absolute left-1.5 top-5 bottom-0 w-px bg-gray-200 dark:bg-gray-700"></div>
                @endif

                <div
                    class="bg-white dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700/50 rounded-xl p-5 ring-1 ring-primary-500/20 dark:ring-primary-400/15">
                    <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-1 mb-3">
                        <div class="flex items-baseline gap-3 flex-wrap">
                            <span
                                class="font-mono text-xs font-semibold text-gray-500 dark:text-gray-400">v{{ $version }}</span>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $entry['title'] ?? '' }}
                            </h3>
                            <span
                                class="text-xs text-gray-400 dark:text-gray-500 font-medium border border-gray-200 dark:border-gray-700 px-1.5 py-0.5 rounded-md">Installed</span>
                        </div>
                        @if (!empty($entry['released_at']))
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                {{ \Carbon\Carbon::parse($entry['released_at'])->format('M j, Y') }}
                            </span>
                        @endif
                    </div>
                    @if (!empty($entry['description']))
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $entry['description'] }}</p>
                    @endif
                    @if (!empty($entry['features']))
                        <ul class="space-y-1.5">
                            @foreach ($entry['features'] as $feature)
                                <li class="flex items-start gap-2 text-xs text-gray-600 dark:text-gray-400">
                                    <span
                                        class="mt-1 w-1 h-1 rounded-full bg-gray-400 dark:bg-gray-500 flex-shrink-0"></span>
                                    <span>
                                        <span
                                            class="font-medium text-gray-700 dark:text-gray-300">{{ $feature['title'] ?? '' }}</span>
                                        @if (!empty($feature['description']))
                                            — {{ $feature['description'] }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @empty
        @endforelse

        {{-- Older versions in a collapsible --}}
        @if ($older->isNotEmpty())
            @php $olderLabel = 'Show ' . $older->count() . ' older ' . Str::plural('version', $older->count()); @endphp
            <details class="sp-older" x-data="{ open: false }" @toggle="open = $el.open">
                <summary class="relative pl-7 mb-5 cursor-pointer select-none">
                    <div
                        class="absolute left-0 top-2 w-3 h-3 rounded-full bg-gray-200 dark:bg-gray-700 ring-4 ring-white dark:ring-gray-950">
                    </div>
                    <div class="flex items-center gap-2 py-0.5">
                        <span
                            class="text-xs text-gray-400 dark:text-gray-500 font-medium hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <span x-text="open ? 'Hide older versions' : '{{ $olderLabel }}'"></span>
                        </span>
                        <svg class="w-3 h-3 text-gray-400 dark:text-gray-500 transition-transform"
                            :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </summary>

                <div class="space-y-5 mt-0">
                    @foreach ($older as $version => $entry)
                        <div class="relative pl-7 sp-version-card">
                            <div
                                class="absolute left-0 top-2 w-3 h-3 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-700 ring-4 ring-white dark:ring-gray-950">
                            </div>
                            @if (!$loop->last)
                                <div class="absolute left-1.5 top-5 bottom-0 w-px bg-gray-100 dark:bg-gray-800"></div>
                            @endif

                            <div
                                class="bg-white dark:bg-gray-900/20 border border-gray-100 dark:border-gray-700/40 rounded-xl p-5">
                                <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-1 mb-3">
                                    <div class="flex items-baseline gap-3">
                                        <span
                                            class="font-mono text-xs font-semibold text-gray-400 dark:text-gray-500">v{{ $version }}</span>
                                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-400">
                                            {{ $entry['title'] ?? '' }}</h3>
                                    </div>
                                    @if (!empty($entry['released_at']))
                                        <span class="text-xs text-gray-400 dark:text-gray-500">
                                            {{ \Carbon\Carbon::parse($entry['released_at'])->format('M j, Y') }}
                                        </span>
                                    @endif
                                </div>
                                @if (!empty($entry['description']))
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">
                                        {{ $entry['description'] }}</p>
                                @endif
                                @if (!empty($entry['features']))
                                    <ul class="space-y-1.5">
                                        @foreach ($entry['features'] as $feature)
                                            <li class="flex items-start gap-2 text-xs text-gray-400 dark:text-gray-500">
                                                <span
                                                    class="mt-1 w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600 flex-shrink-0"></span>
                                                <span>
                                                    <span
                                                        class="font-medium text-gray-500 dark:text-gray-400">{{ $feature['title'] ?? '' }}</span>
                                                    @if (!empty($feature['description']))
                                                        — {{ $feature['description'] }}
                                                    @endif
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </details>
        @endif

        @if ($current->isEmpty() && $older->isEmpty())
            <div class="text-center py-16 text-gray-400 dark:text-gray-600">
                <p class="text-sm">No changelog entries found in <code
                        class="font-mono text-xs">storage/app/version.json</code>.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
