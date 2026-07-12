<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold font-headline gp-text-primary uppercase tracking-widest">{{ __('history.global_title') }}</h2>
            <p class="mt-1 text-sm gp-text-on-surface-variant">{{ __('history.global_subtitle') }}</p>
        </div>

        @if ($available)
            <select wire:model.live="tradeFilter"
                class="py-1.5 pl-3 pr-8 text-sm gp-text-on-surface focus:outline-none"
                style="border: 1px solid rgba(77, 70, 53, 0.5); background-color: var(--gp-surface-container);">
                <option value="">{{ __('history.filter_all') }}</option>
                <option value="WTS">{{ __('history.filter_wts') }}</option>
                <option value="WTB">{{ __('history.filter_wtb') }}</option>
            </select>
        @endif
    </div>

    @if (!$available)
        <div class="text-center py-12">
            <p class="gp-text-on-surface-variant">{{ __('history.global_unavailable') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="overflow-x-auto gp-card gp-ghost-border">
            <table class="min-w-full divide-y" style="border-color: rgba(77, 70, 53, 0.2);">
                <thead class="gp-card-high">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium gp-text-on-surface-variant uppercase tracking-wider">{{ __('history.col_message') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium gp-text-on-surface-variant uppercase tracking-wider">{{ __('history.col_character') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium gp-text-on-surface-variant uppercase tracking-wider">{{ __('history.col_date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: rgba(77, 70, 53, 0.2);">
                    @foreach ($rows as $row)
                        <tr class="transition-colors hover:gp-card-high" style="background-color: var(--gp-surface-container);">
                            <td class="px-4 py-3 text-sm gp-text-on-surface break-words max-w-xl">{{ $row->Comment }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm gp-text-on-surface">
                                @if (filled($row->CharName) && $row->CharID)
                                    <a href="{{ route('ranking.characters.show', ['idOrSlug' => $row->CharID]) }}"
                                        class="inline-flex items-center gap-2 gp-text-primary hover:text-yellow-400 transition">
                                        <img src="{{ \App\Enums\CharacterAvatarMapEnum::getAvatarUrl((int) $row->RefObjID, (string) config('silkpanel.version')) }}"
                                            onerror="this.style.display='none'"
                                            class="w-6 h-6 rounded-full object-cover" alt="">
                                        <span>{{ $row->CharName }}</span>
                                    </a>
                                @elseif (filled($row->CharName))
                                    <span>{{ $row->CharName }}</span>
                                @else
                                    <span class="gp-text-outline">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm gp-text-on-surface-variant">
                                <span title="{{ \Carbon\Carbon::make($row->EventTime)?->toDayDateTimeString() }}">
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
        <div class="text-center py-12">
            <p class="gp-text-on-surface-variant">{{ __('history.no_records') }}</p>
        </div>
    @endif
</div>
