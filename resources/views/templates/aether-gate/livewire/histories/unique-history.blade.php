<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="ag-text-muted text-sm">{{ __('history.unique_subtitle') }}</p>

        @if ($available)
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" wire:click="toggleSpawns" @checked($showSpawns)
                    style="accent-color: var(--ag-primary);">
                <span class="ag-text-muted text-sm">{{ __('history.show_spawns') }}</span>
            </label>
        @endif
    </div>

    @if (!$available)
        <div class="text-center py-16 ag-card">
            <p class="ag-text-muted text-sm">{{ __('history.unique_unavailable') }}</p>
        </div>
    @elseif ($rows->count() > 0)
        <div class="ag-card overflow-hidden">
            <table class="ag-table">
                <thead>
                    <tr>
                        <th>{{ __('history.col_unique') }}</th>
                        <th>{{ __('history.col_time') }}</th>
                        <th>{{ __('history.col_killer') }}</th>
                        <th>{{ __('history.col_area') }}</th>
                        <th>{{ __('history.col_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        @php
                            $uniqueName = $uniques[$row->Value]['name'] ?? $row->Value;
                            $isKill = $row->ValueCodeName128 === 'KILL_UNIQUE_MONSTER';
                        @endphp
                        <tr>
                            <td>
                                <span class="inline-flex items-center gap-2 font-semibold ag-text-surface">
                                    <span class="inline-flex items-center justify-center w-6 h-6" style="color: var(--ag-primary); background: var(--ag-primary-glow);">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 1c-3.87 0-7 3.02-7 6.75 0 1.98.88 3.76 2.28 5v2.5c0 .41.34.75.75.75h.72v1.5c0 .41.34.75.75.75s.75-.34.75-.75v-1.5h1.5v1.5c0 .41.34.75.75.75s.75-.34.75-.75v-1.5h.72c.41 0 .75-.34.75-.75v-2.5A6.73 6.73 0 0017 7.75C17 4.02 13.87 1 10 1zM7.5 9.5A1.25 1.25 0 117.5 7a1.25 1.25 0 010 2.5zm5 0A1.25 1.25 0 1112.5 7a1.25 1.25 0 010 2.5z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    {{ $uniqueName }}
                                </span>
                            </td>
                            <td>
                                <span class="ag-text-muted" title="{{ \Carbon\Carbon::make($row->EventTime)?->toDayDateTimeString() }}">
                                    {{ \Carbon\Carbon::make($row->EventTime)?->diffForHumans() }}
                                </span>
                            </td>
                            <td>
                                @if ($isKill && filled($row->CharName16))
                                    <a href="{{ route('ranking.characters.show', ['idOrSlug' => $row->CharID]) }}"
                                        class="inline-flex items-center gap-2 ag-text-primary hover:underline">
                                        <img src="{{ \App\Enums\CharacterAvatarMapEnum::getAvatarUrl((int) $row->RefObjID, (string) config('silkpanel.version')) }}"
                                            onerror="this.style.display='none'"
                                            class="w-6 h-6 rounded-full object-cover" alt="">
                                        <span class="font-semibold">{{ $row->CharName16 }}</span>
                                    </a>
                                @else
                                    <span class="ag-text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="ag-text-muted">{{ filled($row->AreaName) ? $row->AreaName : '—' }}</span>
                            </td>
                            <td>
                                @if ($isKill)
                                    <span class="ag-badge" style="color: var(--ag-error); background: rgba(248, 113, 113, 0.12);">
                                        {{ __('history.status_killed') }}
                                    </span>
                                @else
                                    <span class="ag-badge" style="color: var(--ag-success); background: rgba(52, 211, 153, 0.12);">
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
        <div class="text-center py-16 ag-card">
            <p class="ag-text-muted text-sm">{{ __('history.no_records') }}</p>
        </div>
    @endif
</div>
