@extends('template::layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        .ns-map-wrapper {
            position: relative;
            width: 100%;
            height: calc(100vh - 300px);
            min-height: 450px;
            overflow: hidden;
        }

        #map {
            display: block;
            position: absolute;
            inset: 0;
            background: #09090b;
        }

        .leaflet-container {
            background: #09090b;
        }
    </style>
@endpush

@php
    $refreshInterval = max(10, (int) \App\Models\Setting::get('map_refresh_interval', 30));
    $maxChars = (int) \App\Models\Setting::get('map_max_characters', \App\Services\SilkroadMapService::MAX_CHARACTERS);
@endphp

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-5" x-data="silkroadMap">

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                ← {{ __('dashboard.back_to_dashboard') }}
            </a>

            {{-- Header --}}
            <div class="bg-zinc-900 border border-violet-500/20 p-6">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70">{{ __('dashboard.map') }}</p>
                <h1
                    class="mt-1 text-2xl font-black uppercase tracking-widest bg-linear-to-r from-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
                    {{ __('dashboard.world_map') }}
                </h1>
                <p class="mt-1 text-xs font-mono text-zinc-600">{{ __('dashboard.map_subtitle') }}</p>
            </div>

            {{-- Toolbar --}}
            <div class="bg-zinc-900 border border-violet-500/20 px-5 py-3 flex flex-wrap items-center gap-3">
                <input type="search" x-model="search" placeholder="{{ __('dashboard.map_search') }}"
                    class="bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-1.5 text-xs font-mono focus:outline-none focus:border-violet-500 transition flex-1 min-w-40 max-w-xs" />

                <div class="flex items-center gap-2 text-xs font-mono">
                    <span class="text-zinc-600 uppercase tracking-wider">{{ __('dashboard.map_online') }}</span>
                    <span class="text-violet-400 font-bold" x-text="visibleCount"></span>
                    <template x-if="totalCount >= {{ $maxChars }}">
                        <span class="text-zinc-700">({{ __('dashboard.map_max_shown', ['n' => $maxChars]) }})</span>
                    </template>
                </div>

                <div class="flex items-center gap-2 ml-auto">
                    <span class="hidden sm:inline text-xs font-mono text-zinc-700"
                        x-text="lastRefreshed ? '{{ __('dashboard.map_updated') }}: ' + lastRefreshed : ''"></span>

                    <button @click="loadCharacters()" :disabled="loading"
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition disabled:opacity-40">
                        <svg x-show="!loading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span x-show="loading"
                            class="w-3.5 h-3.5 border-2 border-current/30 border-t-current rounded-full animate-spin"></span>
                        <span
                            x-text="loading ? '{{ __('dashboard.map_loading') }}' : '{{ __('dashboard.map_refresh') }}'"></span>
                    </button>

                    <select x-model="currentInterval" @change="resetTimer()"
                        class="bg-zinc-950 border border-zinc-700 text-zinc-300 px-3 py-1.5 text-xs font-mono focus:outline-none focus:border-violet-500 transition">
                        <option value="0">{{ __('dashboard.map_manual') }}</option>
                        <option value="30">30s</option>
                        <option value="60">60s</option>
                    </select>
                </div>
            </div>

            {{-- Level legend --}}
            <div class="flex flex-wrap items-center gap-4 text-xs font-mono text-zinc-600 px-1">
                <span class="uppercase tracking-[0.2em]">{{ __('dashboard.map_level') }}</span>
                <span class="flex items-center gap-1.5"><span
                        class="w-2.5 h-2.5 rounded-full bg-blue-400 inline-block"></span> 1–29</span>
                <span class="flex items-center gap-1.5"><span
                        class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span> 30–59</span>
                <span class="flex items-center gap-1.5"><span
                        class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span> 60–89</span>
                <span class="flex items-center gap-1.5"><span
                        class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span> 90+</span>
            </div>

            {{-- Map --}}
            <div class="border border-violet-500/20">
                <div class="ns-map-wrapper">
                    <div id="map"></div>
                </div>
            </div>

            {{-- Error --}}
            <div x-show="errorMsg" x-transition
                class="p-3 border border-red-500/30 bg-red-500/10 text-red-400 text-xs font-mono" x-text="errorMsg"></div>

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
