@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8 space-y-8">

            {{-- Welcome --}}
            <div class="gp-card gp-ornate-border p-6 md:p-8">
                <p class="text-xs font-headline font-bold uppercase tracking-widest gp-text-outline">
                    {{ __('dashboard.title') }}</p>
                <h1 class="mt-2 text-3xl font-headline font-black uppercase tracking-widest gp-text-primary">
                    {{ __('dashboard.welcome', ['name' => Auth::user()->name]) }}
                </h1>
                <p class="mt-2 text-sm gp-text-on-surface-variant">{{ Auth::user()->email }}</p>
            </div>

            {{-- Silk Balance + Quick Actions --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Silk Balance --}}
                <div class="lg:col-span-2 gp-card gp-ornate-border p-6">
                    <div class="flex items-center justify-between mb-5">
                        <p class="text-xs font-headline font-bold uppercase tracking-widest gp-text-outline">
                            {{ __('dashboard.silk_balance') }}</p>
                        <a href="{{ route('donate.index') }}"
                            class="text-xs font-headline uppercase tracking-wider gp-text-primary hover:opacity-80 transition">
                            {{ __('dashboard.refill_silk') }}
                        </a>
                    </div>

                    @if ($silkData['type'] === 'vsro')
                        <div class="grid grid-cols-3 gap-4">
                            @foreach ([['label' => __('dashboard.silk_own'), 'value' => $silkData['silk_own']], ['label' => __('dashboard.silk_gift'), 'value' => $silkData['silk_gift']], ['label' => __('dashboard.silk_point'), 'value' => $silkData['silk_point']]] as $item)
                                <div class="text-center p-3 gp-card-lowest rounded"
                                    style="border:1px solid rgba(242,202,80,0.15);">
                                    <p class="text-2xl font-headline font-black gp-text-primary">
                                        {{ number_format($item['value']) }}
                                    </p>
                                    <p class="text-xs gp-text-on-surface-variant mt-1">{{ $item['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs gp-text-on-surface-variant text-right">
                            {{ __('dashboard.silk_total', ['total' => number_format($silkData['total'])]) }}
                        </p>
                    @else
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ([['label' => __('dashboard.silk_own'), 'value' => $silkData['silk']], ['label' => __('dashboard.silk_premium'), 'value' => $silkData['premium_silk']]] as $item)
                                <div class="text-center p-3 gp-card-lowest rounded"
                                    style="border:1px solid rgba(242,202,80,0.15);">
                                    <p class="text-2xl font-headline font-black gp-text-primary">
                                        {{ number_format($item['value']) }}
                                    </p>
                                    <p class="text-xs gp-text-on-surface-variant mt-1">{{ $item['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs gp-text-on-surface-variant text-right">
                            {{ __('dashboard.silk_total', ['total' => number_format($silkData['total'])]) }}
                        </p>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div class="gp-card gp-ornate-border p-6">
                    <p class="text-xs font-headline font-bold uppercase tracking-widest gp-text-outline mb-4">
                        {{ __('dashboard.quick_actions') }}</p>
                    <div class="space-y-2">
                        <a href="{{ route('profile.edit') }}"
                            class="group flex items-center gap-3 p-3 gp-card-lowest hover:gp-card transition rounded"
                            style="border:1px solid rgba(242,202,80,0.15);">
                            <svg class="h-4 w-4 gp-text-primary shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span
                                class="text-sm font-headline uppercase tracking-wide gp-text-on-surface group-hover:gp-text-primary transition">
                                {{ __('dashboard.profile') }}
                            </span>
                        </a>
                        <a href="{{ route('donate.index') }}"
                            class="group flex items-center gap-3 p-3 gp-card-lowest hover:gp-card transition rounded"
                            style="border:1px solid rgba(242,202,80,0.15);">
                            <svg class="h-4 w-4 gp-text-primary shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span
                                class="text-sm font-headline uppercase tracking-wide gp-text-on-surface group-hover:gp-text-primary transition">
                                {{ __('dashboard.refill_silk') }}
                            </span>
                        </a>
                        @if ($votingEnabled)
                            <a href="{{ route('voting.index') }}"
                                class="group flex items-center gap-3 p-3 gp-card-lowest hover:gp-card transition rounded"
                                style="border:1px solid rgba(242,202,80,0.15);">
                                <svg class="h-4 w-4 gp-text-primary shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                                <span
                                    class="text-sm font-headline uppercase tracking-wide gp-text-on-surface group-hover:gp-text-primary transition">
                                    {{ __('dashboard.vote_now') }}
                                </span>
                            </a>
                        @endif
                        <a href="{{ route('dashboard.silk-history') }}"
                            class="group flex items-center gap-3 p-3 gp-card-lowest hover:gp-card transition rounded"
                            style="border:1px solid rgba(242,202,80,0.15);">
                            <svg class="h-4 w-4 gp-text-primary shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span
                                class="text-sm font-headline uppercase tracking-wide gp-text-on-surface group-hover:gp-text-primary transition">
                                {{ __('dashboard.silk_history') }}
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Characters --}}
            <div class="gp-card gp-ornate-border p-6">
                <p class="text-xs font-headline font-bold uppercase tracking-widest gp-text-outline mb-5">
                    {{ __('dashboard.your_characters') }}</p>

                @if ($characters->isEmpty())
                    <p class="text-sm gp-text-on-surface-variant">{{ __('dashboard.no_characters') }}</p>
                @else
                    <div class="divide-y" style="border-color:rgba(242,202,80,0.1);">
                        @foreach ($characters as $char)
                            <a href="{{ route('ranking.characters.show', $char->CharID) }}"
                                class="flex items-center gap-4 py-3 -mx-6 px-6 hover:gp-card-lowest transition">
                                <img src="{{ $char->avatar_url }}" alt="{{ $char->CharName16 }}"
                                    class="w-10 h-10 rounded-full object-cover gp-card-lowest" loading="lazy">
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm font-headline font-bold uppercase tracking-wide gp-text-on-surface group-hover:gp-text-primary truncate">
                                        {{ $char->CharName16 }}
                                    </p>
                                    <p class="text-xs gp-text-on-surface-variant">
                                        {{ __('dashboard.char_level', ['level' => $char->CurLevel]) }}
                                        @if ($char->NickName16 && $char->NickName16 !== '<No Job>')
                                            &bull; {{ $char->NickName16 }}
                                        @endif
                                    </p>
                                </div>
                                <span
                                    class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                                    {{ $char->is_online ? 'bg-green-900/30 text-green-400' : 'bg-gray-800 text-gray-400' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $char->is_online ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                                    {{ $char->is_online ? __('dashboard.online') : __('dashboard.offline') }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Voting Status (only if voting package is installed) --}}
            @if ($votingEnabled && $votingData)
                <div class="gp-card gp-ornate-border p-6">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-headline font-bold uppercase tracking-widest gp-text-outline">
                            {{ __('dashboard.voting_status') }}</p>
                        <a href="{{ route('voting.index') }}"
                            class="text-xs font-headline uppercase tracking-wider gp-text-primary hover:opacity-80 transition">
                            {{ __('dashboard.vote_now') }}
                        </a>
                    </div>
                    @if ($votingData['voted_today'])
                        <p class="text-sm text-green-400">{{ __('dashboard.voted_today') }}</p>
                    @elseif ($votingData['can_vote'])
                        <p class="text-sm gp-text-primary">{{ __('dashboard.can_vote_now') }}</p>
                    @else
                        <p class="text-sm gp-text-on-surface-variant">{{ __('dashboard.voting_desc') }}</p>
                    @endif
                </div>
            @endif

            {{-- Referral System (only if enabled) --}}
            @if ($referralEnabled && $referralData)
                <div class="gp-card gp-ornate-border p-6">
                    <p class="text-xs font-headline font-bold uppercase tracking-widest gp-text-outline mb-5">
                        {{ __('dashboard.referral_title') }}</p>

                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center p-3 gp-card-lowest rounded" style="border:1px solid rgba(242,202,80,0.15);">
                            <p class="text-2xl font-headline font-black gp-text-primary">
                                {{ $referralData['valid_count'] }}
                            </p>
                            <p class="text-xs gp-text-on-surface-variant mt-1">{{ __('dashboard.referral_valid') }}</p>
                        </div>
                        <div class="text-center p-3 gp-card-lowest rounded"
                            style="border:1px solid rgba(242,202,80,0.15);">
                            <p class="text-2xl font-headline font-black gp-text-primary">
                                {{ $referralData['pending_count'] }}
                            </p>
                            <p class="text-xs gp-text-on-surface-variant mt-1">{{ __('dashboard.referral_pending') }}</p>
                        </div>
                        <div class="text-center p-3 gp-card-lowest rounded"
                            style="border:1px solid rgba(242,202,80,0.15);">
                            <p class="text-2xl font-headline font-black gp-text-primary">
                                {{ number_format($referralData['total_silk_earned']) }}
                            </p>
                            <p class="text-xs gp-text-on-surface-variant mt-1">{{ __('dashboard.referral_silk_earned') }}
                            </p>
                        </div>
                    </div>

                    {{-- Referral link --}}
                    <div class="mb-4">
                        <p class="text-xs font-headline uppercase tracking-wider gp-text-on-surface-variant mb-2">
                            {{ __('dashboard.referral_your_link') }}
                        </p>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly
                                value="{{ route('register') . '?ref=' . $referralData['reflink'] }}"
                                class="flex-1 text-sm gp-card-lowest border rounded px-3 py-2 gp-text-on-surface truncate"
                                style="border-color:rgba(242,202,80,0.25);" />
                            <button type="button"
                                onclick="navigator.clipboard.writeText('{{ route('register') . '?ref=' . $referralData['reflink'] }}').then(() => this.textContent = '{{ __('dashboard.referral_copied') }}')"
                                class="gp-gold-btn shrink-0 text-xs font-headline uppercase tracking-wider px-4 py-2 rounded gp-btn-primary transition">
                                {{ __('dashboard.referral_copy') }}
                            </button>
                        </div>
                    </div>

                    {{-- Referral list --}}
                    @if ($referralData['referrals']->isNotEmpty())
                        <div class="mt-4 divide-y border-amber-500 text-amber-400"
                            style="border-color:rgba(242,202,80,0.1);">
                            @foreach ($referralData['referrals'] as $referral)
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm font-headline uppercase tracking-wide gp-text-on-surface">
                                        {{ $referral->character_name ?? __('dashboard.referral_no_character') }}
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                                        {{ $referral->status === 'valid' ? 'bg-green-900/30 text-green-400' : 'bg-yellow-900/30 text-yellow-400' }}">
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
