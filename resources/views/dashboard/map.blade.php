<x-app-layout>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <style>
            .silkpanel-map-wrapper {
                position: relative;
                width: 100%;
                height: calc(100vh - 240px);
                min-height: 450px;
                border-radius: 0.75rem;
                overflow: hidden;
            }

            #map {
                display: block;
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
            }
        </style>
    @endpush

    @php
        $refreshInterval = max(10, (int) \App\Models\Setting::get('map_refresh_interval', 30));
        $maxChars = (int) \App\Models\Setting::get(
            'map_max_characters',
            \App\Services\SilkroadMapService::MAX_CHARACTERS,
        );
    @endphp

    <div x-data="silkroadMap" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('dashboard.world_map') }}</h1>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('dashboard.map_subtitle') }}
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                ← {{ __('dashboard.back_to_dashboard') }}
            </a>
        </div>

        <div
            class="flex flex-wrap items-center gap-3 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 px-4 py-3">
            <input type="search" x-model="search" placeholder="{{ __('dashboard.map_search') }}"
                class="flex-1 min-w-45 max-w-xs px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500" />

            <div class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                <span>{{ __('dashboard.map_online') }}:</span>
                <span class="font-semibold text-violet-600 dark:text-violet-400" x-text="visibleCount"></span>
                <template x-if="totalCount >= {{ $maxChars }}">
                    <span
                        class="text-xs text-amber-500">({{ __('dashboard.map_max_shown', ['n' => $maxChars]) }})</span>
                </template>
            </div>

            <div class="flex items-center gap-2 ml-auto">
                <span class="hidden sm:inline text-xs text-gray-400"
                    x-text="lastRefreshed ? '{{ __('dashboard.map_updated') }}: ' + lastRefreshed : ''"></span>

                <button @click="loadCharacters()" :disabled="loading"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg bg-violet-600 hover:bg-violet-500 text-white font-medium disabled:opacity-50 transition">
                    <svg x-show="!loading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span x-show="loading"
                        class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span
                        x-text="loading ? '{{ __('dashboard.map_loading') }}' : '{{ __('dashboard.map_refresh') }}'"></span>
                </button>

                <select x-model="currentInterval" @change="resetTimer()"
                    class="text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-auto py-1.5">
                    <option value="0">{{ __('dashboard.map_manual') }}</option>
                    <option value="30">30s</option>
                    <option value="60">60s</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 px-1">
            <span class="font-medium">{{ __('dashboard.map_level') }}:</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span>
                1–29</span>
            <span class="flex items-center gap-1.5"><span
                    class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span> 30–59</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                60–89</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>
                90+</span>
        </div>

        <div class="silkpanel-map-wrapper shadow-lg ring-1 ring-black/5 dark:ring-white/5">
            <div id="map"></div>
        </div>

        <div x-show="errorMsg" x-transition
            class="text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg px-4 py-3"
            x-text="errorMsg"></div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script src="{{ asset('js/silkpanel-map.js') }}"></script>

        <script>
            window._silkroadMapConfig = {
                apiUrl: @js(route('api.map.characters')),
                refreshInterval: {{ $refreshInterval }},
            };
        </script>
    @endpush
</x-app-layout>
