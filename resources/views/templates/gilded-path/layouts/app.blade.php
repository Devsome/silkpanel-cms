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
    @livewireStyles

    <style>
        :root {
            --gp-primary: #f2ca50;
            --gp-primary-container: #d4af37;
            --gp-on-primary: #3c2f00;
            --gp-background: #131313;
            --gp-surface: #131313;
            --gp-surface-container: #20201f;
            --gp-surface-container-low: #1c1b1b;
            --gp-surface-container-high: #2a2a2a;
            --gp-surface-container-highest: #353535;
            --gp-surface-container-lowest: #0e0e0e;
            --gp-surface-bright: #393939;
            --gp-on-surface: #e5e2e1;
            --gp-on-surface-variant: #d0c5af;
            --gp-outline: #99907c;
            --gp-outline-variant: #4d4635;
            --gp-secondary: #ffb4a8;
            --gp-tertiary: #b8cfff;
            --gp-error: #ffb4ab;
        }

        body {
            background-color: var(--gp-background);
            color: var(--gp-on-surface);
        }

        .gp-ornate-border {
            border: 2px solid transparent;
            border-image: linear-gradient(to bottom, #f2ca50, #554300) 1;
            position: relative;
        }

        .gp-ornate-border::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            width: 10px;
            height: 10px;
            background: #f2ca50;
            box-shadow: 0 0 10px #f2ca50;
        }

        .gp-hero-gradient {
            background: linear-gradient(to bottom, rgba(19, 19, 19, 0) 0%, rgba(19, 19, 19, 1) 100%);
        }

        .gp-gold-shimmer {
            background: linear-gradient(45deg, #d4af37 25%, #f2ca50 50%, #d4af37 75%);
            background-size: 200% auto;
        }

        .gp-gold-btn {
            background: linear-gradient(135deg, #d4af37, #f2ca50);
            color: var(--gp-on-primary);
            box-shadow: inset 1px 1px 0 rgba(255, 255, 255, 0.3), inset -1px -1px 0 rgba(0, 0, 0, 0.3);
        }

        .gp-gold-btn:hover {
            filter: brightness(1.1);
        }

        .gp-gold-btn:active {
            transform: scale(0.95);
        }

        .gp-card {
            background-color: var(--gp-surface-container);
            border: 1px solid rgba(77, 70, 53, 0.2);
        }

        .gp-card-low {
            background-color: var(--gp-surface-container-low);
        }

        .gp-card-high {
            background-color: var(--gp-surface-container-high);
        }

        .gp-card-lowest {
            background-color: var(--gp-surface-container-lowest);
            border: 1px solid rgba(77, 70, 53, 0.2);
        }

        .gp-text-primary {
            color: var(--gp-primary);
        }

        .gp-text-on-surface {
            color: var(--gp-on-surface);
        }

        .gp-text-on-surface-variant {
            color: var(--gp-on-surface-variant);
        }

        .gp-text-outline {
            color: var(--gp-outline);
        }

        .gp-text-secondary {
            color: var(--gp-secondary);
        }

        .gp-text-tertiary {
            color: var(--gp-tertiary);
        }

        .gp-bg-surface {
            background-color: var(--gp-surface);
        }

        .gp-bg-surface-container {
            background-color: var(--gp-surface-container);
        }

        .gp-input {
            background-color: var(--gp-surface-container-lowest);
            border: 0.5px solid rgba(77, 70, 53, 0.2);
            color: var(--gp-on-surface);
        }

        .gp-input:focus {
            border-color: var(--gp-primary);
            box-shadow: inset 0 0 8px rgba(242, 202, 80, 0.1);
            outline: none;
        }

        .gp-ghost-border {
            border: 1px solid rgba(77, 70, 53, 0.2);
        }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen flex flex-col antialiased selection:bg-yellow-500 selection:text-yellow-950">
    {{-- Navigation --}}
    @include('template::partials.navigation')

    {{-- Page Content --}}
    <main class="flex-1 pt-20">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('template::partials.footer')

    @livewireScripts
    @stack('scripts')
</body>

</html>
