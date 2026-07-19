<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white uppercase tracking-widest">{{ __('history.global_title') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('history.global_subtitle') }}</p>
        </div>

        @if ($available)
            <select wire:model.live="tradeFilter"
                class="rounded-xl border border-gray-700 bg-gray-800 py-1.5 pl-3 pr-8 text-sm text-gray-200 focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">{{ __('history.filter_all') }}</option>
                <option value="WTS">{{ __('history.filter_wts') }}</option>
                <option value="WTB">{{ __('history.filter_wtb') }}</option>
            </select>
        @endif
    </div>

    @if (!$available)
        <div class="text-center py-12">
            <p class="text-gray-500">{{ __('history.global_unavailable') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="overflow-x-auto rounded-2xl border border-gray-800 bg-gray-900/50">
            <table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-gray-900/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('history.col_message') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('history.col_character') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('history.col_date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    @foreach ($rows as $row)
                        <tr class="hover:bg-emerald-500/5 transition">
                            <td class="px-4 py-3 text-sm text-gray-100 break-words max-w-xl">{{ $row->Comment }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-200">
                                @if (filled($row->CharName) && $row->CharID)
                                    <a href="{{ route('ranking.characters.show', ['idOrSlug' => \Illuminate\Support\Str::slug($row->CharID)]) }}"
                                        class="inline-flex items-center gap-2 text-emerald-400 hover:text-emerald-300 transition">
                                        <img src="{{ \App\Enums\CharacterAvatarMapEnum::getAvatarUrl((int) $row->RefObjID, (string) config('silkpanel.version')) }}"
                                            onerror="this.style.display='none'"
                                            class="w-6 h-6 rounded-full object-cover bg-gray-800" alt="">
                                        <span>{{ $row->CharName }}</span>
                                    </a>
                                @elseif (filled($row->CharName))
                                    <span>{{ $row->CharName }}</span>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-400">
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
            <p class="text-gray-500">{{ __('history.no_records') }}</p>
        </div>
    @endif
</div>
