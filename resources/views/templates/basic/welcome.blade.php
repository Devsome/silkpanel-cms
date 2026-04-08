@extends('template::layouts.app', ['title' => \App\Helpers\SettingHelper::get('site_title', 'SilkPanel CMS')])

@section('content')
    {{-- Hero Section - Centered --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-indigo-900 via-purple-900 to-gray-900">
        @if (\App\Helpers\SettingHelper::get('background_image'))
            <div class="absolute inset-0">
                <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}" alt=""
                    class="w-full h-full object-cover opacity-20" />
            </div>
        @endif
        <div
            class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-indigo-500/10 via-transparent to-transparent">
        </div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-28 sm:py-36 lg:py-44 text-center">
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-6">
                🎮 {{ __('index.server_info') }}
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white tracking-tight">
                @settings('site_title', 'SilkPanel CMS')
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-gray-300 leading-relaxed max-w-2xl mx-auto">
                @settings('site_description', 'A powerful and user-friendly content management system.')
            </p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-flex items-center px-8 py-3.5 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/25">
                        {{ __('navigation.dashboard') }}
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                @else
                    @settingsRegistrationOpen
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center px-8 py-3.5 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/25">
                            {{ __('index.register_now') }}
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @endsettingsRegistrationOpen
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center px-8 py-3.5 border border-white/20 text-white font-semibold rounded-full hover:bg-white/10 backdrop-blur-sm transition">
                        {{ __('navigation.login') }}
                    </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- Server Info Section - Card Grid with Icons --}}
    <section class="py-16 sm:py-24 -mt-12 relative z-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none text-center group hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        @settings('sro_cap', '110')
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('index.cap') }}</div>
                </div>
                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none text-center group hover:border-purple-300 dark:hover:border-purple-700 transition">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ \App\Helpers\SettingHelper::get('sro_exp_sp', '1') }}x
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('index.exp_sp') }}</div>
                </div>
                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none text-center group hover:border-amber-300 dark:hover:border-amber-700 transition">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ \App\Helpers\SettingHelper::get('sro_drop_rate', '1') }}x
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('index.drop_rate') }}</div>
                </div>
                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none text-center group hover:border-emerald-300 dark:hover:border-emerald-700 transition">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        @settings('sro_max_player', '500')
                    </div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('index.max_player') }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('index.about_title') }}</h2>
            <p class="mt-6 text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                {{ __('index.about_text') }}
            </p>
        </div>
    </section>
@endsection
