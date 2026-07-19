<div>
    <div class="mb-5 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h2 class="text-sm font-black uppercase tracking-[0.2em] bg-linear-to-r from-violet-400 to-fuchsia-400 bg-clip-text text-transparent">
                {{ __('history.unique_title') }}
            </h2>
            <p class="mt-1 text-xs font-mono uppercase tracking-wider text-zinc-600">
                {{ __('history.unique_subtitle') }}
            </p>
        </div>

        @if ($available)
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" wire:click="toggleSpawns" @checked($showSpawns)
                    class="border-zinc-700 bg-zinc-950 text-violet-500 focus:ring-violet-500/40">
                <span class="text-xs font-mono uppercase tracking-wider text-zinc-400">{{ __('history.show_spawns') }}</span>
            </label>
        @endif
    </div>

    @if (!$available)
        <div class="py-12 text-center border border-zinc-800/60">
            <p class="text-xs font-mono uppercase tracking-[0.3em] text-zinc-600">{{ __('history.unique_unavailable') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="overflow-x-auto border border-violet-500/20">
            <table class="min-w-full">
                <thead class="border-b border-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">{{ __('history.col_unique') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">{{ __('history.col_time') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">{{ __('history.col_killer') }}</th>
                        @if ($showArea ?? true)
                        <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">{{ __('history.col_area') }}</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-mono uppercase tracking-wider text-zinc-500">{{ __('history.col_status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @foreach ($rows as $row)
                        @php
                            $uniqueName = $uniques[$row->Value]['name'] ?? $row->Value;
                            $isKill = $row->ValueCodeName128 === 'KILL_UNIQUE_MONSTER';
                        @endphp
                        <tr class="hover:bg-violet-500/5 transition">
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center gap-2 font-bold font-mono text-xs uppercase tracking-wide text-zinc-100">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-fuchsia-500/15 text-fuchsia-400">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 1c-3.87 0-7 3.02-7 6.75 0 1.98.88 3.76 2.28 5v2.5c0 .41.34.75.75.75h.72v1.5c0 .41.34.75.75.75s.75-.34.75-.75v-1.5h1.5v1.5c0 .41.34.75.75.75s.75-.34.75-.75v-1.5h.72c.41 0 .75-.34.75-.75v-2.5A6.73 6.73 0 0017 7.75C17 4.02 13.87 1 10 1zM7.5 9.5A1.25 1.25 0 117.5 7a1.25 1.25 0 010 2.5zm5 0A1.25 1.25 0 1112.5 7a1.25 1.25 0 010 2.5z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    {{ $uniqueName }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs font-mono text-zinc-400">
                                <span title="{{ \Carbon\Carbon::make($row->EventTime)?->toDayDateTimeString() }}">
                                    {{ \Carbon\Carbon::make($row->EventTime)?->diffForHumans() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                @if ($isKill && filled($row->CharName16))
                                    <a href="{{ route('ranking.characters.show', ['idOrSlug' => \Illuminate\Support\Str::slug($row->CharID)]) }}"
                                        class="inline-flex items-center gap-2 font-bold font-mono text-xs uppercase tracking-wide text-violet-400 hover:text-violet-300 transition">
                                        <img src="{{ \App\Enums\CharacterAvatarMapEnum::getAvatarUrl((int) $row->RefObjID, (string) config('silkpanel.version')) }}"
                                            onerror="this.style.display='none'"
                                            class="w-6 h-6 object-cover bg-zinc-800" alt="">
                                        <span>{{ $row->CharName16 }}</span>
                                    </a>
                                @else
                                    <span class="text-zinc-600">—</span>
                                @endif
                            </td>
                            @if ($showArea ?? true)
                            <td class="px-4 py-3 whitespace-nowrap text-xs font-mono text-zinc-400">
                                {{ filled($row->AreaName) ? $row->AreaName : '—' }}
                            </td>
                            @endif
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                @if ($isKill)
                                    <span class="inline-flex items-center bg-rose-500/15 px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider text-rose-400">
                                        {{ __('history.status_killed') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center bg-emerald-500/15 px-2.5 py-0.5 text-xs font-mono font-bold uppercase tracking-wider text-emerald-400">
                                        {{ __('history.status_spawned') }}
                                    </span>
                                @endif
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
