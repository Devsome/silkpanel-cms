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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Figtree:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @templateStyles
    @livewireStyles

    <style>
        :root {
            --ag-primary: #22d3ee;
            --ag-primary-dim: #06b6d4;
            --ag-primary-glow: rgba(34, 211, 238, 0.15);
            --ag-primary-glow-strong: rgba(34, 211, 238, 0.35);
            --ag-primary-container: #0c4a6e;
            --ag-on-primary: #001c26;
            --ag-secondary: #fbbf24;
            --ag-secondary-dim: #f59e0b;
            --ag-accent: #a78bfa;
            --ag-background: #06080f;
            --ag-surface: #090c17;
            --ag-surface-container: #0d1224;
            --ag-surface-container-low: #0a0f1d;
            --ag-surface-container-high: #121929;
            --ag-surface-container-highest: #1a2336;
            --ag-surface-bright: #1f2b42;
            --ag-on-surface: #e2e8f0;
            --ag-on-surface-variant: #7f93b0;
            --ag-outline: #2a3a52;
            --ag-outline-variant: #162032;
            --ag-error: #f87171;
            --ag-success: #34d399;
            --ag-warning: #fbbf24;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--ag-background);
            color: var(--ag-on-surface);
            font-family: 'Figtree', sans-serif;
        }

        .ag-font-display { font-family: 'Chakra Petch', sans-serif; }
        .ag-font-mono { font-family: 'Space Mono', monospace; }

        /* Cards */
        .ag-card {
            background-color: var(--ag-surface-container);
            border: 1px solid var(--ag-outline);
        }
        .ag-card-low {
            background-color: var(--ag-surface-container-low);
            border: 1px solid var(--ag-outline-variant);
        }
        .ag-card-high {
            background-color: var(--ag-surface-container-high);
            border: 1px solid var(--ag-outline);
        }
        .ag-card-surface {
            background-color: var(--ag-surface);
            border: 1px solid var(--ag-outline-variant);
        }

        /* Glow border variant */
        .ag-card-glow {
            background-color: var(--ag-surface-container);
            border: 1px solid rgba(34, 211, 238, 0.2);
            box-shadow: 0 0 0 1px rgba(34, 211, 238, 0.05), inset 0 1px 0 rgba(34, 211, 238, 0.06);
        }

        /* Buttons */
        .ag-btn-primary {
            background: linear-gradient(135deg, #0891b2 0%, #22d3ee 100%);
            color: #001c26;
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            transition: all 0.2s ease;
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.2);
        }
        .ag-btn-primary:hover {
            filter: brightness(1.1);
            box-shadow: 0 0 30px rgba(34, 211, 238, 0.4);
            transform: translateY(-1px);
        }
        .ag-btn-primary:active { transform: translateY(0); filter: brightness(0.95); }

        .ag-btn-secondary {
            background: transparent;
            color: var(--ag-primary);
            border: 1px solid rgba(34, 211, 238, 0.3);
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            transition: all 0.2s ease;
        }
        .ag-btn-secondary:hover {
            border-color: var(--ag-primary);
            background: rgba(34, 211, 238, 0.07);
        }

        .ag-btn-amber {
            background: linear-gradient(135deg, #d97706 0%, #fbbf24 100%);
            color: #1c0a00;
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            transition: all 0.2s ease;
        }
        .ag-btn-amber:hover { filter: brightness(1.1); transform: translateY(-1px); }

        /* Inputs */
        .ag-input {
            background-color: var(--ag-surface-container-low);
            border: 1px solid var(--ag-outline);
            color: var(--ag-on-surface);
            font-family: 'Figtree', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .ag-input:focus {
            border-color: var(--ag-primary-dim);
            box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.1);
            outline: none;
        }
        .ag-input::placeholder { color: var(--ag-on-surface-variant); }

        /* Text colors */
        .ag-text-primary { color: var(--ag-primary); }
        .ag-text-secondary { color: var(--ag-secondary); }
        .ag-text-accent { color: var(--ag-accent); }
        .ag-text-surface { color: var(--ag-on-surface); }
        .ag-text-muted { color: var(--ag-on-surface-variant); }
        .ag-text-outline { color: var(--ag-outline); }
        .ag-text-error { color: var(--ag-error); }
        .ag-text-success { color: var(--ag-success); }

        /* Background utilities */
        .ag-bg { background-color: var(--ag-background); }
        .ag-bg-surface { background-color: var(--ag-surface); }
        .ag-bg-container { background-color: var(--ag-surface-container); }

        /* Section divider */
        .ag-divider { border-color: var(--ag-outline-variant); }

        /* Corner bracket decorations */
        .ag-bracket {
            position: relative;
        }
        .ag-bracket::before,
        .ag-bracket::after {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            border-color: rgba(34, 211, 238, 0.4);
            border-style: solid;
        }
        .ag-bracket::before {
            top: -1px; left: -1px;
            border-width: 2px 0 0 2px;
        }
        .ag-bracket::after {
            bottom: -1px; right: -1px;
            border-width: 0 2px 2px 0;
        }

        /* Cyan accent line on top of sections */
        .ag-accent-line {
            position: relative;
        }
        .ag-accent-line::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--ag-primary), transparent);
        }

        /* Hero gradient overlay */
        .ag-hero-overlay {
            background: linear-gradient(
                to bottom,
                rgba(6, 8, 15, 0.2) 0%,
                rgba(6, 8, 15, 0.5) 60%,
                rgba(6, 8, 15, 1) 100%
            );
        }

        /* Noise texture */
        .ag-noise {
            position: relative;
        }
        .ag-noise::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            opacity: 0.03;
        }

        /* Table styles */
        .ag-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ag-table th {
            background-color: var(--ag-surface-container-low);
            color: var(--ag-on-surface-variant);
            font-family: 'Chakra Petch', sans-serif;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 0.75rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid var(--ag-outline-variant);
        }
        .ag-table td {
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid var(--ag-outline-variant);
            color: var(--ag-on-surface);
            font-size: 0.875rem;
        }
        .ag-table tr:last-child td { border-bottom: none; }
        .ag-table tr:hover td { background-color: rgba(34, 211, 238, 0.03); }

        /* Badge */
        .ag-badge {
            font-family: 'Chakra Petch', sans-serif;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.15rem 0.5rem;
        }

        /* Rank medal colors */
        .ag-rank-1 { color: #fbbf24; text-shadow: 0 0 10px rgba(251,191,36,0.5); }
        .ag-rank-2 { color: #94a3b8; }
        .ag-rank-3 { color: #c87941; }

        /* Navigation active indicator */
        .ag-nav-active {
            color: var(--ag-primary);
            position: relative;
        }
        .ag-nav-active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--ag-primary), transparent);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--ag-surface); }
        ::-webkit-scrollbar-thumb { background: var(--ag-outline); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--ag-primary-dim); }

        /* Selection */
        ::selection { background: rgba(34, 211, 238, 0.25); color: var(--ag-on-surface); }

        /* Subtle dot-grid background pattern */
        .ag-dot-bg {
            background-image: radial-gradient(circle, rgba(34, 211, 238, 0.06) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* Animated glow pulse */
        @keyframes ag-pulse-glow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        .ag-pulse { animation: ag-pulse-glow 2s ease-in-out infinite; }

        /* Stat number styling */
        .ag-stat-number {
            font-family: 'Space Mono', monospace;
            color: var(--ag-primary);
            font-weight: 700;
        }
        .ag-stat-amber {
            font-family: 'Space Mono', monospace;
            color: var(--ag-secondary);
            font-weight: 700;
        }

        /* Server online dot */
        .ag-online-dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--ag-success);
            box-shadow: 0 0 6px var(--ag-success);
            animation: ag-pulse-glow 2s ease-in-out infinite;
        }
        .ag-offline-dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--ag-error);
        }

        /* Page section headers */
        .ag-section-title {
            font-family: 'Chakra Petch', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ag-on-surface);
            letter-spacing: 0.04em;
        }
        .ag-section-eyebrow {
            font-family: 'Chakra Petch', sans-serif;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--ag-primary);
        }

        /* News card thumbnail */
        .ag-news-thumb {
            position: relative;
            overflow: hidden;
        }
        .ag-news-thumb img {
            transition: transform 0.4s ease;
        }
        .ag-news-thumb:hover img { transform: scale(1.05); }

        /* Event timer countdown */
        .ag-countdown-block {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            min-width: 2.5rem;
        }

        /* Webmall item card hover */
        .ag-item-card {
            background-color: var(--ag-surface-container);
            border: 1px solid var(--ag-outline);
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
        }
        .ag-item-card:hover {
            border-color: rgba(34, 211, 238, 0.3);
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.08);
            transform: translateY(-2px);
        }

        /* Profile section */
        .ag-profile-header {
            background: linear-gradient(135deg, var(--ag-surface-container) 0%, var(--ag-surface-container-high) 100%);
            border: 1px solid var(--ag-outline);
            position: relative;
            overflow: hidden;
        }
        .ag-profile-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--ag-primary-dim), var(--ag-primary), var(--ag-primary-dim));
        }

        /* Tabs */
        .ag-tab {
            font-family: 'Chakra Petch', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.5rem 1.25rem;
            color: var(--ag-on-surface-variant);
            border-bottom: 2px solid transparent;
            transition: color 0.2s, border-color 0.2s;
            cursor: pointer;
        }
        .ag-tab:hover { color: var(--ag-on-surface); }
        .ag-tab.active {
            color: var(--ag-primary);
            border-bottom-color: var(--ag-primary);
        }

        /* Category chip */
        .ag-chip {
            font-family: 'Chakra Petch', sans-serif;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.35rem 0.9rem;
            background: var(--ag-surface-container-high);
            border: 1px solid var(--ag-outline);
            color: var(--ag-on-surface-variant);
            transition: all 0.2s;
            cursor: pointer;
        }
        .ag-chip:hover, .ag-chip.active {
            background: rgba(34, 211, 238, 0.1);
            border-color: rgba(34, 211, 238, 0.3);
            color: var(--ag-primary);
        }

        /* Alert / flash message */
        .ag-alert-success {
            background: rgba(52, 211, 153, 0.08);
            border: 1px solid rgba(52, 211, 153, 0.3);
            color: #34d399;
        }
        .ag-alert-error {
            background: rgba(248, 113, 113, 0.08);
            border: 1px solid rgba(248, 113, 113, 0.3);
            color: #f87171;
        }
        .ag-alert-info {
            background: rgba(34, 211, 238, 0.06);
            border: 1px solid rgba(34, 211, 238, 0.2);
            color: var(--ag-primary);
        }
        .ag-alert-warning {
            background: rgba(251, 191, 36, 0.08);
            border: 1px solid rgba(251, 191, 36, 0.3);
            color: #fbbf24;
        }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen flex flex-col antialiased ag-noise">
    {{-- Navigation --}}
    @include('template::partials.navigation')

    {{-- Page Content --}}
    <main class="flex-1 pt-20">
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
    <x-session-modals />
</body>

</html>
