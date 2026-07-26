@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8 space-y-6">

            {{-- Header --}}
            <div class="bg-zinc-900 border border-violet-500/20 p-6 relative ns-corner">
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70">{{ __('dashboard.title') }}</p>
                <h1
                    class="mt-2 text-3xl font-black uppercase tracking-widest bg-linear-to-r from-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
                    {{ __('dashboard.welcome', ['name' => Auth::user()->name]) }}
                </h1>
                <p class="mt-1 text-sm font-mono text-zinc-500">{{ Auth::user()->email }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Silk Balance --}}
                <div class="lg:col-span-2 bg-zinc-900 border border-violet-500/20 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70">
                            {{ __('dashboard.silk_balance') }}</p>
                        <a href="{{ route('donate.index') }}"
                            class="text-xs font-mono uppercase tracking-wider text-violet-500 hover:text-violet-300 border border-violet-500/30 px-3 py-1 hover:bg-violet-500/10 transition">
                            {{ __('dashboard.refill_silk') }} +
                        </a>
                    </div>

                    @if ($silkData['type'] === 'vsro')
                        <div class="grid grid-cols-3 gap-3">
                            @foreach ([['label' => __('dashboard.silk_own'), 'value' => $silkData['silk_own'], 'color' => 'from-violet-400 to-fuchsia-400'], ['label' => __('dashboard.silk_gift'), 'value' => $silkData['silk_gift'], 'color' => 'from-cyan-400 to-violet-400'], ['label' => __('dashboard.silk_point'), 'value' => $silkData['silk_point'], 'color' => 'from-fuchsia-400 to-pink-400']] as $item)
                                <div class="text-center p-4 border border-zinc-800 bg-zinc-950/50">
                                    <p
                                        class="text-2xl font-black font-mono bg-linear-to-r {{ $item['color'] }} bg-clip-text text-transparent">
                                        {{ number_format($item['value']) }}
                                    </p>
                                    <p class="text-xs font-mono uppercase tracking-wider text-zinc-600 mt-1">
                                        {{ $item['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs font-mono text-zinc-700 text-right">
                            {{ __('dashboard.silk_total', ['total' => number_format($silkData['total'])]) }}
                        </p>
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ([['label' => __('dashboard.silk_own'), 'value' => $silkData['silk'], 'color' => 'from-violet-400 to-fuchsia-400'], ['label' => __('dashboard.silk_premium'), 'value' => $silkData['premium_silk'], 'color' => 'from-cyan-400 to-violet-400']] as $item)
                                <div class="text-center p-4 border border-zinc-800 bg-zinc-950/50">
                                    <p
                                        class="text-2xl font-black font-mono bg-linear-to-r {{ $item['color'] }} bg-clip-text text-transparent">
                                        {{ number_format($item['value']) }}
                                    </p>
                                    <p class="text-xs font-mono uppercase tracking-wider text-zinc-600 mt-1">
                                        {{ $item['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs font-mono text-zinc-700 text-right">
                            {{ __('dashboard.silk_total', ['total' => number_format($silkData['total'])]) }}
                        </p>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div class="bg-zinc-900 border border-violet-500/20 p-6">
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-4">
                        {{ __('dashboard.quick_actions') }}</p>
                    <div class="space-y-2">
                        @php
                            $actions = [
                                [
                                    'route' => 'profile.edit',
                                    'label' => __('dashboard.profile'),
                                    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                ],
                                [
                                    'route' => 'donate.index',
                                    'label' => __('dashboard.refill_silk'),
                                    'icon' =>
                                        'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                ],
                                [
                                    'route' => 'webmall.index',
                                    'label' => __('dashboard.webmall'),
                                    'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                                ],
                                [
                                    'route' => 'voting.index',
                                    'label' => __('dashboard.voting'),
                                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                ],
                                [
                                    'route' => 'dashboard.map',
                                    'label' => __('dashboard.world_map'),
                                    'icon' =>
                                        'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
                                ],
                                [
                                    'route' => 'tickets.index',
                                    'label' => __('dashboard.support'),
                                    'icon' =>
                                        'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z',
                                ],
                                [
                                    'route' => 'dashboard.silk-history',
                                    'label' => __('dashboard.silk_history'),
                                    'icon' =>
                                        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                                ],
                            ];
                        @endphp
                        @foreach ($actions as $action)
                            <a href="{{ route($action['route']) }}"
                                class="flex items-center gap-3 p-3 border border-zinc-800 hover:border-violet-500/40 hover:bg-violet-500/5 group transition">
                                <span
                                    class="flex h-7 w-7 items-center justify-center border border-violet-500/30 text-violet-500 group-hover:bg-violet-500/15 transition flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $action['icon'] }}" />
                                    </svg>
                                </span>
                                <span
                                    class="text-xs font-mono uppercase tracking-wider text-zinc-400 group-hover:text-violet-300 transition">{{ $action['label'] }}</span>
                            </a>
                        @endforeach
                        @if ($webMarketEnabled)
                            <a href="{{ route('web-market.index') }}"
                                class="flex items-center gap-3 p-3 border border-zinc-800 hover:border-violet-500/40 hover:bg-violet-500/5 group transition">
                                <span
                                    class="flex h-7 w-7 items-center justify-center border border-violet-500/30 text-violet-500 group-hover:bg-violet-500/15 transition flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                    </svg>
                                </span>
                                <span
                                    class="text-xs font-mono uppercase tracking-wider text-zinc-400 group-hover:text-violet-300 transition">{{ __('dashboard.web_market') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Referral --}}
            @if ($referralEnabled && $referralData)
                <div class="bg-zinc-900 border border-violet-500/20 p-6">
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-5">
                        {{ __('dashboard.referral_title') }}
                    </p>

                    <div class="grid grid-cols-3 gap-3 mb-6">
                        @foreach ([['label' => __('dashboard.referral_valid'), 'value' => $referralData['valid_count'], 'color' => 'from-violet-400 to-fuchsia-400'], ['label' => __('dashboard.referral_pending'), 'value' => $referralData['pending_count'], 'color' => 'from-cyan-400 to-violet-400'], ['label' => __('dashboard.referral_silk_earned'), 'value' => number_format($referralData['total_silk_earned']), 'color' => 'from-fuchsia-400 to-pink-400']] as $item)
                            <div class="text-center p-4 border border-zinc-800 bg-zinc-950/50">
                                <p class="text-2xl font-black font-mono bg-linear-to-r {{ $item['color'] }} bg-clip-text text-transparent">
                                    {{ $item['value'] }}
                                </p>
                                <p class="text-xs font-mono uppercase tracking-wider text-zinc-600 mt-1">
                                    {{ $item['label'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <p class="text-xs font-mono uppercase tracking-wider text-zinc-600 mb-2">
                            {{ __('dashboard.referral_your_link') }}
                        </p>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly
                                value="{{ route('register') . '?ref=' . $referralData['reflink'] }}"
                                class="flex-1 text-sm font-mono bg-zinc-950 border border-zinc-800 px-3 py-2 text-zinc-300 truncate focus:outline-none focus:border-violet-500/40" />
                            <button type="button"
                                onclick="navigator.clipboard.writeText('{{ route('register') . '?ref=' . $referralData['reflink'] }}').then(() => this.textContent = '{{ __('dashboard.referral_copied') }}')"
                                class="shrink-0 text-xs font-mono font-bold uppercase tracking-wider px-4 py-2 border border-violet-500/40 text-violet-400 hover:bg-violet-500/10 transition">
                                {{ __('dashboard.referral_copy') }}
                            </button>
                        </div>
                    </div>

                    @if ($referralData['referrals']->isNotEmpty())
                        <div class="mt-4 divide-y divide-zinc-800/50">
                            @foreach ($referralData['referrals'] as $referral)
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm font-mono text-zinc-300">
                                        {{ $referral->character_name ?? __('dashboard.referral_no_character') }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-mono font-medium px-2 py-0.5 rounded-full
                                        {{ $referral->status === 'valid' ? 'bg-emerald-900/30 text-emerald-400' : 'bg-yellow-900/30 text-yellow-400' }}">
                                        {{ $referral->status === 'valid' ? __('dashboard.referral_status_valid') : __('dashboard.referral_status_pending') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Characters --}}
            @if (!empty($characters) && count($characters) > 0)
                <div class="bg-zinc-900 border border-violet-500/20 p-6">
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-5">
                        {{ __('dashboard.characters') }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                        @foreach ($characters as $char)
                            <div
                                class="border border-zinc-800 bg-zinc-950/50 p-4 hover:border-violet-500/30 hover:bg-violet-500/5 transition">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-bold text-zinc-200 font-mono truncate">
                                        {{ e($char->CharName16 ?? ($char->name ?? 'Character')) }}</p>
                                </div>
                                <p class="text-xs font-mono text-zinc-600 uppercase tracking-wider">
                                    {{ __('dashboard.char_level') }} <span
                                        class="text-cyan-400">{{ $char->CurLevel ?? ($char->level ?? '-') }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>
@endsection
