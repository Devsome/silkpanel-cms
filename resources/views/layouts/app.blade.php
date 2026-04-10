<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@settings('site_title', 'SilkPanel CMS')</title>
    <meta name="description" content="@settings('site_description', 'Made by devsome')">
    <meta name="keywords" content="@settings('site_keywords', 'silkpanel, cms, laravel, filament')">

    @if (\App\Helpers\SettingHelper::get('logo'))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}">
        <meta property="og:image" content="{{ asset('storage/' . \App\Helpers\SettingHelper::get('logo')) }}">
    @endif

    @if (\App\Helpers\SettingHelper::get('favicon'))
        <link rel="icon" href="{{ asset('storage/' . \App\Helpers\SettingHelper::get('favicon')) }}">
    @endif

    <script>
        (() => {
            const storedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = storedTheme === 'dark' || storedTheme === 'light' ? storedTheme : (systemPrefersDark ?
                'dark' : 'light');

            document.documentElement.classList.toggle('dark', theme === 'dark');
            document.documentElement.style.colorScheme = theme;
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased min-h-screen flex flex-col bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    @include('layouts.navigation')

    @isset($header)
        <header class="bg-white shadow dark:bg-gray-800 mt-16">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main class="flex-1 pt-16 @isset($header) pt-0 @endisset">
        {{ $slot }}
    </main>

    @include('layouts.partials.footer')

    @livewireScripts
</body>

</html>
