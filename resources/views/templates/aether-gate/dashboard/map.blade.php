@extends('template::layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        .silkpanel-map-wrapper {
            position: relative;
            width: 100%;
            height: calc(100vh - 280px);
            min-height: 450px;
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
    $maxChars = (int) \App\Models\Setting::get('map_max_characters', \App\Services\SilkroadMapService::MAX_CHARACTERS);
@endphp

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8 space-y-6" x-data="silkroadMap">

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-muted hover:ag-text-primary transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            <div class="ag-card-glow p-6 md:p-8">
                <p class="ag-section-eyebrow">{{ __('dashboard.map_subtitle') }}</p>
                <h1 class="ag-section-title mt-2">{{ __('dashboard.world_map') }}</h1>
            </div>

            <div class="ag-card px-5 py-4 flex flex-wrap items-center gap-3">
                <input type="search" x-model="search" placeholder="{{ __('dashboard.map_search') }}"
                    class="ag-input flex-1 min-w-48 max-w-xs px-3 py-1.5 text-sm rounded" />

                <div class="flex items-center gap-1.5">
                    <span class="text-xs ag-font-display font-bold uppercase tracking-widest ag-text-muted">
                        {{ __('dashboard.map_online') }}
                    </span>
                    <span class="ag-font-display font-bold ag-text-primary" x-text="visibleCount"></span>
                    <template x-if="totalCount >= {{ $maxChars }}">
                        <span class="text-xs ag-stat-amber">
                            ({{ __('dashboard.map_max_shown', ['n' => $maxChars]) }})
                        </span>
                    </template>
                </div>

                <div class="flex items-center gap-2 ml-auto">
                    <span class="hidden sm:inline text-xs ag-text-muted"
                        x-text="lastRefreshed ? '{{ __('dashboard.map_updated') }}: ' + lastRefreshed : ''"></span>

                    <button @click="loadCharacters()" :disabled="loading"
                        class="ag-btn-primary inline-flex items-center gap-1.5 px-4 py-1.5 text-sm ag-font-display uppercase tracking-wide rounded disabled:opacity-50">
                        <svg x-show="!loading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span x-show="loading"
                            class="w-3.5 h-3.5 border-2 border-current/30 border-t-current rounded-full animate-spin"></span>
                        <span x-text="loading ? '{{ __('dashboard.map_loading') }}' : '{{ __('dashboard.map_refresh') }}'"></span>
                    </button>

                    <select x-model="currentInterval" @change="resetTimer()" class="ag-input text-sm rounded px-3 py-1.5">
                        <option value="0">{{ __('dashboard.map_manual') }}</option>
                        <option value="30">30s</option>
                        <option value="60">60s</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-4 text-xs ag-text-muted px-1">
                <span class="ag-font-display font-bold uppercase tracking-widest text-xs">{{ __('dashboard.map_level') }}</span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-blue-400 inline-block"></span> 1–29
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span> 30–59
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> 60–89
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span> 90+
                </span>
            </div>

            <div class="ag-card p-0.5">
                <div class="silkpanel-map-wrapper">
                    <div id="map"></div>
                </div>
            </div>

            <div x-show="errorMsg" x-transition class="text-sm rounded px-4 py-3 ag-alert-error"
                x-text="errorMsg"></div>

        </div>
    </section>
@endsection

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
