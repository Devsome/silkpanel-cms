<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@settings('site_title', 'SilkPanel CMS')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @templateStyles
    @livewireStyles
</head>

<body class="min-h-screen flex flex-col items-center justify-center antialiased bg-black text-zinc-100">
    {{-- Ambient glow background --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div
            class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-violet-600/10 rounded-full blur-[120px]">
        </div>
        <div class="absolute bottom-1/4 right-1/4 w-[400px] h-[400px] bg-fuchsia-600/8 rounded-full blur-[100px]"></div>
    </div>

    <main class="relative z-10 w-full">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    @livewireScripts
</body>

</html>
