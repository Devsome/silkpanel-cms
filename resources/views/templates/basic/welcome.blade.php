@extends('template::layouts.app', ['title' => \App\Helpers\SettingHelper::get('site_title', 'SilkPanel CMS')])

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-gray-900 to-gray-800">
        @if (\App\Helpers\SettingHelper::get('background_image'))
            <div class="absolute inset-0">
                <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}" alt=""
                    class="w-full h-full object-cover opacity-30" />
            </div>
        @endif
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32 lg:py-40">
            <div class="max-w-2xl">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white tracking-tight">
                    @settings('site_title', 'SilkPanel CMS')
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-gray-300 leading-relaxed">
                    @settings('site_description', 'A powerful and user-friendly content management system.')
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="inline-flex items-center px-6 py-3 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition shadow-lg">
                            {{ __('Dashboard') }}
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @else
                        @settingsRegistrationOpen
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center px-6 py-3 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition shadow-lg">
                                {{ __('Get Started') }}
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        @endsettingsRegistrationOpen
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-6 py-3 border border-gray-400 text-white font-semibold rounded-lg hover:bg-white/10 transition">
                            {{ __('Log in') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Server Info Section --}}
    <section class="py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Server Information</h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Join our growing community</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800 text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        @settings('sro_cap', '110')
                    </div>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Level Cap</div>
                </div>
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800 text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ \App\Helpers\SettingHelper::get('sro_exp_sp', '1') }}x
                    </div>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">EXP / SP Rate</div>
                </div>
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800 text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ \App\Helpers\SettingHelper::get('sro_drop_rate', '1') }}x
                    </div>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Drop Rate</div>
                </div>
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-800 text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        @settings('sro_max_player', '500')
                    </div>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Max Players</div>
                </div>
            </div>
        </div>
    </section>
@endsection
