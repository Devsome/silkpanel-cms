<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth dark">

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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @templateStyles
    @livewireStyles

    @stack('styles')
</head>

<body class="min-h-screen flex flex-col antialiased bg-black text-zinc-100">

    {{-- Navigation --}}
    @include('template::partials.navigation')

    {{-- Page Content --}}
    <main class="flex-1 pt-16">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    {{-- Footer --}}
    @include('template::partials.footer')

    @livewireScripts
    @stack('scripts')
</body>

</html>
