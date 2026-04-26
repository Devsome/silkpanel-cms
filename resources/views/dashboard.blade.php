<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('dashboard.title') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('dashboard.welcome', ['name' => Auth::user()->name]) }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ Auth::user()->email }}
                </p>
            </div>

            {{-- Silk balance + Quick actions --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Silk Balance --}}
                <div
                    class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ __('dashboard.silk_balance') }}
                        </h3>
                        <a href="{{ route('donate.index') }}"
                            class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">
                            {{ __('dashboard.refill_silk') }}
                        </a>
                    </div>

                    @if ($silkData['type'] === 'vsro')
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($silkData['silk_own']) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('dashboard.silk_own') }}
                                </p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($silkData['silk_gift']) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('dashboard.silk_gift') }}
                                </p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                    {{ number_format($silkData['silk_point']) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('dashboard.silk_point') }}
                                </p>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 text-right">
                            {{ __('dashboard.silk_total', ['total' => number_format($silkData['total'])]) }}
                        </p>
                    @else
                        {{-- ISRO --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($silkData['silk']) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('dashboard.silk_own') }}
                                </p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($silkData['premium_silk']) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('dashboard.silk_premium') }}
                                </p>
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 text-right">
                            {{ __('dashboard.silk_total', ['total' => number_format($silkData['total'])]) }}
                        </p>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('dashboard.quick_actions') }}
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-sm text-gray-700 dark:text-gray-300 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ __('dashboard.profile') }}
                        </a>
                        <a href="{{ route('donate.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-sm text-gray-700 dark:text-gray-300 hover:text-emerald-700 dark:hover:text-emerald-300 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('dashboard.refill_silk') }}
                        </a>
                        @if ($votingEnabled)
                            <a href="{{ route('voting.index') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-amber-50 dark:hover:bg-amber-900/20 text-sm text-gray-700 dark:text-gray-300 hover:text-amber-700 dark:hover:text-amber-300 transition">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                {{ __('dashboard.vote_now') }}
                            </a>
                        @endif
                        @if ($worldMapEnabled)
                            <a href="{{ route('dashboard.map') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-violet-50 dark:hover:bg-violet-900/20 text-sm text-gray-700 dark:text-gray-300 hover:text-violet-700 dark:hover:text-violet-300 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="h-4 w-4 gp-text-primary shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                </svg>
                                {{ __('dashboard.world_map') }}
                            </a>
                        @endif
                        <a href="{{ route('dashboard.silk-history') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-purple-50 dark:hover:bg-purple-900/20 text-sm text-gray-700 dark:text-gray-300 hover:text-purple-700 dark:hover:text-purple-300 transition">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            {{ __('dashboard.silk_history') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Characters --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                    {{ __('dashboard.your_characters') }}
                </h3>

                @if ($characters->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.no_characters') }}</p>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($characters as $char)
                            <a href="{{ route('ranking.characters.show', $char->CharID) }}"
                                class="flex items-center gap-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/40 -mx-6 px-6 transition">
                                <img src="{{ $char->avatar_url }}" alt="{{ $char->CharName16 }}"
                                    class="w-10 h-10 rounded-full object-cover bg-gray-100 dark:bg-gray-700"
                                    loading="lazy">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $char->CharName16 }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('dashboard.char_level', ['level' => $char->CurLevel]) }}
                                        @if ($char->NickName16 && $char->NickName16 !== '<No Job>')
                                            &bull; {{ $char->NickName16 }}
                                        @endif
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                                    {{ $char->is_online
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $char->is_online ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $char->is_online ? __('dashboard.online') : __('dashboard.offline') }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Voting Status (only if voting package is installed) --}}
            @if ($votingEnabled && $votingData)
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ __('dashboard.voting_status') }}
                        </h3>
                        <a href="{{ route('voting.index') }}"
                            class="text-sm text-amber-600 dark:text-amber-400 hover:underline">
                            {{ __('dashboard.vote_now') }}
                        </a>
                    </div>
                    @if ($votingData['voted_today'])
                        <p class="text-sm text-green-600 dark:text-green-400">{{ __('dashboard.voted_today') }}</p>
                    @elseif ($votingData['can_vote'])
                        <p class="text-sm text-amber-600 dark:text-amber-400">{{ __('dashboard.can_vote_now') }}</p>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.voting_desc') }}</p>
                    @endif
                </div>
            @endif

            {{-- Referral System (only if enabled) --}}
            @if ($referralEnabled && $referralData)
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('dashboard.referral_title') }}
                    </h3>

                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $referralData['valid_count'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('dashboard.referral_valid') }}
                            </p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                                {{ $referralData['pending_count'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('dashboard.referral_pending') }}
                            </p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                {{ number_format($referralData['total_silk_earned']) }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('dashboard.referral_silk_earned') }}
                            </p>
                        </div>
                    </div>

                    {{-- Referral link --}}
                    <div class="mb-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            {{ __('dashboard.referral_your_link') }}
                        </p>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly
                                value="{{ route('register') . '?ref=' . $referralData['reflink'] }}"
                                class="flex-1 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 text-gray-700 dark:text-gray-300 truncate" />
                            <button type="button"
                                onclick="navigator.clipboard.writeText('{{ route('register') . '?ref=' . $referralData['reflink'] }}').then(() => this.textContent = '{{ __('dashboard.referral_copied') }}')"
                                class="shrink-0 text-sm px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                {{ __('dashboard.referral_copy') }}
                            </button>
                        </div>
                    </div>

                    {{-- Referral list --}}
                    @if ($referralData['referrals']->isNotEmpty())
                        <div class="mt-4 divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($referralData['referrals'] as $referral)
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $referral->character_name ?? __('dashboard.referral_no_character') }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                                        {{ $referral->status === 'valid'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                        {{ $referral->status === 'valid' ? __('dashboard.referral_status_valid') : __('dashboard.referral_status_pending') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
