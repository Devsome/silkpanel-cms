<x-filament-panels::page>
    @php
        $config = $this->getMapConfig();
    @endphp

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <style>
            .silkpanel-admin-map-wrapper {
                position: relative;
                width: 100%;
                height: 70vh;
                min-height: 500px;
                border-radius: 0.5rem;
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

            .leaflet-popup-content-wrapper {
                background: #1e1e2e;
                color: #cdd6f4;
                border: 1px solid #45475a;
            }

            .leaflet-popup-tip {
                background: #1e1e2e;
            }

            @keyframes silk-pulse {

                0%,
                100% {
                    opacity: 1;
                    transform: scale(1);
                }

                50% {
                    opacity: .45;
                    transform: scale(.75);
                }
            }
        </style>
    @endpush

    <div x-data="silkpanelAdminMap" class="space-y-4">
        <div class="flex flex-wrap items-center gap-3">
            <input type="text" x-model="search" placeholder="{{ __('filament/map.search_placeholder') }}"
                class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 w-52" />

            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" x-model="showOffline"
                    class="rounded border-gray-600 text-violet-600 focus:ring-violet-500" />
                <span class="text-sm text-gray-400">{{ __('filament/map.show_offline') }}</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" x-model="showJobOnly"
                    class="rounded border-gray-600 text-orange-500 focus:ring-orange-500" />
                <span class="text-sm text-orange-400">{{ __('filament/map.show_job_only') }}</span>
            </label>

            <div class="flex items-center gap-1.5">
                <span class="text-sm text-gray-400">
                    <template x-if="showOffline">
                        <span>{{ __('filament/map.all_label') }}</span>
                    </template>
                    <template x-if="!showOffline">
                        <span>{{ __('filament/map.online_label') }}</span>
                    </template>:
                </span>
                <span class="font-semibold text-violet-400" x-text="visibleCount"></span>
                <template
                    x-if="totalCount >= {{ (int) \App\Models\Setting::get('map_max_characters', \App\Services\SilkroadMapService::MAX_CHARACTERS) }}">
                    <span
                        class="ml-1 text-xs text-amber-400">({{ __('filament/map.limit_reached', ['n' => (int) \App\Models\Setting::get('map_max_characters', \App\Services\SilkroadMapService::MAX_CHARACTERS)]) }})</span>
                </template>
            </div>

            <div class="flex items-center gap-2 ml-auto">
                <span class="text-xs text-gray-500"
                    x-text="lastRefreshed ? '{{ __('filament/map.refreshed_label') }}: ' + lastRefreshed : ''"></span>
                <button @click="loadCharacters()" :disabled="loading"
                    class="px-3 py-1.5 text-sm rounded-lg bg-violet-700 hover:bg-violet-600 text-white disabled:opacity-50 transition">
                    <span x-show="!loading">&#8635; {{ __('filament/map.refresh_btn') }}</span>
                    <span x-show="loading">{{ __('filament/map.loading_btn') }}</span>
                </button>
                <select x-model="currentInterval" @change="resetTimer()"
                    class="text-sm rounded-lg border border-gray-300 bg-white text-gray-900 px-2 py-1.5">
                    <option value="0">{{ __('filament/map.manual_option') }}</option>
                    <option value="10">10s</option>
                    <option value="30">30s</option>
                    <option value="60">60s</option>
                </select>
            </div>
        </div>

        {{-- Map container: silkpanel-map.js hardcodes L.map('map') --}}
        <div class="silkpanel-admin-map-wrapper">
            <div id="map"></div>
        </div>

        {{-- Error banner --}}
        <div x-show="errorMsg" x-transition
            class="text-sm text-red-400 bg-red-900/30 border border-red-700 rounded-lg px-4 py-2" x-text="errorMsg">
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script src="{{ asset('js/silkpanel-map.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('silkpanelAdminMap', () => {
                    const cfg = @js($config);

                    return {
                        search: '',
                        showOffline: false,
                        showJobOnly: false,
                        visibleCount: 0,
                        totalCount: 0,
                        loading: false,
                        errorMsg: '',
                        lastRefreshed: '',
                        currentInterval: cfg.refresh_interval ?? 30,
                        _allCharacters: [],
                        _timer: null,

                        init() {
                            this.$nextTick(() => {
                                if (typeof xSROMap === 'undefined') {
                                    this.errorMsg = '{{ __('filament/map.error_lib') }}';
                                    return;
                                }
                                xSROMap.init();
                                this.loadCharacters();
                                if (this.currentInterval > 0) {
                                    this._timer = setInterval(() => this.loadCharacters(), this
                                        .currentInterval * 1000);
                                }
                                this.$watch('search', () => this.applySearch());
                                this.$watch('showOffline', () => this.loadCharacters());
                                this.$watch('showJobOnly', () => this.applySearch());
                            });
                        },

                        destroy() {
                            clearInterval(this._timer);
                        },

                        async loadCharacters() {
                            this.loading = true;
                            this.errorMsg = '';
                            try {
                                const url = this.showOffline ?
                                    cfg.api_url + '?include_offline=1' :
                                    cfg.api_url;
                                const res = await fetch(url, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    },
                                    credentials: 'same-origin',
                                });
                                if (!res.ok) throw new Error('HTTP ' + res.status);
                                const json = await res.json();
                                if (json.error) throw new Error(json.error);

                                this._allCharacters.forEach(c => xSROMap.RemovePlayer(c.char_id));
                                this._allCharacters = json.data || [];
                                this.totalCount = json.total || 0;
                                this.lastRefreshed = new Date().toLocaleTimeString();
                                this.applySearch();
                            } catch (err) {
                                this.errorMsg = '{{ __('filament/map.error_load') }}: ' + err.message;
                            } finally {
                                this.loading = false;
                            }
                        },

                        applySearch() {
                            const term = this.search.trim().toLowerCase();
                            const isSearching = !!term;

                            // Apply job-only filter first, then search on top
                            const source = this.showJobOnly ?
                                this._allCharacters.filter(c => c.has_job_suit) :
                                this._allCharacters;

                            const matched = isSearching ?
                                source.filter(c => c.name.toLowerCase().includes(term)) :
                                source;

                            this.visibleCount = matched.length;

                            const matchedIds = new Set(matched.map(c => c.char_id));

                            // Re-render ALL characters — visibility controlled via options.
                            this._allCharacters.forEach(c => xSROMap.RemovePlayer(c.char_id));

                            this._allCharacters.forEach(c => {
                                const isMatch = !isSearching || matchedIds.has(c.char_id);
                                const isOffline = c.is_online === false;
                                const isInJobFilter = !this.showJobOnly || c.has_job_suit;

                                // Skip entirely when job-only is active and char has no job suit
                                if (!isInJobFilter) return;

                                let color;
                                if (isMatch && isSearching) {
                                    color = '#facc15'; // yellow – search hit
                                } else if (isOffline) {
                                    color = '#4b5563'; // gray – offline
                                } else if (c.has_job_suit) {
                                    color = '#f97316'; // orange – job suit
                                } else {
                                    color = this._levelColor(c.level);
                                }

                                const suitBadge = c.has_job_suit ?
                                    '<br/><span style="color:#f97316;font-weight:600">{{ __('filament/map.popup_job_suit') }}</span>' :
                                    '';
                                const offlineBadge = isOffline ?
                                    '<br/><span style="color:#6b7280;font-weight:600">{{ __('filament/map.popup_offline') }}</span>' :
                                    '';
                                const popup = `<div style="min-width:140px;line-height:1.6;color:#cdd6f4">
                                    <strong>${this._esc(c.name)}</strong><br/>
                                    {{ __('filament/map.popup_level') }} <b>${c.level}</b>
                                    ${c.guild_id ? `<br/>{{ __('filament/map.popup_guild_id') }}: ${c.guild_id}` : ''}
                                    ${suitBadge}
                                    ${offlineBadge}
                                    ${c.updated_at ? `<br/><small style="color:#888">{{ __('filament/map.popup_last_logout') }}: ${c.updated_at}</small>` : ''}
                                </div>`;

                                xSROMap.AddPlayer(c.char_id, popup, c.pos_x, c.pos_y, c.pos_z, c
                                    .region, {
                                        color,
                                        highlighted: isMatch && isSearching,
                                        pulse: isMatch && isSearching,
                                        dimmed: (!isMatch && isSearching) || isOffline,
                                    });
                            });

                            // Fly to first match when 1–5 results
                            if (isSearching && matched.length >= 1 && matched.length <= 5) {
                                xSROMap.FlyView(matched[0].pos_x, matched[0].pos_y, matched[0].pos_z, matched[0]
                                    .region);
                            }
                        },

                        _levelColor(lvl) {
                            if (lvl < 30) return '#60a5fa';
                            if (lvl < 60) return '#34d399';
                            if (lvl < 90) return '#fbbf24';
                            return '#f87171';
                        },

                        resetTimer() {
                            clearInterval(this._timer);
                            this._timer = null;
                            if (this.currentInterval > 0) {
                                this._timer = setInterval(() => this.loadCharacters(), this.currentInterval *
                                    1000);
                            }
                        },

                        _esc(str) {
                            const d = document.createElement('div');
                            d.textContent = String(str);
                            return d.innerHTML;
                        },
                    };
                });
            });
        </script>
    @endpush
</x-filament-panels::page>
