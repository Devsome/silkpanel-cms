@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">

            <a href="{{ route('dashboard') }}"
                class="mb-6 inline-flex items-center gap-2 text-xs font-headline font-bold uppercase tracking-widest gp-text-on-surface-variant transition-colors hover:gp-text-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            <div class="gp-card gp-ornate-border p-6 md:p-8">
                <p class="text-xs font-headline font-bold uppercase tracking-widest gp-text-outline mb-5">
                    {{ __('dashboard.silk_history') }}
                </p>

                @if ($history->isEmpty())
                    <p class="text-sm gp-text-on-surface-variant">{{ __('dashboard.silk_history_empty') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b" style="border-color:rgba(242,202,80,0.15);">
                                    @if ($isro)
                                        <th class="pb-2 pr-4 text-xs font-headline uppercase tracking-wide gp-text-outline">
                                            {{ __('silk-history.table.item_name') }}</th>
                                        <th class="pb-2 pr-4 text-xs font-headline uppercase tracking-wide gp-text-outline">
                                            {{ __('silk-history.table.changed_silk') }}</th>
                                        <th class="pb-2 pr-4 text-xs font-headline uppercase tracking-wide gp-text-outline">
                                            {{ __('silk-history.table.remained_silk') }}</th>
                                        <th class="pb-2 pr-4 text-xs font-headline uppercase tracking-wide gp-text-outline">
                                            {{ __('silk-history.table.silk_type') }}</th>
                                        <th class="pb-2 text-xs font-headline uppercase tracking-wide gp-text-outline">
                                            {{ __('silk-history.table.change_date') }}</th>
                                    @else
                                        <th class="pb-2 pr-4 text-xs font-headline uppercase tracking-wide gp-text-outline">
                                            {{ __('silk-history.table.silk_offset') }}</th>
                                        <th class="pb-2 pr-4 text-xs font-headline uppercase tracking-wide gp-text-outline">
                                            {{ __('silk-history.table.buy_quantity') }}</th>
                                        <th class="pb-2 pr-4 text-xs font-headline uppercase tracking-wide gp-text-outline">
                                            {{ __('silk-history.table.silk_remain') }}</th>
                                        <th class="pb-2 text-xs font-headline uppercase tracking-wide gp-text-outline">
                                            {{ __('silk-history.table.auth_date') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $historyItems = $history->items(); @endphp
                                @foreach ($historyItems as $i => $entry)
                                    @php
                                        $nextEntry = $historyItems[$i + 1] ?? null;
                                        $isSilkCharged =
                                            $entry->ChangedSilk == 0 &&
                                            $nextEntry !== null &&
                                            $entry->RemainedSilk > $nextEntry->RemainedSilk;
                                    @endphp
                                    <tr class="border-b" style="border-color:rgba(242,202,80,0.08);">
                                        @if ($isro)
                                            <td class="py-2 pr-4 gp-text-on-surface">
                                                @php
                                                    $cpItemCode = $entry->CPItemCode ?? null;
                                                    $cpItemName = $entry->CPItemName ?? null;
                                                    $imgPath = $cpItemCode
                                                        ? public_path('images/silkroad/webmall/' . $cpItemCode . '.jpg')
                                                        : null;
                                                @endphp
                                                @if ($entry->PTInvoiceID)
                                                    <span class="flex items-center gap-2">
                                                        @if ($cpItemCode && $imgPath && \Illuminate\Support\Facades\File::exists($imgPath))
                                                            <img src="{{ asset('images/silkroad/webmall/' . $cpItemCode . '.jpg') }}"
                                                                alt="" width="32" height="32" class="">
                                                        @elseif($entry->ChangedSilk == 0 && $entry->RemainedSilk > 0)
                                                        @endif
                                                        {{ $cpItemName }}
                                                    </span>
                                                @elseif($entry->ChangedSilk == 0 && $entry->RemainedSilk > 0)
                                                    <span
                                                        class="text-green-400 font-bold">{{ __('silk-history.table.add_silk') }}</span>
                                                @endif
                                            </td>
                                            <td
                                                class="py-2 pr-4 font-headline font-bold
                                                {{ $entry->ChangedSilk == 0 || $isSilkCharged ? 'text-green-400' : ($entry->ChangedSilk < 0 ? 'text-red-400' : 'gp-text-on-surface') }}">
                                                @if ($entry->ChangedSilk == 0 && $entry->RemainedSilk > 0)
                                                    {{ number_format($entry->RemainedSilk) ?? number_format($entry->Silk_Offset) }}
                                                @else
                                                    {{ number_format($entry->ChangedSilk) }}
                                                @endif
                                            </td>
                                            <td class="py-2 pr-4 gp-text-on-surface">
                                                {{ number_format($entry->RemainedSilk) }}</td>
                                            <td class="py-2 pr-4 gp-text-on-surface-variant">
                                                {{ \App\Enums\SilkTypeIsroEnum::tryFrom((int) $entry->SilkType)?->getLabel() ?? $entry->SilkType }}
                                            </td>
                                            <td class="py-2 gp-text-on-surface-variant">{{ $entry->ChangeDate }}</td>
                                        @else
                                            <td class="py-2 pr-4 gp-text-on-surface">
                                                {{ number_format($entry->Silk_Offset) }}</td>
                                            <td
                                                class="py-2 pr-4 font-headline font-bold
                                                {{ $entry->BuyQuantity > 0 ? 'text-green-400' : ($entry->BuyQuantity < 0 ? 'text-red-400' : 'gp-text-on-surface') }}">
                                                {{ number_format($entry->BuyQuantity) }}
                                            </td>
                                            <td class="py-2 pr-4 gp-text-on-surface">
                                                {{ number_format($entry->Silk_Remain) }}</td>
                                            <td class="py-2 gp-text-on-surface-variant">{{ $entry->AuthDate }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>

        </div>
    </section>
@endsection
