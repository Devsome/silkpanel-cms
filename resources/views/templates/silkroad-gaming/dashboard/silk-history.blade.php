@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 md:px-8">

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-emerald-400 transition mb-6">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('dashboard.back_to_dashboard') }}
            </a>

            <div class="rounded-2xl border border-gray-800 bg-gray-900/50 backdrop-blur p-6 md:p-8">
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-400/70 mb-5">
                    {{ __('dashboard.silk_history') }}
                </p>

                @if ($history->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('dashboard.silk_history_empty') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b border-gray-800">
                                    @if ($isro)
                                        <th class="pb-3 pr-4 text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ __('silk-history.table.item_name') }}</th>
                                        <th class="pb-3 pr-4 text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ __('silk-history.table.changed_silk') }}</th>
                                        <th class="pb-3 pr-4 text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ __('silk-history.table.remained_silk') }}</th>
                                        <th class="pb-3 pr-4 text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ __('silk-history.table.silk_type') }}</th>
                                        <th class="pb-3 text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ __('silk-history.table.change_date') }}</th>
                                    @else
                                        <th class="pb-3 pr-4 text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ __('silk-history.table.silk_offset') }}</th>
                                        <th class="pb-3 pr-4 text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ __('silk-history.table.buy_quantity') }}</th>
                                        <th class="pb-3 pr-4 text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ __('silk-history.table.silk_remain') }}</th>
                                        <th class="pb-3 text-xs font-medium uppercase tracking-wider text-gray-500">
                                            {{ __('silk-history.table.auth_date') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800/50">
                                @php $historyItems = $history->items(); @endphp
                                @foreach ($historyItems as $i => $entry)
                                    @php
                                        $nextEntry = $historyItems[$i + 1] ?? null;
                                        $isSilkCharged =
                                            $entry->ChangedSilk == 0 &&
                                            $nextEntry !== null &&
                                            $entry->RemainedSilk > $nextEntry->RemainedSilk;
                                    @endphp
                                    <tr class="hover:bg-gray-800/30 transition">
                                        @if ($isro)
                                            <td class="py-3 pr-4 text-gray-200">
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
                                                                alt="" width="32" height="32">
                                                        @endif
                                                        {{ $cpItemName }}
                                                    </span>
                                                @elseif($entry->ChangedSilk == 0 && $entry->RemainedSilk > 0)
                                                    <span
                                                        class="text-emerald-400 font-semibold">{{ __('silk-history.table.add_silk') }}</span>
                                                @endif
                                            </td>
                                            <td
                                                class="py-3 pr-4 font-semibold
                                                {{ $entry->ChangedSilk == 0 || $isSilkCharged ? 'text-emerald-400' : ($entry->ChangedSilk < 0 ? 'text-red-400' : 'text-gray-200') }}">
                                                @if ($entry->ChangedSilk == 0 && $entry->RemainedSilk > 0)
                                                    {{ number_format($entry->RemainedSilk) ?? number_format($entry->Silk_Offset) }}
                                                @else
                                                    {{ number_format($entry->ChangedSilk) }}
                                                @endif
                                            </td>
                                            <td class="py-3 pr-4 text-gray-200">{{ number_format($entry->RemainedSilk) }}
                                            </td>
                                            <td class="py-3 pr-4 text-gray-400">
                                                {{ \App\Enums\SilkTypeIsroEnum::tryFrom((int) $entry->SilkType)?->getLabel() ?? $entry->SilkType }}
                                            </td>
                                            <td class="py-3 text-gray-400">{{ $entry->ChangeDate }}</td>
                                        @else
                                            <td class="py-3 pr-4 text-gray-200">{{ number_format($entry->Silk_Offset) }}
                                            </td>
                                            <td
                                                class="py-3 pr-4 font-semibold
                                                {{ $entry->BuyQuantity > 0 ? 'text-emerald-400' : ($entry->BuyQuantity < 0 ? 'text-red-400' : 'text-gray-200') }}">
                                                {{ number_format($entry->BuyQuantity) }}
                                            </td>
                                            <td class="py-3 pr-4 text-gray-200">{{ number_format($entry->Silk_Remain) }}
                                            </td>
                                            <td class="py-3 text-gray-400">{{ $entry->AuthDate }}</td>
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
