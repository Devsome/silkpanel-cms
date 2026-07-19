<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="ag-text-muted text-sm">{{ __('history.global_subtitle') }}</p>

        @if ($available)
            <select wire:model.live="tradeFilter" class="ag-input py-2 pl-3 pr-8 text-sm">
                <option value="">{{ __('history.filter_all') }}</option>
                <option value="WTS">{{ __('history.filter_wts') }}</option>
                <option value="WTB">{{ __('history.filter_wtb') }}</option>
            </select>
        @endif
    </div>

    @if (!$available)
        <div class="text-center py-16 ag-card">
            <p class="ag-text-muted text-sm">{{ __('history.global_unavailable') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="ag-card overflow-hidden">
            <table class="ag-table">
                <thead>
                    <tr>
                        <th>{{ __('history.col_message') }}</th>
                        <th>{{ __('history.col_character') }}</th>
                        <th>{{ __('history.col_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td class="break-words max-w-xl ag-text-surface">{{ $row->Comment }}</td>
                            <td>
                                @if (filled($row->CharName) && $row->CharID)
                                    <a href="{{ route('ranking.characters.show', ['idOrSlug' => \Illuminate\Support\Str::slug($row->CharID)]) }}"
                                        class="inline-flex items-center gap-2 ag-text-primary hover:underline">
                                        <img src="{{ \App\Enums\CharacterAvatarMapEnum::getAvatarUrl((int) $row->RefObjID, (string) config('silkpanel.version')) }}"
                                            onerror="this.style.display='none'"
                                            class="w-6 h-6 rounded-full object-cover" alt="">
                                        <span class="font-semibold">{{ $row->CharName }}</span>
                                    </a>
                                @elseif (filled($row->CharName))
                                    <span class="font-semibold ag-text-surface">{{ $row->CharName }}</span>
                                @else
                                    <span class="ag-text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="ag-text-muted" title="{{ \Carbon\Carbon::make($row->EventTime)?->toDayDateTimeString() }}">
                                    {{ \Carbon\Carbon::make($row->EventTime)?->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($rows->hasPages())
            <div class="mt-4">{{ $rows->links() }}</div>
        @endif
    @else
        <div class="text-center py-16 ag-card">
            <p class="ag-text-muted text-sm">{{ __('history.no_records') }}</p>
        </div>
    @endif
</div>
