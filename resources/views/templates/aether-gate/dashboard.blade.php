@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8 space-y-6">

            {{-- Welcome header --}}
            <div class="ag-profile-header p-6 md:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="ag-section-eyebrow">{{ __('dashboard.title') }}</p>
                        <h1 class="ag-font-display text-2xl md:text-3xl font-bold ag-text-surface mt-2">
                            {{ __('dashboard.welcome', ['name' => Auth::user()->name]) }}
                        </h1>
                        <p class="text-sm ag-text-muted mt-1">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="hidden md:flex items-center gap-3">
                        <a href="{{ route('donate.index') }}" class="px-4 py-2 ag-btn-primary text-xs">
                            {{ __('dashboard.refill_silk') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Silk Balance --}}
                <div class="lg:col-span-2 ag-card ag-accent-line p-6">
                    <div class="flex items-center justify-between mb-5">
                        <p class="ag-section-eyebrow">{{ __('dashboard.silk_balance') }}</p>
                        <a href="{{ route('donate.index') }}"
                            class="text-xs ag-font-display font-semibold tracking-wider uppercase ag-text-primary hover:opacity-80 transition-opacity">
                            {{ __('dashboard.refill_silk') }} →
                        </a>
                    </div>

                    @if ($silkData['type'] === 'vsro')
                        <div class="grid grid-cols-3 gap-4">
                            @foreach ([['label' => __('dashboard.silk_own'), 'value' => $silkData['silk_own']], ['label' => __('dashboard.silk_gift'), 'value' => $silkData['silk_gift']], ['label' => __('dashboard.silk_point'), 'value' => $silkData['silk_point']]] as $item)
                                <div class="text-center p-4 ag-card-low">
                                    <p class="ag-stat-amber text-2xl">{{ number_format($item['value']) }}</p>
                                    <p class="text-xs ag-text-muted mt-1.5">{{ $item['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs ag-text-muted text-right">
                            {{ __('dashboard.silk_total', ['total' => number_format($silkData['total'])]) }}
                        </p>
                    @else
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ([['label' => __('dashboard.silk_own'), 'value' => $silkData['silk']], ['label' => __('dashboard.silk_premium'), 'value' => $silkData['premium_silk']]] as $item)
                                <div class="text-center p-4 ag-card-low">
                                    <p class="ag-stat-amber text-2xl">{{ number_format($item['value']) }}</p>
                                    <p class="text-xs ag-text-muted mt-1.5">{{ $item['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs ag-text-muted text-right">
                            {{ __('dashboard.silk_total', ['total' => number_format($silkData['total'])]) }}
                        </p>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div class="ag-card p-6">
                    <p class="ag-section-eyebrow mb-4">{{ __('dashboard.quick_actions') }}</p>
                    <div class="space-y-2">
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                            <div class="w-7 h-7 flex items-center justify-center shrink-0"
                                style="background: rgba(34,211,238,0.1);">
                                <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('dashboard.profile') }}</span>
                        </a>
                        <a href="{{ route('donate.index') }}"
                            class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                            <div class="w-7 h-7 flex items-center justify-center shrink-0"
                                style="background: rgba(34,211,238,0.1);">
                                <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('dashboard.refill_silk') }}</span>
                        </a>
                        @if ($votingEnabled)
                            <a href="{{ route('voting.index') }}"
                                class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                                <div class="w-7 h-7 flex items-center justify-center shrink-0"
                                    style="background: rgba(34,211,238,0.1);">
                                    <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('dashboard.vote_now') }}</span>
                            </a>
                        @endif
                        @if ($worldMapEnabled)
                            <a href="{{ route('dashboard.map') }}"
                                class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                                <div class="w-7 h-7 flex items-center justify-center shrink-0"
                                    style="background: rgba(34,211,238,0.1);">
                                    <svg class="w-4 h-4 ag-text-primary" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('dashboard.world_map') }}</span>
                            </a>
                        @endif
                        @if ($webmallEnabled)
                            <a href="{{ route('webmall.index') }}"
                                class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                                <div class="w-7 h-7 flex items-center justify-center shrink-0"
                                    style="background: rgba(34,211,238,0.1);">
                                    <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('dashboard.webmall') }}</span>
                            </a>
                        @endif
                        <a href="{{ route('dashboard.silk-history') }}"
                            class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                            <div class="w-7 h-7 flex items-center justify-center shrink-0"
                                style="background: rgba(34,211,238,0.1);">
                                <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <span
                                class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('dashboard.silk_history') }}</span>
                        </a>
                        @if (Route::has('tickets.index') && $ticketSystemEnabled)
                            <a href="{{ route('tickets.index') }}"
                                class="flex items-center gap-3 p-3 ag-card-low hover:border-cyan-400/20 transition-all group">
                                <div class="w-7 h-7 flex items-center justify-center shrink-0"
                                    style="background: rgba(34,211,238,0.1);">
                                    <svg class="w-4 h-4 ag-text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-sm font-medium ag-text-surface group-hover:ag-text-primary transition-colors">{{ __('dashboard.support_tickets') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Characters --}}
            @if (isset($characters) && $characters->isNotEmpty())
                <div class="ag-card p-6">
                    <p class="ag-section-eyebrow mb-5">{{ __('dashboard.characters') }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($characters as $char)
                            <div class="ag-card-low p-4 flex items-start gap-4">
                                <div class="w-10 h-10 flex items-center justify-center shrink-0"
                                    style="background: rgba(34,211,238,0.08); border: 1px solid rgba(34,211,238,0.15);">
                                    <svg class="w-5 h-5 ag-text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold ag-text-surface text-sm truncate">{{ e($char->CharName16) }}
                                    </p>
                                    <p class="text-xs ag-text-muted mt-0.5">{{ __('dashboard.level') }} <span
                                            class="ag-stat-number text-sm">{{ $char->CurLevel }}</span></p>
                                    @if (isset($char->HP))
                                        <p class="text-xs ag-text-muted">HP: {{ number_format($char->HP) }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recent purchases --}}
            @if (isset($recentPurchases) && $recentPurchases->isNotEmpty())
                <div class="ag-card p-6">
                    <p class="ag-section-eyebrow mb-5">{{ __('dashboard.recent_purchases') }}</p>
                    <div class="overflow-x-auto">
                        <table class="ag-table">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.item') }}</th>
                                    <th>{{ __('dashboard.amount') }}</th>
                                    <th>{{ __('dashboard.date') }}</th>
                                    <th>{{ __('dashboard.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentPurchases as $purchase)
                                    <tr>
                                        <td class="font-medium">{{ e($purchase->item_name ?? '—') }}</td>
                                        <td class="ag-stat-amber">{{ number_format($purchase->amount ?? 0) }}</td>
                                        <td class="ag-text-muted">{{ $purchase->created_at->format('M d, Y') }}</td>
                                        <td><span class="ag-badge"
                                                style="background: rgba(52,211,153,0.1); border: 1px solid rgba(52,211,153,0.2); color: #34d399;">{{ $purchase->status ?? 'completed' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
