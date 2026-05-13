@extends('template::layouts.app')

@section('content')
    <section class="py-8">
        <div class="mx-auto max-w-7xl px-4 md:px-8 space-y-6">

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                    {{ __('dashboard.title') }}
                </p>
                <h1 class="mt-2 text-3xl font-bold text-white">
                    {{ __('dashboard.welcome', ['name' => Auth::user()->name]) }}
                </h1>
                <p class="mt-1 text-sm text-gray-400">{{ Auth::user()->email }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Silk Balance --}}
                <div class="lg:col-span-2 rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6">
                    <div class="flex items-center justify-between mb-5">
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                            {{ __('dashboard.silk_balance') }}
                        </p>
                        <a href="{{ route('donate.index') }}"
                            class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition">
                            {{ __('dashboard.refill_silk') }} &rarr;
                        </a>
                    </div>

                    @if ($silkData['type'] === 'vsro')
                        <div class="grid grid-cols-3 gap-4">
                            @foreach ([['label' => __('dashboard.silk_own'), 'value' => $silkData['silk_own']], ['label' => __('dashboard.silk_gift'), 'value' => $silkData['silk_gift']], ['label' => __('dashboard.silk_point'), 'value' => $silkData['silk_point']]] as $item)
                                <div class="text-center p-4 rounded-xl border border-gray-800 bg-gray-900/80">
                                    <p
                                        class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">
                                        {{ number_format($item['value']) }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $item['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs text-gray-500 text-right">
                            {{ __('dashboard.silk_total', ['total' => number_format($silkData['total'])]) }}
                        </p>
                    @else
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ([['label' => __('dashboard.silk_own'), 'value' => $silkData['silk']], ['label' => __('dashboard.silk_premium'), 'value' => $silkData['premium_silk']]] as $item)
                                <div class="text-center p-4 rounded-xl border border-gray-800 bg-gray-900/80">
                                    <p
                                        class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">
                                        {{ number_format($item['value']) }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $item['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs text-gray-500 text-right">
                            {{ __('dashboard.silk_total', ['total' => number_format($silkData['total'])]) }}
                        </p>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70 mb-4">
                        {{ __('dashboard.quick_actions') }}
                    </p>
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
                                    'route' => 'dashboard.silk-history',
                                    'label' => __('dashboard.silk_history'),
                                    'icon' =>
                                        'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                                ],
                            ];
                        @endphp
                        @foreach ($actions as $action)
                            <a href="{{ route($action['route']) }}"
                                class="group flex items-center gap-3 p-3 rounded-xl border border-gray-800 bg-gray-900/60 hover:border-emerald-500/40 hover:bg-emerald-500/5 transition">
                                <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $action['icon'] }}" />
                                </svg>
                                <span class="text-sm font-medium text-gray-300 group-hover:text-white transition">
                                    {{ $action['label'] }}
                                </span>
                            </a>
                        @endforeach
                        @if ($votingEnabled)
                            <a href="{{ route('voting.index') }}"
                                class="group flex items-center gap-3 p-3 rounded-xl border border-gray-800 bg-gray-900/60 hover:border-emerald-500/40 hover:bg-emerald-500/5 transition">
                                <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                <span
                                    class="text-sm font-medium text-gray-300 group-hover:text-white transition">{{ __('dashboard.vote_now') }}</span>
                            </a>
                        @endif
                        @if ($worldMapEnabled)
                            <a href="{{ route('dashboard.map') }}"
                                class="group flex items-center gap-3 p-3 rounded-xl border border-gray-800 bg-gray-900/60 hover:border-emerald-500/40 hover:bg-emerald-500/5 transition">
                                <svg class="h-4 w-4 text-emerald-400 shrink-0" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                </svg>
                                <span
                                    class="text-sm font-medium text-gray-300 group-hover:text-white transition">{{ __('dashboard.world_map') }}</span>
                            </a>
                        @endif
                        @if ($webmallEnabled)
                            <a href="{{ route('webmall.index') }}"
                                class="group flex items-center gap-3 p-3 rounded-xl border border-gray-800 bg-gray-900/60 hover:border-emerald-500/40 hover:bg-emerald-500/5 transition">
                                <svg class="h-4 w-4 text-emerald-400 shrink-0" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                </svg>
                                <span
                                    class="text-sm font-medium text-gray-300 group-hover:text-white transition">{{ __('dashboard.webmall') }}</span>
                            </a>
                        @endif
                        @if (Route::has('tickets.index') && $ticketSystemEnabled)
                            <a href="{{ route('tickets.index') }}"
                                class="group flex items-center gap-3 p-3 rounded-xl border border-gray-800 bg-gray-900/60 hover:border-emerald-500/40 hover:bg-emerald-500/5 transition">
                                <svg class="h-4 w-4 text-emerald-400 shrink-0" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                                </svg>
                                <span
                                    class="text-sm font-medium text-gray-300 group-hover:text-white transition">{{ __('dashboard.support_tickets') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Characters --}}
            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70 mb-5">
                    {{ __('dashboard.your_characters') }}
                </p>
                @if ($characters->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('dashboard.no_characters') }}</p>
                @else
                    <div class="divide-y divide-gray-800/50">
                        @foreach ($characters as $char)
                            <a href="{{ route('ranking.characters.show', $char->CharID) }}"
                                class="flex items-center gap-4 py-3 -mx-6 px-6 hover:bg-emerald-500/5 transition">
                                <img src="{{ $char->avatar_url }}" alt="{{ $char->CharName16 }}"
                                    class="w-10 h-10 rounded-full object-cover bg-gray-800 border border-gray-700"
                                    loading="lazy">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ $char->CharName16 }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ __('dashboard.char_level', ['level' => $char->CurLevel]) }}
                                        @if ($char->NickName16 && $char->NickName16 !== '<No Job>')
                                            &bull; {{ $char->NickName16 }}
                                        @endif
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                                    {{ $char->is_online ? 'bg-emerald-900/40 text-emerald-400' : 'bg-gray-800 text-gray-500' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $char->is_online ? 'bg-emerald-500' : 'bg-gray-600' }}"></span>
                                    {{ $char->is_online ? __('dashboard.online') : __('dashboard.offline') }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Voting Status --}}
            @if ($votingEnabled && $votingData)
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70">
                            {{ __('dashboard.voting_status') }}
                        </p>
                        <a href="{{ route('voting.index') }}"
                            class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition">
                            {{ __('dashboard.vote_now') }} &rarr;
                        </a>
                    </div>
                    @if ($votingData['voted_today'])
                        <p class="text-sm text-emerald-400">{{ __('dashboard.voted_today') }}</p>
                    @elseif ($votingData['can_vote'])
                        <p class="text-sm text-cyan-400">{{ __('dashboard.can_vote_now') }}</p>
                    @else
                        <p class="text-sm text-gray-500">{{ __('dashboard.voting_desc') }}</p>
                    @endif
                </div>
            @endif

            {{-- Referral --}}
            @if ($referralEnabled && $referralData)
                <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70 mb-5">
                        {{ __('dashboard.referral_title') }}
                    </p>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        @foreach ([['label' => __('dashboard.referral_valid'), 'value' => $referralData['valid_count']], ['label' => __('dashboard.referral_pending'), 'value' => $referralData['pending_count']], ['label' => __('dashboard.referral_silk_earned'), 'value' => number_format($referralData['total_silk_earned'])]] as $item)
                            <div class="text-center p-4 rounded-xl border border-gray-800 bg-gray-900/80">
                                <p
                                    class="text-2xl font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">
                                    {{ $item['value'] }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $item['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mb-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">
                            {{ __('dashboard.referral_your_link') }}
                        </p>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly
                                value="{{ route('register') . '?ref=' . $referralData['reflink'] }}"
                                class="flex-1 text-sm bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-300 truncate focus:outline-none" />
                            <button type="button"
                                onclick="navigator.clipboard.writeText('{{ route('register') . '?ref=' . $referralData['reflink'] }}').then(() => this.textContent = '{{ __('dashboard.referral_copied') }}')"
                                class="shrink-0 text-xs font-semibold px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-cyan-500 text-gray-950 hover:brightness-110 transition">
                                {{ __('dashboard.referral_copy') }}
                            </button>
                        </div>
                    </div>
                    @if ($referralData['referrals']->isNotEmpty())
                        <div class="mt-4 divide-y divide-gray-800/50">
                            @foreach ($referralData['referrals'] as $referral)
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-300">
                                        {{ $referral->character_name ?? __('dashboard.referral_no_character') }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                                        {{ $referral->status === 'valid' ? 'bg-emerald-900/40 text-emerald-400' : 'bg-yellow-900/30 text-yellow-400' }}">
                                        {{ $referral->status === 'valid' ? __('dashboard.referral_status_valid') : __('dashboard.referral_status_pending') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </section>
@endsection
