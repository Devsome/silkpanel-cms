<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'SilkPanel CMS') }}</title>
    <meta name="description" content="@settings('site_description', 'Made by devsome')">
    <meta name="keywords" content="@settings('site_keywords', 'silkpanel, cms')">

    @if (\App\Helpers\SettingHelper::get('favicon'))
        <link rel="icon" href="{{ asset('storage/' . \App\Helpers\SettingHelper::get('favicon')) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 antialiased">
    {{-- Navigation --}}
    @include('template::partials.navigation')

    {{-- Page Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('template::partials.footer')

    @stack('scripts')
</body>

</html>
