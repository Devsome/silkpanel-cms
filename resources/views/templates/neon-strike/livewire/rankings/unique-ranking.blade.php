<div>
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2
            class="text-sm font-black uppercase tracking-[0.2em] bg-linear-to-r from-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
            {{ e($title) }}
        </h2>
        @if ($configured)
            <div class="relative w-full sm:w-64">
                <div class="flex items-center border border-zinc-700 bg-zinc-950 overflow-hidden">
                    <svg class="ml-3 w-4 h-4 shrink-0 text-zinc-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('ranking.search_unique_placeholder') }}"
                        class="block min-w-0 grow bg-transparent py-2 pr-3 pl-2 text-xs font-mono text-zinc-100 placeholder:text-zinc-600 focus:outline-none border-0">
                </div>
            </div>
        @endif
    </div>

    @if (!$configured)
        <div class="py-12 text-center border border-zinc-800/60">
            <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">
                {{ __('ranking.unique_not_configured') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="overflow-x-auto border border-violet-500/20">
            <table class="min-w-full">
                <thead class="border-b border-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">#</th>
                        @foreach ($columns as $col)
                            <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">
                                {{ e($col['label']) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @foreach ($rows as $row)
                        @php $rank = $startRank + $loop->index; @endphp
                        <tr class="hover:bg-violet-500/5 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-mono">
                                @if ($rank === 1)
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 bg-amber-500/20 text-amber-400 text-xs font-bold">1</span>
                                @elseif ($rank === 2)
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 bg-zinc-600/50 text-zinc-300 text-xs font-bold">2</span>
                                @elseif ($rank === 3)
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 bg-amber-900/30 text-amber-600 text-xs font-bold">3</span>
                                @else
                                    <span class="text-zinc-600">{{ $rank }}</span>
                                @endif
                            </td>
                            @foreach ($columns as $col)
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-300">
                                    @php $value = $row->{$col['column']} ?? '—'; @endphp
                                    @if ($col['column'] === 'CharName16')
                                        <a href="{{ route('ranking.characters.show', $row->CharID) }}"
                                            class="font-bold font-mono text-xs uppercase tracking-wide text-violet-400 hover:text-violet-300 transition">
                                            {{ e((string) $value) }}
                                        </a>
                                    @elseif ($col['column'] === 'UniqueKillCount')
                                        <span
                                            class="font-bold font-mono text-cyan-400">{{ number_format((int) $value) }}</span>
                                    @elseif ($col['column'] === 'CurLevel')
                                        <span class="font-bold font-mono text-zinc-200">{{ $value }}</span>
                                    @else
                                        {{ e((string) $value) }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($paginate && $rows->hasPages())
            <div class="mt-4">{{ $rows->links() }}</div>
        @endif
    @else
        <div class="py-12 text-center border border-zinc-800/60">
            <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">{{ __('ranking.no_data') }}</p>
        </div>
    @endif
</div>
