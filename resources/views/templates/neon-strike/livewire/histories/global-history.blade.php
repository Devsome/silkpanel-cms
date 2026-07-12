<div>
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-sm font-black uppercase tracking-[0.2em] bg-linear-to-r from-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
                {{ __('history.global_title') }}
            </h2>
            <p class="mt-1 text-xs font-mono uppercase tracking-wider text-zinc-600">
                {{ __('history.global_subtitle') }}
            </p>
        </div>

        @if ($available)
            <select wire:model.live="tradeFilter"
                class="border border-zinc-700 bg-zinc-950 py-1.5 pl-3 pr-8 text-xs font-mono uppercase tracking-wider text-zinc-200 focus:outline-none focus:border-violet-500">
                <option value="">{{ __('history.filter_all') }}</option>
                <option value="WTS">{{ __('history.filter_wts') }}</option>
                <option value="WTB">{{ __('history.filter_wtb') }}</option>
            </select>
        @endif
    </div>

    @if (!$available)
        <div class="py-12 text-center border border-zinc-800/60">
            <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">{{ __('history.global_unavailable') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="overflow-x-auto border border-violet-500/20">
            <table class="min-w-full">
                <thead class="border-b border-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">{{ __('history.col_message') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">{{ __('history.col_character') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">{{ __('history.col_date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @foreach ($rows as $row)
                        <tr class="hover:bg-violet-500/5 transition">
                            <td class="px-4 py-3 text-sm text-zinc-200 break-words max-w-xl">{{ $row->Comment }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                @if (filled($row->CharName) && $row->CharID)
                                    <a href="{{ route('ranking.characters.show', ['idOrSlug' => $row->CharID]) }}"
                                        class="inline-flex items-center gap-2 font-bold font-mono text-xs uppercase tracking-wide text-violet-400 hover:text-violet-300 transition">
                                        <img src="{{ \App\Enums\CharacterAvatarMapEnum::getAvatarUrl((int) $row->RefObjID, (string) config('silkpanel.version')) }}"
                                            onerror="this.style.display='none'"
                                            class="w-6 h-6 object-cover bg-zinc-800" alt="">
                                        <span>{{ $row->CharName }}</span>
                                    </a>
                                @elseif (filled($row->CharName))
                                    <span class="font-bold font-mono text-xs uppercase tracking-wide text-zinc-300">{{ $row->CharName }}</span>
                                @else
                                    <span class="text-zinc-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs font-mono text-zinc-400">
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
        <div class="py-12 text-center border border-zinc-800/60">
            <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">{{ __('history.no_records') }}</p>
        </div>
    @endif
</div>
